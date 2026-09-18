<?php

declare(strict_types=1);

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service\AgentCommerce;

use Eccube\Entity\Cart;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Order;
use Eccube\Entity\ProductClass;
use Eccube\Repository\CartRepository;
use Eccube\Service\AgentCommerce\AgentCheckoutPurchaseFlowAdapter;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutAddress;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutLineItem;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutRequest;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutErrorCode;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutException;
use Eccube\Tests\EccubeTestCase;

/**
 * Layer 3 (PurchaseFlow 連携) tests for AgentCheckoutPurchaseFlowAdapter.
 *
 * 中立 DTO -> Cart(永続) -> Order -> shopping flow 再計算 -> 結果 の経路を、
 * fixtures を読み込んだ DB 上で検証する。ゲスト購入を基準線とし、税・送料・手数料が
 * 既存 shopping flow で計算されること、ビジネス系結果 (在庫超過) が例外でなく
 * messages[] に反映されること、プロトコル系エラーが例外になることを確認する。
 */
final class AgentCheckoutPurchaseFlowAdapterTest extends EccubeTestCase
{
    private ?AgentCheckoutPurchaseFlowAdapter $adapter = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = self::getContainer()->get(AgentCheckoutPurchaseFlowAdapter::class);
    }

    private function guestAddress(): AgentCheckoutAddress
    {
        return new AgentCheckoutAddress(
            name01: '山田',
            name02: '太郎',
            kana01: 'ヤマダ',
            kana02: 'タロウ',
            postalCode: '5300001',
            prefId: 27, // 大阪府
            addr01: '大阪市北区',
            addr02: '梅田1-1-1',
            email: 'agent-guest@example.com',
            phoneNumber: '0612345678',
        );
    }

    private function createPurchasableProductClass(string $stock = '100'): ProductClass
    {
        $Product = $this->createProduct('エージェント注文テスト商品', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $ProductClass->setStock($stock);
        $ProductClass->setStockUnlimited(false);
        $this->entityManager->flush();

        return $ProductClass;
    }

    public function testBuildOrderComputesTotalsForGuest(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), 2)],
            buyer: $this->guestAddress(),
            currencyCode: 'JPY',
            protocolId: AgentProtocol::ACP,
            agentId: 'agent-xyz',
        );

        $result = $this->adapter->buildOrder($request);
        $Order = $result->order;

        $this->assertFalse($result->hasError(), '在庫十分なゲスト注文ではエラーメッセージは出ない');
        $this->assertSame('acp', $Order->getAgentProtocol()?->getName(), 'agent_protocol マスタが Order に刻まれる');
        $this->assertSame('agent-xyz', $Order->getAgentId(), 'agent_id が Order に刻まれる');
        $this->assertNotInstanceOf(Customer::class, $Order->getCustomer(), 'ゲスト購入では Order.Customer は null');
        $this->assertSame('山田', $Order->getName01(), 'buyer 住所が Order にコピーされる');

        // 明細が DTO 通り (単価 = price02 / 数量 = 2) に構築されていること。
        $productItems = $Order->getProductOrderItems();
        $this->assertCount(1, $productItems, '商品明細は 1 行');
        $this->assertSame(2, (int) $productItems[0]->getQuantity(), '数量が DTO の通り反映される');
        $this->assertSame(0, bccomp((string) $productItems[0]->getPrice(), (string) $ProductClass->getPrice02(), 2), '明細単価は price02');
        // shopping flow が税・送料・手数料込みの支払総額を計算していること。
        $this->assertGreaterThan(0, (int) $Order->getPaymentTotal(), 'shopping flow で支払総額 (税送料込) が計算される');
    }

    public function testReloadKeepsProductItems(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), 1)],
            buyer: $this->guestAddress(),
            protocolId: AgentProtocol::ACP,
        );
        $Order = $this->adapter->buildOrder($request)->order;
        $orderId = $Order->getId();
        $this->assertCount(1, $Order->getProductOrderItems(), 'in-memory: 商品明細 1 行');

        $this->entityManager->flush();
        $this->entityManager->clear();

        $reloaded = $this->entityManager->getRepository(Order::class)->find($orderId);
        $this->assertInstanceOf(Order::class, $reloaded);
        $this->assertCount(1, $reloaded->getProductOrderItems(), 'reload 後: 商品明細 1 行');
        $this->assertNotEmpty($reloaded->getSaleTypes(), 'reload 後: 販売種別が取れる');
    }

    public function testPrepareThenCommitFinalizesOrder(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), 1)],
            buyer: $this->guestAddress(),
            protocolId: AgentProtocol::ACP,
        );

        $Order = $this->adapter->buildOrder($request)->order;

        // PurchaseFlow の悲観ロック (StockReduceProcessor) のためトランザクションを張る。
        // 通常購入の ShoppingController と同様、トランザクション境界は呼び出し側 (決済
        // オーケストレーション層) が管理し、Adapter 自身は開始しない。
        $this->entityManager->beginTransaction();
        try {
            // prepare = 在庫引当・受注番号採番 (決済オーソリの「前」, PaymentMethod::apply 相当)。
            $prepareResult = $this->adapter->prepare($Order);
            $this->assertFalse($prepareResult->hasError(), 'prepare でエラーが出ない');
            $this->assertNotNull($Order->getOrderNo(), 'prepare で受注番号が採番される');

            // commit = 確定 (決済オーソリ成功後, PaymentMethod::checkout 成功時相当)。
            $this->adapter->commit($Order);
            $this->entityManager->commit();
        } catch (\Throwable $e) {
            $this->entityManager->rollback();

            throw $e;
        }

        $this->assertNotNull($Order->getOrderNo(), 'commit 後も受注番号が保持される');
    }

    public function testRollbackRestoresStockAfterPrepare(): void
    {
        $ProductClass = $this->createPurchasableProductClass('10');
        $ProductStock = $ProductClass->getProductStock();
        $initialStock = (int) $ProductStock->getStock();

        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), 3)],
            buyer: $this->guestAddress(),
            protocolId: AgentProtocol::ACP,
        );

        $Order = $this->adapter->buildOrder($request)->order;

        $this->entityManager->beginTransaction();
        try {
            // prepare で在庫を 3 引き当て、決済失敗を想定して rollback で戻す。
            $this->adapter->prepare($Order);
            $this->adapter->rollback($Order);
            $this->entityManager->commit();
        } catch (\Throwable $e) {
            $this->entityManager->rollback();

            throw $e;
        }

        $this->entityManager->refresh($ProductStock);
        $this->assertSame($initialStock, (int) $ProductStock->getStock(), 'rollback で在庫が prepare 前に戻る (決済失敗時)');
    }

    public function testAgentCartIsIsolatedFromWebStorefront(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), 1)],
            buyer: $this->guestAddress(),
            protocolId: AgentProtocol::ACP,
        );

        $this->adapter->buildOrder($request);
        // buildOrder が生成した agent_owned カートを cartRepository から特定する。
        $cartRepository = self::getContainer()->get(CartRepository::class);

        $agentCarts = $cartRepository->findBy(['agent_owned' => true]);
        $this->assertNotEmpty($agentCarts, 'エージェント生成カートは agent_owned=true で保存される');
        foreach ($agentCarts as $agentCart) {
            $this->assertTrue($agentCart->isAgentOwned(), 'agent_owned フラグが立つ');
            $this->assertNull($agentCart->getCustomer(), 'エージェントカートの customer_id は常に NULL (会員帰属は Order 側)');
        }

        // CartService が Web カート解決に用いる検索条件 (agent_owned=false) では拾われないこと。
        $webVisible = $cartRepository->findBy(['agent_owned' => false]);
        $webVisibleIds = array_map(static fn (Cart $c): ?int => $c->getId(), $webVisible);
        foreach ($agentCarts as $agentCart) {
            $this->assertNotContains($agentCart->getId(), $webVisibleIds, 'エージェントカートは Web 解決条件 (agent_owned=false) に含まれない');
        }
    }

    public function testOverStockSurfacesAsMessageNotException(): void
    {
        $ProductClass = $this->createPurchasableProductClass('1');

        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), 5)],
            buyer: $this->guestAddress(),
            protocolId: AgentProtocol::ACP,
        );

        // 在庫超過は例外でなく messages[] (ビジネス系) として返る。
        $result = $this->adapter->buildOrder($request);

        $this->assertNotEmpty($result->messages, '在庫超過は messages[] に反映される (HTTP 200 + messages[] 系統)');
    }

    public function testEmptyLineItemsThrows(): void
    {
        $request = new AgentCheckoutRequest(lineItems: [], buyer: $this->guestAddress());

        try {
            $this->adapter->buildOrder($request);
            self::fail('空明細は AgentCheckoutException を投げる');
        } catch (AgentCheckoutException $e) {
            $this->assertSame(AgentCheckoutErrorCode::EMPTY_LINE_ITEMS, $e->getErrorCode());
        }
    }

    public function testUnknownProductThrows(): void
    {
        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem(999999999, 1)],
            buyer: $this->guestAddress(),
        );

        try {
            $this->adapter->buildOrder($request);
            self::fail('未知の商品参照は AgentCheckoutException を投げる');
        } catch (AgentCheckoutException $e) {
            $this->assertSame(AgentCheckoutErrorCode::PRODUCT_NOT_FOUND, $e->getErrorCode());
        }
    }

    public function testGuestWithoutAddressThrows(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), 1)],
        );

        try {
            $this->adapter->buildOrder($request);
            self::fail('ゲストで住所が無い場合は AgentCheckoutException を投げる');
        } catch (AgentCheckoutException $e) {
            $this->assertSame(AgentCheckoutErrorCode::MISSING_ADDRESS, $e->getErrorCode());
        }
    }
}

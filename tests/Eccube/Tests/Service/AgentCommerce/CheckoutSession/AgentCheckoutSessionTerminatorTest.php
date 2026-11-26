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

namespace Eccube\Tests\Service\AgentCommerce\CheckoutSession;

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Entity\Order;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Repository\CheckoutSessionRepository;
use Eccube\Repository\Master\CheckoutSessionStatusRepository;
use Eccube\Service\AgentCommerce\AgentCheckoutPurchaseFlowAdapter;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutAddress;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutLineItem;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutRequest;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutSessionLocker;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutSessionTerminator;
use Eccube\Service\AgentCommerce\CheckoutSession\GuestCustomerResolver;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\CancellablePaymentHandlerInterface;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcome;
use Eccube\Tests\EccubeTestCase;
use Psr\Log\NullLogger;

/**
 * 中断中 (在庫引当済み) のセッションを終端へ遷移させたとき、在庫と PSP の取引が解放されることを検証する.
 */
final class AgentCheckoutSessionTerminatorTest extends EccubeTestCase
{
    private ?AgentCheckoutPurchaseFlowAdapter $adapter = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = self::getContainer()->get(AgentCheckoutPurchaseFlowAdapter::class);
    }

    public function testCancelHeldSessionReleasesStockAndCancelsTransaction(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $stock = $ProductClass->getProductStock();
        $session = $this->createHeldSession($ProductClass, 2);
        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(98, (int) $stock->getStock(), '前提: 中断中は在庫を引き当てている');

        $handler = $this->cancellableHandler();
        $terminator = $this->terminator($handler);
        $this->locker()->transactional($session, fn (CheckoutSession $locked) => $terminator->cancel($locked));

        $this->assertSame(CheckoutSessionStatus::CANCELED, $session->getStatus()?->getId());
        $this->entityManager->refresh($stock);
        $this->assertSame(100, (int) $stock->getStock(), 'cancel で在庫が戻る');
        $this->assertSame([['transaction_id' => 'pi_held']], $handler->cancelled, '保持した PSP 参照で取引を取り消す');
    }

    public function testExpireOverdueReleasesOnlyOverdueHeldSessions(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $stock = $ProductClass->getProductStock();
        $overdue = $this->createHeldSession($ProductClass, 1);
        $overdue->setExpiresAt((new \DateTime())->modify('-1 minutes'));
        $fresh = $this->createHeldSession($ProductClass, 1);
        $fresh->setExpiresAt((new \DateTime())->modify('+10 minutes'));
        $this->entityManager->flush();
        $this->entityManager->refresh($stock);
        $this->assertInstanceOf(ProductStock::class, $stock);
        $this->assertSame(98, (int) $stock->getStock());

        $count = $this->terminator($this->cancellableHandler())->expireOverdue(new \DateTime(), 10);

        $this->assertGreaterThanOrEqual(1, $count);
        $this->assertSame(CheckoutSessionStatus::EXPIRED, $overdue->getStatus()?->getId(), '期限切れは expired');
        $this->assertSame(CheckoutSessionStatus::REQUIRES_ACTION, $fresh->getStatus()?->getId(), '期限内は据え置き');
        $this->entityManager->refresh($stock);
        $this->assertSame(99, (int) $stock->getStock(), '期限切れの分だけ在庫が戻る');
    }

    /**
     * prepare で在庫を引き当て、追加認証待ち (requires_action) で中断したセッションを作る.
     */
    private function createHeldSession(ProductClass $ProductClass, int $quantity): CheckoutSession
    {
        $request = new AgentCheckoutRequest(
            lineItems: [new AgentCheckoutLineItem((int) $ProductClass->getId(), $quantity)],
            buyer: new AgentCheckoutAddress(
                name01: '山田',
                name02: '太郎',
                postalCode: '5300001',
                prefId: 27,
                addr01: '大阪市北区',
                addr02: '梅田1-1-1',
                email: 'agent-terminator@example.com',
                phoneNumber: '0612345678',
            ),
            protocolId: AgentProtocol::UCP,
        );
        $order = $this->adapter->buildOrder($request)->order;
        $this->assertInstanceOf(Order::class, $order);

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();
        $this->adapter->prepare($order);
        $connection->commit();

        $session = (new CheckoutSession())
            ->setSessionId('cs_'.bin2hex(random_bytes(8)))
            ->setOrder($order)
            ->setCurrencyCode('JPY')
            ->setPaymentData(['transaction_id' => 'pi_held'])
            ->setExpiresAt((new \DateTime())->modify('+15 minutes'))
            ->setStatus($this->statusMaster(CheckoutSessionStatus::REQUIRES_ACTION));
        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $session;
    }

    private function terminator(CancellablePaymentHandlerInterface $handler): AgentCheckoutSessionTerminator
    {
        /** @var CheckoutSessionStatusRepository $statusRepository */
        $statusRepository = $this->entityManager->getRepository(CheckoutSessionStatus::class);
        /** @var CheckoutSessionRepository $sessionRepository */
        $sessionRepository = $this->entityManager->getRepository(CheckoutSession::class);

        return new AgentCheckoutSessionTerminator(
            $this->adapter,
            new AgentCheckoutPaymentHandlerRegistry([$handler]),
            $statusRepository,
            $sessionRepository,
            self::getContainer()->get(GuestCustomerResolver::class),
            $this->locker(),
            new NullLogger(),
        );
    }

    private function locker(): AgentCheckoutSessionLocker
    {
        return new AgentCheckoutSessionLocker($this->entityManager);
    }

    private function cancellableHandler(): CancellablePaymentHandlerInterface
    {
        return new class implements CancellablePaymentHandlerInterface {
            /** @var list<array<string, mixed>> cancel() が受け取った PSP 参照 */
            public array $cancelled = [];

            public function cancel(Order $order, array $paymentReference): void
            {
                $this->cancelled[] = $paymentReference;
            }

            public function getHandlerId(): string
            {
                return 'test_cancellable';
            }

            public function authorize(Order $order, array $paymentData, array $paymentReference = []): PaymentOutcome
            {
                return PaymentOutcome::requiresAction([]);
            }

            public function capture(Order $order, array $paymentData, PaymentOutcome $authorization): PaymentOutcome
            {
                return PaymentOutcome::completed('cap');
            }

            public function supports(Order $order): bool
            {
                return true;
            }
        };
    }

    private function statusMaster(int $id): CheckoutSessionStatus
    {
        $status = $this->entityManager->getRepository(CheckoutSessionStatus::class)->find($id);
        $this->assertInstanceOf(CheckoutSessionStatus::class, $status);

        return $status;
    }

    private function createPurchasableProductClass(string $stock): ProductClass
    {
        $Product = $this->createProduct('エージェント終了処理テスト商品', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $ProductClass->setStock($stock);
        $ProductClass->setStockUnlimited(false);
        $ProductClass->getProductStock()->setStock($stock);
        $this->entityManager->flush();

        $this->assertInstanceOf(ProductStock::class, $ProductClass->getProductStock());

        return $ProductClass;
    }
}

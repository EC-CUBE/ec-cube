<?php

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

namespace Eccube\Service\AgentCommerce;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Repository\Master\AgentProtocolRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutAddress;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessage;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutRequest;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutResult;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutErrorCode;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutException;
use Eccube\Service\OrderHelper;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;

/**
 * エージェントチェックアウトの中立 DTO を、EC-CUBE の `Cart` → `Order` へ変換し、
 * 既存の shopping flow (`eccube.purchase.flow.shopping`) で税・送料・在庫を再計算するアダプタ.
 *
 * 設計方針 (#6777):
 * - **新規の購入フローは定義せず、通常購入と同一の shopping flow を再利用**する。
 *   これにより税計算・送料・代引手数料・ポイント・在庫引当・受注番号採番のロジックを完全共有する。
 * - 明細は `Cart`/`CartItem` を再利用 (エージェントはセッションを持たないため `CheckoutSession` が束ねる)。
 * - 会員はゲスト購入を基準線とする ({@link \Eccube\Service\AgentCommerce\CheckoutSession\CustomerResolverInterface})。
 *   会員が解決された場合は `Order` に紐づけ、ゲストは住所 DTO から transient な `Customer` を構築する。
 *
 * ビジネス系の結果 (在庫不足・販売停止・配送制限等) は shopping flow の warning/error を
 * {@link AgentCheckoutMessage} へ写し取り、HTTP 200 + messages[] として返せる形にする。
 */
class AgentCheckoutPurchaseFlowAdapter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderHelper $orderHelper,
        private readonly ProductClassRepository $productClassRepository,
        private readonly PrefRepository $prefRepository,
        private readonly AgentProtocolRepository $agentProtocolRepository,
        private readonly PurchaseFlow $shoppingPurchaseFlow,
    ) {
    }

    /**
     * 中立 DTO から `Cart`/`Order` を構築し、shopping flow で見積を計算する.
     *
     * 在庫不足等のビジネス系エラーは例外でなく {@link AgentCheckoutResult::$messages} に反映する。
     * 不正な商品参照・空明細等のプロトコル系エラーは {@link AgentCheckoutException} を投げる。
     */
    public function buildOrder(AgentCheckoutRequest $request, ?Customer $member = null): AgentCheckoutResult
    {
        if ($request->lineItems === []) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::EMPTY_LINE_ITEMS, 'line items must not be empty');
        }

        $Customer = $member ?? $this->buildGuestCustomer($request->buyer);

        $Cart = $this->buildCart($request);

        $Order = $this->orderHelper->createPurchaseProcessingOrder($Cart, $Customer);
        $Cart->setPreOrderId($Order->getPreOrderId());

        if ($request->protocolId !== null) {
            $Order->setAgentProtocol($this->agentProtocolRepository->find($request->protocolId));
        }
        if ($request->agentId !== null) {
            $Order->setAgentId($request->agentId);
        }

        $this->entityManager->flush();

        $messages = $this->runFlow(fn (PurchaseContext $context) => $this->shoppingPurchaseFlow->validate($Order, $context), $Order, $member);
        $this->entityManager->flush();

        return new AgentCheckoutResult($Order, $messages);
    }

    /**
     * 注文を確定する (在庫引当・受注番号採番・ポイント付与等).
     *
     * shopping flow の prepare → commit を実行する。ビジネス系エラーは messages[] に反映する。
     */
    public function complete(Order $Order, ?Customer $member = null): AgentCheckoutResult
    {
        $connection = $this->entityManager->getConnection();

        $messages = $this->runFlow(function (PurchaseContext $context) use ($Order, $connection): PurchaseFlowResult {
            $result = $this->shoppingPurchaseFlow->validate($Order, $context);
            if ($result->hasError()) {
                return $result;
            }

            // PurchaseFlow::prepare()/commit() 内の StockReduceProcessor が
            // EntityManager::lock() (悲観ロック) を使うためトランザクションが必須。
            // 通常購入 (ShoppingController) と同様に、未開始なら自前で開始・コミットする。
            $startedTransaction = false;
            if (!$connection->isTransactionActive()) {
                $this->entityManager->beginTransaction();
                $startedTransaction = true;
            }

            try {
                $this->shoppingPurchaseFlow->prepare($Order, $context);
                $this->shoppingPurchaseFlow->commit($Order, $context);
                $this->entityManager->flush();

                if ($startedTransaction) {
                    $this->entityManager->commit();
                }
            } catch (\Throwable $e) {
                if ($startedTransaction) {
                    $this->entityManager->rollback();
                }

                throw $e;
            }

            return $result;
        }, $Order, $member);

        $this->entityManager->flush();

        return new AgentCheckoutResult($Order, $messages);
    }

    /**
     * PurchaseFlow を実行し、warning/error を中立メッセージへ写し取る.
     *
     * @param callable(PurchaseContext): PurchaseFlowResult $flow
     *
     * @return array<int, AgentCheckoutMessage>
     */
    private function runFlow(callable $flow, Order $Order, ?Customer $member): array
    {
        $context = new PurchaseContext(clone $Order, $member);
        $result = $flow($context);

        $messages = [];
        foreach ($result->getErrors() as $error) {
            $messages[] = new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, (string) $error->getMessage());
        }
        foreach ($result->getWarning() as $warning) {
            $messages[] = new AgentCheckoutMessage(AgentCheckoutMessageLevel::WARNING, (string) $warning->getMessage());
        }

        return $messages;
    }

    /**
     * 中立明細から永続化された `Cart` を構築する.
     */
    private function buildCart(AgentCheckoutRequest $request): Cart
    {
        $Cart = new Cart();
        // 合計は Order 側の shopping flow で再計算されるため、ここでは NOT NULL 制約を満たす初期値のみ設定する。
        $Cart->setTotalPrice('0');
        $Cart->setDeliveryFeeTotal('0');
        // エージェント所有カートは Web ストアフロント (CartService) の解決対象から除外する。
        // 会員帰属は Order 側 (OrderHelper) に持たせ、Cart の customer_id は常に NULL に保つ
        // ことで、ログイン会員の Web カートに混入・操作されないようにする。
        $Cart->setAgentOwned(true);

        foreach ($request->lineItems as $lineItem) {
            if ($lineItem->quantity < 1) {
                throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_QUANTITY, 'quantity must be positive');
            }

            $ProductClass = $this->productClassRepository->find($lineItem->productClassId);
            if ($ProductClass === null) {
                throw new AgentCheckoutException(AgentCheckoutErrorCode::PRODUCT_NOT_FOUND, sprintf('ProductClass #%d not found', $lineItem->productClassId));
            }

            $CartItem = new CartItem();
            $CartItem
                ->setQuantity((string) $lineItem->quantity)
                ->setPrice((string) $ProductClass->getPrice02IncTax())
                ->setProductClass($ProductClass);
            $Cart->addCartItem($CartItem);
            $CartItem->setCart($Cart);
        }

        $this->entityManager->persist($Cart);

        return $Cart;
    }

    /**
     * 住所 DTO から transient (非永続) な会員オブジェクトを構築する (ゲスト購入用).
     *
     * `OrderHelper::createPurchaseProcessingOrder()` は `Customer` のプロパティを `Order`/`Shipping`
     * へコピーするため、id を持たない transient な `Customer` を渡すことでゲストの住所を反映できる。
     */
    private function buildGuestCustomer(?AgentCheckoutAddress $address): Customer
    {
        if ($address === null) {
            throw new AgentCheckoutException(AgentCheckoutErrorCode::MISSING_ADDRESS, 'buyer address is required for guest checkout');
        }

        $Customer = new Customer();
        $Customer
            ->setName01((string) $address->name01)
            ->setName02((string) $address->name02)
            // kana は UCP 等カナを持たないプロトコルでは null になり得る。OrderHelper が
            // Shipping へコピーする際に Shipping::setKana01() が非 null string を要求するため、
            // null は空文字へ正規化する (カナ提供時はその値を使う)。
            ->setKana01($address->kana01 ?? '')
            ->setKana02($address->kana02 ?? '')
            ->setCompanyName($address->companyName)
            ->setEmail($address->email)
            ->setPhonenumber($address->phoneNumber)
            ->setPostalcode($address->postalCode)
            ->setAddr01($address->addr01)
            ->setAddr02($address->addr02);

        if ($address->prefId !== null) {
            $Pref = $this->prefRepository->find($address->prefId);
            if ($Pref === null) {
                throw new AgentCheckoutException(AgentCheckoutErrorCode::INVALID_PREF, sprintf('Pref #%d not found', $address->prefId));
            }
            $Customer->setPref($Pref);
        }

        return $Customer;
    }
}

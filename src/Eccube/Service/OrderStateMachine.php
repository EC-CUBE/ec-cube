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

namespace Eccube\Service;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\PurchaseFlow\Processor\PointProcessor;
use Eccube\Service\PurchaseFlow\Processor\StockReduceProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderStateMachine implements EventSubscriberInterface
{
    public function __construct(
        private readonly WorkflowInterface $_orderStateMachine,
        private readonly OrderStatusRepository $orderStatusRepository,
        private readonly PointProcessor $pointProcessor,
        private readonly StockReduceProcessor $stockReduceProcessor,
    ) {
    }

    /**
     * 指定ステータスに遷移.
     *
     * @param Order $Order 受注
     * @param OrderStatus $OrderStatus 遷移先ステータス
     */
    public function apply(Order $Order, OrderStatus $OrderStatus): void
    {
        $context = $this->newContext($Order);
        $transition = $this->getTransition($context, $OrderStatus);
        if ($transition) {
            $this->_orderStateMachine->apply($context, $transition->getName());
        } else {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * 指定ステータスに遷移できるかどうかを判定.
     *
     * @param Order $Order 受注
     * @param OrderStatus $OrderStatus 遷移先ステータス
     *
     * @return bool 指定ステータスに遷移できる場合はtrue
     */
    public function can(Order $Order, OrderStatus $OrderStatus): bool
    {
        // OrderStatusが設定されていない場合は遷移不可
        if (!$Order->getOrderStatus()) {
            return false;
        }

        return !is_null($this->getTransition($this->newContext($Order), $OrderStatus));
    }

    private function getTransition(OrderStateMachineContext $context, OrderStatus $OrderStatus): ?Transition
    {
        $transitions = $this->_orderStateMachine->getEnabledTransitions($context);
        foreach ($transitions as $t) {
            if (in_array($OrderStatus->getId(), $t->getTos())) {
                return $t;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.order.completed' => ['onCompleted'],
            'workflow.order.transition.pay' => ['updatePaymentDate'],
            'workflow.order.transition.cancel' => [['rollbackStock'], ['rollbackUsePoint']],
            'workflow.order.transition.back_to_in_progress' => [['commitStock'], ['commitUsePoint']],
            'workflow.order.transition.ship' => [['commitAddPoint']],
            'workflow.order.transition.return' => [['rollbackUsePoint'], ['rollbackAddPoint']],
            'workflow.order.transition.cancel_return' => [['commitUsePoint'], ['commitAddPoint']],
        ];
    }

    /*
     * Event handlers.
     */
    /**
     * 入金日を更新する.
     */
    public function updatePaymentDate(Event $event): void
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $Order->setPaymentDate(new \DateTime());
    }

    /**
     * 会員の保有ポイントを減らす.
     *
     * @throws PurchaseFlow\PurchaseException
     */
    public function commitUsePoint(Event $event): void
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->pointProcessor->prepare($Order, new PurchaseContext());
    }

    /**
     * 利用ポイントを会員に戻す.
     */
    public function rollbackUsePoint(Event $event): void
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->pointProcessor->rollback($Order, new PurchaseContext());
    }

    /**
     * 在庫を減らす.
     *
     * @throws PurchaseFlow\PurchaseException
     */
    public function commitStock(Event $event): void
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->stockReduceProcessor->prepare($Order, new PurchaseContext());
    }

    /**
     * 在庫を戻す.
     */
    public function rollbackStock(Event $event): void
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->stockReduceProcessor->rollback($Order, new PurchaseContext());
    }

    /**
     * 会員に加算ポイントを付与する.
     */
    public function commitAddPoint(Event $event): void
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $Customer = $Order->getCustomer();
        if ($Customer) {
            $Customer->setPoint(intval($Customer->getPoint()) + intval($Order->getAddPoint()));
        }
    }

    /**
     * 会員に付与した加算ポイントを取り消す.
     */
    public function rollbackAddPoint(Event $event): void
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $Customer = $Order->getCustomer();
        if ($Customer) {
            $Customer->setPoint(intval($Customer->getPoint()) - intval($Order->getAddPoint()));
        }
    }

    /**
     * 受注ステータスを再設定.
     * {@link StateMachine}によって遷移が終了したときには{@link Order#OrderStatus}のidが変更されるだけなのでOrderStatusを設定し直す.
     */
    public function onCompleted(Event $event): void
    {
        /** @var OrderStateMachineContext $context */
        $context = $event->getSubject();
        $Order = $context->getOrder();
        $CompletedOrderStatus = $this->orderStatusRepository->find($context->getStatus());
        $Order->setOrderStatus($CompletedOrderStatus);
    }

    private function newContext(Order $Order): OrderStateMachineContext
    {
        $orderStatus = $Order->getOrderStatus();
        $statusId = $orderStatus ? (string) $orderStatus->getId() : '';

        return new OrderStateMachineContext($statusId, $Order);
    }
}

class OrderStateMachineContext
{
    /**
     * OrderStateMachineContext constructor.
     */
    public function __construct(private string $status, private readonly Order $Order)
    {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getOrder(): Order
    {
        return $this->Order;
    }

    // order_state_machine.php の marking_store => property は、デフォルト値である marking を使用するよう強く推奨されている.
    // EC-CUBE4.1 までは status を指定していたが、 Symfony5 よりエラーになるためエイリアスを作成して対応する.

    /**
     * Alias of getStatus()
     */
    public function getMarking(): string
    {
        return $this->getStatus();
    }

    /**
     * Alias of setStatus()
     */
    public function setMarking(string $status): void
    {
        $this->setStatus($status);
    }
}

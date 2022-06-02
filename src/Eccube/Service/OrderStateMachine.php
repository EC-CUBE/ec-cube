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
use Symfony\Component\Workflow\StateMachine;

class OrderStateMachine implements EventSubscriberInterface
{
    /**
     * @var StateMachine
     */
    private $machine;

    /**
     * @var OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * @var PointProcessor
     */
    private $pointProcessor;
    /**
     * @var StockReduceProcessor
     */
    private $stockReduceProcessor;

    public function __construct(StateMachine $_orderStateMachine, OrderStatusRepository $orderStatusRepository, PointProcessor $pointProcessor, StockReduceProcessor $stockReduceProcessor)
    {
        $this->machine = $_orderStateMachine;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->pointProcessor = $pointProcessor;
        $this->stockReduceProcessor = $stockReduceProcessor;
    }

    /**
     * 指定ステータスに遷移.
     *
     * @param Order $Order 受注
     * @param OrderStatus $OrderStatus 遷移先ステータス
     */
    public function apply(Order $Order, OrderStatus $OrderStatus)
    {
        $context = $this->newContext($Order);
        $transition = $this->getTransition($context, $OrderStatus);
        if ($transition) {
            $this->machine->apply($context, $transition->getName());
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
     * @return boolean 指定ステータスに遷移できる場合はtrue
     */
    public function can(Order $Order, OrderStatus $OrderStatus)
    {
        return !is_null($this->getTransition($this->newContext($Order), $OrderStatus));
    }

    private function getTransition(OrderStateMachineContext $context, OrderStatus $OrderStatus)
    {
        $transitions = $this->machine->getEnabledTransitions($context);
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
    public static function getSubscribedEvents()
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
     *
     * @param Event $event
     */
    public function updatePaymentDate(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $Order->setPaymentDate(new \DateTime());
    }

    /**
     * 会員の保有ポイントを減らす.
     *
     * @param Event $event
     *
     * @throws PurchaseFlow\PurchaseException
     */
    public function commitUsePoint(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->pointProcessor->prepare($Order, new PurchaseContext());
    }

    /**
     * 利用ポイントを会員に戻す.
     *
     * @param Event $event
     */
    public function rollbackUsePoint(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->pointProcessor->rollback($Order, new PurchaseContext());
    }

    /**
     * 在庫を減らす.
     *
     * @param Event $event
     *
     * @throws PurchaseFlow\PurchaseException
     */
    public function commitStock(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->stockReduceProcessor->prepare($Order, new PurchaseContext());
    }

    /**
     * 在庫を戻す.
     *
     * @param Event $event
     */
    public function rollbackStock(Event $event)
    {
        /* @var Order $Order */
        $Order = $event->getSubject()->getOrder();
        $this->stockReduceProcessor->rollback($Order, new PurchaseContext());
    }

    /**
     * 会員に加算ポイントを付与する.
     *
     * @param Event $event
     */
    public function commitAddPoint(Event $event)
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
     *
     * @param Event $event
     */
    public function rollbackAddPoint(Event $event)
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
     *
     * @param Event $event
     */
    public function onCompleted(Event $event)
    {
        /** @var $context OrderStateMachineContext */
        $context = $event->getSubject();
        $Order = $context->getOrder();
        $CompletedOrderStatus = $this->orderStatusRepository->find($context->getStatus());
        $Order->setOrderStatus($CompletedOrderStatus);
    }

    private function newContext(Order $Order)
    {
        return new OrderStateMachineContext((string) $Order->getOrderStatus()->getId(), $Order);
    }
}

class OrderStateMachineContext
{
    /** @var string */
    private $status;

    /** @var Order */
    private $Order;

    /**
     * OrderStateMachineContext constructor.
     *
     * @param string $status
     * @param Order $Order
     */
    public function __construct($status, Order $Order)
    {
        $this->status = $status;
        $this->Order = $Order;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return Order
     */
    public function getOrder()
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

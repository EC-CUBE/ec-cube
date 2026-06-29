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

namespace Eccube\Service\AgentCommerce\Payment;

use Eccube\Entity\Delivery;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Repository\PaymentRepository;

/**
 * {@link AgentPaymentMethodResolverInterface} の標準実装.
 *
 * 注文の配送業者から利用可能な支払方法 ({@link PaymentRepository::findAllowedPayments()}) を取得し、
 * 登録済みのエージェント決済ハンドラが扱えるものへ絞り込んで Payment を決定する。
 * methodClass 等のプロトコル固有判定はハンドラ ({@link AgentCheckoutPaymentHandlerInterface::supports()})
 * 側に委ね、本実装は「どの Payment にエージェント決済ハンドラが付くか」のみで選択するため、
 * 特定の決済クラスに依存しない (将来 PSP が新しい methodClass を出しても無改変で動く)。
 */
class DefaultAgentPaymentMethodResolver implements AgentPaymentMethodResolverInterface
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly AgentCheckoutPaymentHandlerRegistry $handlerRegistry,
    ) {
    }

    public function resolve(Order $order, ?string $handlerId = null): ?Payment
    {
        $candidates = $this->findCandidatePayments($order);
        if ($candidates === []) {
            return null;
        }

        // 元の割当を退避し、候補を仮設定しながらハンドラ適合を判定する。
        // 不適合のまま走査を終えたら元へ戻し、注文を汚さない。
        $original = $order->getPayment();

        // 明示選択: handler_id が示すハンドラが扱える Payment のみを採用する。
        $requestedHandler = $handlerId !== null ? $this->handlerRegistry->resolveByHandlerId($handlerId) : null;
        if ($handlerId !== null && $requestedHandler === null) {
            return null;
        }

        foreach ($candidates as $payment) {
            $this->assign($order, $payment);

            if ($requestedHandler !== null) {
                if ($requestedHandler->supports($order)) {
                    return $payment;
                }

                continue;
            }

            // 既定選択: いずれかのエージェント決済ハンドラが扱える最初の Payment。
            if ($this->handlerRegistry->resolveForOrder($order) !== null) {
                return $payment;
            }
        }

        $this->assign($order, $original);

        return null;
    }

    /**
     * 注文の配送業者に紐づく利用可能な支払方法を取得する.
     *
     * @return list<Payment>
     */
    private function findCandidatePayments(Order $order): array
    {
        $deliveries = [];
        foreach ($order->getShippings() as $shipping) {
            $delivery = $shipping->getDelivery();
            if ($delivery instanceof Delivery && $delivery->getId() !== null) {
                $deliveries[$delivery->getId()] = $delivery;
            }
        }

        if ($deliveries === []) {
            return [];
        }

        /** @var list<Payment> $payments */
        $payments = $this->paymentRepository->findAllowedPayments(array_values($deliveries), true);

        return $payments;
    }

    /**
     * 通常購入 ({@link \Eccube\Service\OrderHelper}) と同じく Payment と表示用の method 名を設定する.
     */
    private function assign(Order $order, ?Payment $payment): void
    {
        $order->setPayment($payment);
        $order->setPaymentMethod($payment?->getMethod());
    }
}

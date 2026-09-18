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

use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Repository\DeliveryRepository;
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
        private readonly DeliveryRepository $deliveryRepository,
        private readonly AgentCheckoutPaymentHandlerRegistry $handlerRegistry,
    ) {
    }

    public function resolve(Order $order, ?string $handlerId = null): ?Payment
    {
        // 明示選択: handler_id が示すハンドラが扱える Payment のみを採用する。
        $requestedHandler = $handlerId !== null ? $this->handlerRegistry->resolveByHandlerId($handlerId) : null;
        if ($handlerId !== null && $requestedHandler === null) {
            return null;
        }

        // 元の割当を退避し、候補を仮設定しながらハンドラ適合を判定する。
        // 不適合のまま走査を終えたら元へ戻し、注文を汚さない。
        $original = $order->getPayment();

        // 既に割り当て済みの Payment が要求ハンドラ (handler_id 指定時) もしくはいずれかのエージェント
        // 決済ハンドラに適合するなら、それを尊重する。complete は create で割当済みの Payment を引き継ぐため、
        // 別リクエストで再読込された注文 (Shipping へ配送業者未割当・販売種別から候補を引けない) でも解決できる。
        if ($original !== null && $this->isSupported($order, $original, $requestedHandler)) {
            return $original;
        }

        foreach ($this->findCandidatePayments($order) as $payment) {
            if ($this->isSupported($order, $payment, $requestedHandler)) {
                return $payment;
            }
        }

        $this->assign($order, $original);

        return null;
    }

    /**
     * 候補 Payment を Order に仮設定し、要求ハンドラ (指定時) もしくはいずれかのエージェント決済
     * ハンドラが扱えるかを判定する.
     */
    private function isSupported(Order $order, Payment $payment, ?AgentCheckoutPaymentHandlerInterface $requestedHandler): bool
    {
        $this->assign($order, $payment);

        if ($requestedHandler !== null) {
            return $requestedHandler->supports($order);
        }

        return $this->handlerRegistry->resolveForOrder($order) !== null;
    }

    /**
     * 注文で利用可能な支払方法を取得する.
     *
     * 通常購入の {@link \Eccube\Service\OrderHelper} と同じく、注文の販売種別に紐づく配送業者から
     * 解決する (エージェント注文は確定前に Shipping へ配送業者が割り当たらないことがあるため、
     * Shipping ではなく販売種別を起点にする)。
     *
     * @return list<Payment>
     */
    private function findCandidatePayments(Order $order): array
    {
        $saleTypes = $order->getSaleTypes();
        if ($saleTypes === []) {
            return [];
        }

        $deliveries = $this->deliveryRepository->getDeliveries($saleTypes);
        if ($deliveries === []) {
            return [];
        }

        /** @var list<Payment> $payments */
        $payments = $this->paymentRepository->findAllowedPayments($deliveries, true);

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

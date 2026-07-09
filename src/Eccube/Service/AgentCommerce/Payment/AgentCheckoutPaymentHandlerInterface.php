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

/**
 * エージェントチェックアウトの決済ハンドラ共通基底.
 *
 * ACP の Shared Payment Token / UCP の Payment Token Exchange 等、プロトコル固有の
 * トークン償還 (redeem) はそれぞれの派生インターフェイス (#6776 ACP / #6574 UCP) が
 * 本基底を継承して定義する。実装は決済プラグイン側に置く (core は型のみ保持)。
 */
interface AgentCheckoutPaymentHandlerInterface
{
    /**
     * このハンドラの識別子 (エージェントの complete リクエストの handler_id と突合する).
     *
     * ACP は payment_data.handler_id (例: "card_tokenized")、UCP は逆ドメイン名形式の
     * payment.instruments[].handler_id (例: "dev.ucp.payment.card") と突合する。
     * {@link AgentCheckoutPaymentHandlerRegistry::resolveByHandlerId()} および
     * {@link AgentPaymentMethodResolverInterface} がエージェントの選んだ handler_id から
     * Payment を解決するために用いる。プラグイン間で一意でなければならない。
     */
    public function getHandlerId(): string;

    /**
     * 与信 (オーソリ) を行う.
     *
     * complete の**状態機械の入口**であり、初回 complete と再開 complete の双方から呼ばれる
     * (再入可能)。$paymentData に authentication_result 等の再開情報が含まれていれば、それを
     * 使って追加認証 (EMV-3DS) を完了させる。追加認証が必要な場合は例外でなく
     * {@link PaymentOutcome} の REQUIRES_ACTION を返す。
     *
     * @param array<string, mixed> $paymentData 支払トークン等の中立表現 (再開時は authentication_result を含む)
     */
    public function authorize(Order $order, array $paymentData): PaymentOutcome;

    /**
     * 売上確定 (キャプチャ) を行う. {@link authorize()} が COMPLETED を返した後にのみ呼ぶ.
     *
     * @param array<string, mixed> $paymentData 支払トークン等の中立表現
     */
    public function capture(Order $order, array $paymentData): PaymentOutcome;

    /**
     * このハンドラが指定の支払方法 (Payment.method_class 等) を扱えるか.
     */
    public function supports(Order $order): bool;
}

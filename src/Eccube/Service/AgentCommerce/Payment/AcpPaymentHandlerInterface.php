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

/**
 * ACP の Shared Payment Token 向け決済ハンドラ.
 *
 * ACP checkout の complete 時、エージェントから渡された `payment_data` には Shared Payment Token
 * (SPT) が含まれる。具体的な償還 (redeem) ロジックは PSP 依存のため決済プラグイン側に実装し
 * (core は型のみ保持)、本体は {@link AgentCheckoutCompletionService} が
 * {@link AgentCheckoutPaymentHandlerRegistry::resolveForOrder()} で解決したハンドラの
 * {@link AgentCheckoutPaymentHandlerInterface::authorize()} を呼ぶ。authorize 内で必要に応じ
 * {@link redeemSharedPaymentToken()} を用いて SPT を PSP の課金可能な参照へ変換する。
 *
 * Delegate Payment API (エージェント ↔ PSP の vault 発行) は EC-CUBE の責務外 (EC-CUBE は merchant で
 * あり PSP ではない)。本体は SPT の**受け手**としてのみ振る舞う。
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6776
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.payment_handlers.md / rfc.delegate_payment.md
 */
interface AcpPaymentHandlerInterface extends AgentCheckoutPaymentHandlerInterface
{
    /**
     * このハンドラの識別子 (ACP の payment_data.handler_id と突合、例: "card_tokenized").
     */
    public function getHandlerId(): string;

    /**
     * Shared Payment Token (SPT) を PSP の課金可能な参照へ償還する.
     *
     * 償還は PSP 側で**ワンショット**なのが通例で、2 度目は失敗する。1 決済につき
     * {@link AgentCheckoutPaymentHandlerInterface::authorize()} の中で 1 度だけ呼び、
     * capture では償還し直さず与信結果 ({@link PaymentOutcome::$transactionId}) を用いること。
     *
     * 本メソッドは authorize の内側からのみ呼ばれるため、支払データを解決できない場合は例外を投げてよい
     * (authorize がそれを {@link PaymentOutcome::failed()} へ写像する)。
     *
     * @param array<string, mixed> $paymentData complete リクエストの payment_data (handler_id/instrument/credential 等)
     *
     * @return array<string, mixed> authorize() が PSP へ渡す中立な支払データ
     */
    public function redeemSharedPaymentToken(array $paymentData): array;
}

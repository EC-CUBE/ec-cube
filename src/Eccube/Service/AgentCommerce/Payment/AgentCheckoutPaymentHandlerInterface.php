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
     * 戻り値のステータスで**続く capture の有無が決まる**。与信のみ成功したときは
     * {@link PaymentOutcome::authorized()}、与信と売上が 1 度で完結する auto-capture 型 PSP は
     * {@link PaymentOutcome::completed()} を返すこと (後者では capture が呼ばれない)。
     *
     * トークン欠落・不正など**支払データを検証できない場合は例外でなく
     * {@link PaymentOutcome::failed()} を返す** (fail-closed)。無検証で成功を返してはならない。
     * PSP 通信の失敗も同様に failed へ写像すること (例外を投げた場合、オーケストレータは最後の砦として
     * failed に変換するが、その時点では PSP 参照を保持できない)。
     *
     * @param array<string, mixed> $paymentData      支払トークン等の中立表現 (再開時は authentication_result を含む)
     * @param array<string, mixed> $paymentReference 中断前の complete で保持した PSP 参照 (`transaction_id` と metadata)。
     *                                               初回 complete では空配列。**エージェントの入力ではなくサーバ側の記録**なので、
     *                                               再開時はこちらの取引を対象にすること (ワンショットトークンの再償還を避ける)
     */
    public function authorize(Order $order, array $paymentData, array $paymentReference = []): PaymentOutcome;

    /**
     * 売上確定 (キャプチャ) を行う. {@link authorize()} が AUTHORIZED を返した後にのみ呼ばれる.
     *
     * 実 PSP の capture は「authorize が発行した取引に対する操作」であり、支払トークンの再償還
     * (ACP の Shared Payment Token 等) は**ワンショットで 2 度目が失敗する**のが通例である。
     * そのため対象取引は $paymentData から導出し直すのではなく、$authorization が持つ
     * {@link PaymentOutcome::$transactionId} / {@link PaymentOutcome::$metadata} を用いること。
     *
     * authorize と同様、PSP 通信の失敗は例外でなく {@link PaymentOutcome::failed()} で返し、
     * **与信済みの取引識別子を保持する** (与信は PSP 側に残るため、取消・再 capture の照会に要る)。
     *
     * @param array<string, mixed> $paymentData   authorize と同じ支払データ (冪等キー等の参照用)
     * @param PaymentOutcome       $authorization {@link authorize()} が返した与信結果 (transactionId・metadata を含む)
     */
    public function capture(Order $order, array $paymentData, PaymentOutcome $authorization): PaymentOutcome;

    /**
     * このハンドラが指定の支払方法 (Payment.method_class 等) を扱えるか.
     */
    public function supports(Order $order): bool;
}

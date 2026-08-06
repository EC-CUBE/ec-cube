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
 * 決済ハンドラ (authorize/capture) の処理結果 (プロトコル非依存の中立 DTO).
 *
 * EC-CUBE 通常購入の {@link \Eccube\Service\Payment\PaymentResult} / {@link \Eccube\Service\Payment\PaymentDispatcher}
 * が成否と外部遷移を表現するのと同じ役割を、エージェント向けに Symfony Response 非依存の形で持つ
 * (エージェントは Response を解釈しないため、追加対応に必要なデータは {@link $actionData} に中立な形で載せる)。
 */
final readonly class PaymentOutcome
{
    /**
     * @param array<string, mixed> $actionData REQUIRES_ACTION 時の中立データ。プロトコル層が
     *                                          continue_url (UCP) / authentication_metadata (ACP) へ写し取る
     * @param array<string, mixed> $metadata   payment_data へ保持する PSP 参照等 (token はマスキング済)
     */
    public function __construct(
        public PaymentOutcomeStatus $status,
        public ?string $transactionId = null,
        public array $actionData = [],
        public array $metadata = [],
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
        public bool $retryable = true,
    ) {
    }

    /**
     * 与信のみ成功 (売上確定は未実施). オーケストレータが続けて capture を呼ぶ.
     *
     * @param string|null          $transactionId PSP の取引識別子。capture へそのまま引き渡される
     * @param array<string, mixed> $metadata
     */
    public static function authorized(?string $transactionId = null, array $metadata = []): self
    {
        return new self(PaymentOutcomeStatus::AUTHORIZED, $transactionId, [], $metadata);
    }

    /**
     * 売上確定済 (capture 済、または capture 不要な auto-capture 型 PSP).
     *
     * authorize がこれを返すと**オーケストレータは capture を呼ばない**。与信のみの場合は
     * {@link self::authorized()} を使うこと。
     *
     * @param array<string, mixed> $metadata
     */
    public static function completed(?string $transactionId = null, array $metadata = []): self
    {
        return new self(PaymentOutcomeStatus::COMPLETED, $transactionId, [], $metadata);
    }

    /**
     * @param array<string, mixed> $actionData    continue_url / authentication_metadata の原資
     * @param array<string, mixed> $metadata
     * @param string|null          $transactionId 中断時点の PSP 取引識別子 (再開時に参照するため payment_data へ保持される)
     */
    public static function requiresAction(array $actionData, array $metadata = [], ?string $transactionId = null): self
    {
        return new self(PaymentOutcomeStatus::REQUIRES_ACTION, $transactionId, $actionData, $metadata);
    }

    /**
     * @param array<string, mixed> $metadata
     * @param string|null          $transactionId 非同期確定を待つ PSP 取引識別子 (IPN/Webhook との突合に用いる)
     */
    public static function pending(array $metadata = [], ?string $transactionId = null): self
    {
        return new self(PaymentOutcomeStatus::PENDING, $transactionId, [], $metadata);
    }

    /**
     * @param bool                 $retryable     再試行可能か (card declined 等は true=セッションを ready に戻す /
     *                                            fraud block 等の不可逆失敗は false=セッションを canceled にする)
     * @param string|null          $transactionId 失敗した取引の PSP 識別子 (問い合わせ・追跡のため保持する)
     * @param array<string, mixed> $metadata
     */
    public static function failed(string $errorCode, string $errorMessage = '', bool $retryable = true, ?string $transactionId = null, array $metadata = []): self
    {
        return new self(PaymentOutcomeStatus::FAILED, $transactionId, [], $metadata, $errorCode, $errorMessage, $retryable);
    }

    /**
     * 決済が失敗せず前進したか (与信済 or 売上確定済).
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, [PaymentOutcomeStatus::AUTHORIZED, PaymentOutcomeStatus::COMPLETED], true);
    }

    /**
     * 売上確定 (capture) がこれから必要か.
     */
    public function needsCapture(): bool
    {
        return $this->status === PaymentOutcomeStatus::AUTHORIZED;
    }

    public function needsAction(): bool
    {
        return $this->status === PaymentOutcomeStatus::REQUIRES_ACTION;
    }
}

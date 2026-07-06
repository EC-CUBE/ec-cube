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
     * @param array<string, mixed> $metadata
     */
    public static function completed(?string $transactionId = null, array $metadata = []): self
    {
        return new self(PaymentOutcomeStatus::COMPLETED, $transactionId, [], $metadata);
    }

    /**
     * @param array<string, mixed> $actionData continue_url / authentication_metadata の原資
     * @param array<string, mixed> $metadata
     */
    public static function requiresAction(array $actionData, array $metadata = []): self
    {
        return new self(PaymentOutcomeStatus::REQUIRES_ACTION, null, $actionData, $metadata);
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public static function pending(array $metadata = []): self
    {
        return new self(PaymentOutcomeStatus::PENDING, null, [], $metadata);
    }

    /**
     * @param bool $retryable 再試行可能か (card declined 等は true=セッションを ready に戻す /
     *                         fraud block 等の不可逆失敗は false=セッションを canceled にする)
     */
    public static function failed(string $errorCode, string $errorMessage = '', bool $retryable = true): self
    {
        return new self(PaymentOutcomeStatus::FAILED, null, [], [], $errorCode, $errorMessage, $retryable);
    }

    public function isSuccessful(): bool
    {
        return $this->status === PaymentOutcomeStatus::COMPLETED;
    }

    public function needsAction(): bool
    {
        return $this->status === PaymentOutcomeStatus::REQUIRES_ACTION;
    }
}

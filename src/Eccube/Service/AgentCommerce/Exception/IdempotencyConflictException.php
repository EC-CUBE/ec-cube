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

namespace Eccube\Service\AgentCommerce\Exception;

/**
 * Idempotency-Key の競合.
 *
 * - {@link REASON_CONFLICT}: 同一キーが**異なるリクエスト内容**で再利用された (恒久的な競合)
 * - {@link REASON_IN_FLIGHT}: 同一キーの**処理がまだ進行中** (並行リクエスト・再試行で解消し得る)
 *
 * プロトコル層が `reason` を見て HTTP ステータスへ変換する。ACP は異内容を **422 idempotency_conflict**、
 * 処理中を **409 idempotency_in_flight** に分けて返す (UCP は両者を 409 にまとめてよい)。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §6.4
 */
class IdempotencyConflictException extends \RuntimeException
{
    /** 同一キーが異なるリクエスト内容で再利用された (ACP: 422). */
    public const REASON_CONFLICT = 'conflict';

    /** 同一キーの処理が進行中 (ACP: 409). */
    public const REASON_IN_FLIGHT = 'in_flight';

    public function __construct(
        string $message = '',
        private readonly string $reason = self::REASON_CONFLICT,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * 競合理由 ({@link REASON_CONFLICT} または {@link REASON_IN_FLIGHT}).
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * 処理が進行中 (並行リクエスト) による競合か.
     */
    public function isInFlight(): bool
    {
        return $this->reason === self::REASON_IN_FLIGHT;
    }
}

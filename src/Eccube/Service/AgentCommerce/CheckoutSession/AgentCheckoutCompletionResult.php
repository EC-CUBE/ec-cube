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

namespace Eccube\Service\AgentCommerce\CheckoutSession;

use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Entity\Order;

/**
 * complete 状態機械 ({@link AgentCheckoutCompletionService}) の結果 (プロトコル非依存).
 *
 * プロトコル層 (#6776 ACP / #6574 UCP) のマッパーがこの中立結果を各プロトコルのレスポンスへ写す:
 * - `status` (正規化マスタ) → UCP `requires_escalation` / ACP `authentication_required` 等へ変換
 * - `actionData` → UCP `continue_url` / ACP `authentication_metadata` の原資
 * - `messages` → HTTP 200 + messages[] (ビジネス系エラー)
 */
final readonly class AgentCheckoutCompletionResult
{
    /**
     * @param array<int, AgentCheckoutMessage> $messages  ビジネス系メッセージ (在庫不足・決済拒否等)
     * @param array<string, mixed>             $actionData REQUIRES_ACTION/PENDING 時の中立データ
     */
    public function __construct(
        public CheckoutSessionStatus $status,
        public ?Order $order,
        public array $messages = [],
        public array $actionData = [],
    ) {
    }
}

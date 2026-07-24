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

namespace Eccube\Service\AgentCommerce\Ucp;

use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessage;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;

/**
 * 中立メッセージ ({@link AgentCheckoutMessage}) を UCP の messages[] 表現へ写す.
 *
 * UCP のビジネスロジックエラーは HTTP 200 + messages[] で表現する。各メッセージは
 * `type` (error/warning/info) と、error は `severity` (recoverable/requires_buyer_input/
 * requires_buyer_review/unrecoverable) を持つ。
 *
 * PurchaseFlow 由来のメッセージは自由文のため、標準では `severity: recoverable` (update で
 * 再試行可能) とし、`code` は付与しない (UCP では optional)。`out_of_stock` 等の構造化コード付与は
 * app/Customize での拡張余地とする。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP message_error.json (type, severity, content)
 */
class UcpMessageMapper
{
    /**
     * 中立メッセージ群を UCP messages[] へ変換する.
     *
     * @param array<int, AgentCheckoutMessage> $messages
     *
     * @return list<array<string, mixed>>
     */
    public function toUcpMessages(array $messages): array
    {
        $result = [];
        foreach ($messages as $message) {
            $result[] = $this->toUcpMessage($message);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function toUcpMessage(AgentCheckoutMessage $message): array
    {
        $entry = [
            'type' => $message->level->value,
            'content' => $message->message,
            'content_type' => 'plain',
        ];

        // error のみ severity を付す。PurchaseFlow 由来は update での再入力で解消し得るため recoverable。
        if ($message->level === AgentCheckoutMessageLevel::ERROR) {
            $entry['severity'] = 'recoverable';
        }

        return $entry;
    }

    /**
     * メッセージ群が requires_buyer_input/review 相当のエスカレーションを要するか.
     *
     * 標準では recoverable のみ生成するため常に false。エスカレーション (requires_escalation)
     * 判定の拡張余地として seam を用意する。
     *
     * @param list<array<string, mixed>> $ucpMessages
     */
    public function requiresEscalation(array $ucpMessages): bool
    {
        foreach ($ucpMessages as $message) {
            $severity = $message['severity'] ?? null;
            if ($severity === 'requires_buyer_input' || $severity === 'requires_buyer_review') {
                return true;
            }
        }

        return false;
    }
}

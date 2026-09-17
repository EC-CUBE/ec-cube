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
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageCode;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;

/**
 * 中立メッセージ ({@link AgentCheckoutMessage}) を UCP の messages[] 表現へ写す.
 *
 * UCP のビジネスロジックエラーは HTTP 200 + messages[] で表現する。各メッセージは
 * `type` (error/warning/info) を持ち、error / warning は `code` が**必須**
 * (message_error.json / message_warning.json の required)、error はさらに `severity`
 * (recoverable/requires_buyer_input/requires_buyer_review/unrecoverable) を持つ。
 *
 * `code` は生成元が付けた中立コード ({@link AgentCheckoutMessageCode}) をそのまま出し、
 * 無ければレベルに応じた既定値を補う。PurchaseFlow 由来の自由文メッセージは標準では
 * `severity: recoverable` (update で再試行可能) とする。`out_of_stock` 等のより細かい
 * 標準コードへの写像は app/Customize での拡張余地とする。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/message_error.json#L6 (required: type, code, content, severity)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/message_warning.json#L6 (required: type, code, content)
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

        // error / warning は code が必須 (message_error.json / message_warning.json の required)。
        // 中立コードの値は UCP の freeform code としてそのまま使う (payment_failed は標準コードと一致)。
        // 生成元が code を付けていなければレベルに応じた既定値を補う。
        if ($message->level === AgentCheckoutMessageLevel::ERROR) {
            $entry['code'] = ($message->code ?? AgentCheckoutMessageCode::INVALID)->value;
            // PurchaseFlow 由来は update での再入力で解消し得るため recoverable。
            $entry['severity'] = 'recoverable';
        } elseif ($message->level === AgentCheckoutMessageLevel::WARNING) {
            $entry['code'] = ($message->code ?? AgentCheckoutMessageCode::LIMITED_AVAILABILITY)->value;
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

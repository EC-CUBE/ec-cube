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

namespace Eccube\Service\AgentCommerce\Acp;

use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessage;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;

/**
 * 中立メッセージ ({@link AgentCheckoutMessage}) を ACP の messages[] 表現へ写す.
 *
 * ACP のビジネスロジックエラー (在庫切れ・決済拒否等) は **HTTP 200 + messages[]** で表現する
 * (プロトコル系エラーは 4xx/5xx + flat Error)。各メッセージは type (error/warning/info)、
 * error/warning は `code` (enum) が必須、`content_type` (plain/markdown) と `content` を持つ。
 *
 * PurchaseFlow 由来のメッセージは自由文のため、標準では error を `code: "invalid"` /
 * `resolution: "recoverable"` (update で再試行可能) として写し、content_type は `plain`。
 * `out_of_stock` 等の構造化コード付与は app/Customize の拡張余地とする。
 *
 * content_type が `markdown` の場合、ACP 仕様は CommonMark 0.31.2 準拠かつ **raw HTML 禁止 (MUST)**。
 * 本マッパーは plain のみ生成するが、markdown を注入する Customize 向けに {@link assertNoRawHtml()} を提供する。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout.yaml (MessageError/Warning/Info)
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §5.1 (markdown / raw HTML 禁止)
 */
class AcpMessageMapper
{
    /** raw HTML 要素 (ACP は markdown content での出力を禁止). */
    private const RAW_HTML_PATTERN = '/<\s*\/?\s*(script|div|img|iframe|style|object|embed|link|meta|form|input|svg|a|span|table)\b/i';

    /**
     * 中立メッセージ群を ACP messages[] へ変換する.
     *
     * @param array<int, AgentCheckoutMessage> $messages
     *
     * @return list<array<string, mixed>>
     */
    public function toAcpMessages(array $messages): array
    {
        $result = [];
        foreach ($messages as $message) {
            $result[] = $this->toAcpMessage($message);
        }

        return $result;
    }

    /**
     * メッセージ群にエラーが含まれるか (status を not_ready_for_payment へ落とす判定に使う).
     *
     * @param list<array<string, mixed>> $acpMessages
     */
    public function hasError(array $acpMessages): bool
    {
        foreach ($acpMessages as $message) {
            if (($message['type'] ?? null) === 'error') {
                return true;
            }
        }

        return false;
    }

    /**
     * markdown content に raw HTML が含まれていれば例外を投げる (ACP MUST: 受信 markdown の raw HTML 拒否).
     *
     * @throws \InvalidArgumentException raw HTML 要素を含む場合
     */
    public function assertNoRawHtml(string $markdown): void
    {
        if (preg_match(self::RAW_HTML_PATTERN, $markdown) === 1) {
            throw new \InvalidArgumentException('Markdown content must not contain raw HTML elements.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function toAcpMessage(AgentCheckoutMessage $message): array
    {
        $entry = [
            'type' => $message->level->value,
            'content_type' => 'plain',
            'content' => $message->message,
        ];

        // error/warning は code が必須。PurchaseFlow 由来は構造化できないため汎用の "invalid" を付す。
        // update での再入力 (住所・数量変更等) で解消し得るため resolution は recoverable。
        if ($message->level === AgentCheckoutMessageLevel::ERROR) {
            $entry['code'] = 'invalid';
            $entry['resolution'] = 'recoverable';
        } elseif ($message->level === AgentCheckoutMessageLevel::WARNING) {
            $entry['code'] = 'limited_availability';
        }

        return $entry;
    }
}

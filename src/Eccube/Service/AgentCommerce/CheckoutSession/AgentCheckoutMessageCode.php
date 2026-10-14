<?php

declare(strict_types=1);

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

/**
 * ビジネスロジックの結果メッセージの理由コード (中立表現).
 *
 * ACP の `MessageError.code` / `MessageWarning.code` (固定 enum) と UCP の `messages[].code`
 * (標準コード + freeform) はいずれも error / warning で **必須** だが語彙が異なる。
 * ここでは両者へ写像できる最小の中立語彙を定義し、プロトコル固有の値への変換は
 * 各プロトコルの message mapper が担う。
 *
 * 値は UCP の freeform code としてそのまま使える識別子にしてある (ACP 側は enum へ写像する)。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.agentic_checkout.json MessageError / MessageWarning の code enum
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/error_code.json (freeform 可・標準コードは examples)
 */
enum AgentCheckoutMessageCode: string
{
    /** 入力またはカート内容が検証を通らない (PurchaseFlow の ItemHolder エラー等の総称). */
    case INVALID = 'invalid';

    /** 在庫等の制約により数量が調整された、または一部しか提供できない (PurchaseFlow の警告). */
    case LIMITED_AVAILABILITY = 'limited_availability';

    /** 送料・税の確定に必要な配送先住所が未指定. */
    case ADDRESS_REQUIRED = 'address_required';

    /** 決済の与信/売上またはクレデンシャルの処理に失敗した. */
    case PAYMENT_FAILED = 'payment_failed';
}

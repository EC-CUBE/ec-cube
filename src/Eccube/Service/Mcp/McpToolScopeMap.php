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

namespace Eccube\Service\Mcp;

/**
 * MCP Tool 名 → 必要 scope (role) の **唯一の中央定義**。
 *
 * scope 検査は各 Tool の中ではなく `ScopeEnforcingReferenceHandler` (mcp-bundle が Tool を呼ぶ手前の
 * 1 箇所) で行う。 その層がこのマップを引いて認可する。 Tool 自身は scope を表現しない。
 *
 * **fail-closed**: ここに登録されていない Tool 名は `ScopeEnforcingReferenceHandler` が実行時に
 * deny する。 新規 Tool を追加して本マップへの登録を忘れると、 その Tool は「誰の token でも呼べない」
 * 安全側に倒れる (誤って全公開されることはない)。
 */
final class McpToolScopeMap
{
    /**
     * Tool 名 → 必要 role。 role 文字列は {@see McpScope} の定数に揃える。
     *
     * @var array<string, string>
     */
    public const MAP = [
        // 商品/在庫 (mcp:product:read)
        'search_products' => McpScope::ROLE_PRODUCT_READ,
        'get_product' => McpScope::ROLE_PRODUCT_READ,
        'get_product_stock' => McpScope::ROLE_PRODUCT_READ,
        // 注文 (mcp:order:read)
        'search_orders' => McpScope::ROLE_ORDER_READ,
        'get_order' => McpScope::ROLE_ORDER_READ,
        'get_shipping' => McpScope::ROLE_ORDER_READ,
        // 顧客会員 (mcp:customer:read)
        'search_customers' => McpScope::ROLE_CUSTOMER_READ,
        'get_customer' => McpScope::ROLE_CUSTOMER_READ,
        'get_customer_orders' => McpScope::ROLE_CUSTOMER_READ,
        // プラグイン管理 (mcp:plugin:read)
        'list_plugins' => McpScope::ROLE_PLUGIN_READ,
        'get_plugin' => McpScope::ROLE_PLUGIN_READ,
    ];

    private function __construct()
    {
    }

    /**
     * Tool 名に対応する必要 role を返す。 未登録なら null (= 呼び出し側で fail-closed deny)。
     */
    public static function requiredRole(string $toolName): ?string
    {
        return self::MAP[$toolName] ?? null;
    }
}

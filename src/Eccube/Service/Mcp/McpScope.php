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
 * MCP Tool が要求する scope の Symfony Security role 名。 集中管理する。
 *
 * 設計 §4.1 の通り、 scope 名はコロン区切り (`mcp:product:read` 等)。 OAuth2 サーバが
 * `role_prefix: ROLE_OAUTH2_` で role に変換するため、 ここで role 形式に揃えて
 * 定数化しておく (設計の「scope 名は定数で集中管理し、 必要なら後から一括変更」要件)。
 */
final class McpScope
{
    public const ROLE_PRODUCT_READ = 'ROLE_OAUTH2_MCP:PRODUCT:READ';
    public const ROLE_ORDER_READ = 'ROLE_OAUTH2_MCP:ORDER:READ';
    public const ROLE_CUSTOMER_READ = 'ROLE_OAUTH2_MCP:CUSTOMER:READ';
    public const ROLE_PLUGIN_READ = 'ROLE_OAUTH2_MCP:PLUGIN:READ';

    public const SCOPE_PRODUCT_READ = 'mcp:product:read';
    public const SCOPE_ORDER_READ = 'mcp:order:read';
    public const SCOPE_CUSTOMER_READ = 'mcp:customer:read';
    public const SCOPE_PLUGIN_READ = 'mcp:plugin:read';

    private function __construct()
    {
    }
}

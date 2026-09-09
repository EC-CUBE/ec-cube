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
 * mcp-bundle に登録する EC-CUBE の MCP サーバ (`mcp.servers.<NAME>`) と、 bundle がその名前から組むサービス ID。
 *
 * mcp-bundle はサーバ単位でサービスを登録し、 registry / builder のサービス ID にサーバ名を埋め込む
 * (`mcp.server.<name>.registry` / `mcp.server.<name>.builder`)。 `NAME` は
 * `app/config/eccube/packages/mcp.yaml` の `mcp.servers` キーと一致させる。
 */
final class McpServerDefinition
{
    public const NAME = 'eccube';

    public const BUILDER_SERVICE_ID = 'mcp.server.'.self::NAME.'.builder';

    public const REGISTRY_SERVICE_ID = 'mcp.server.'.self::NAME.'.registry';
}

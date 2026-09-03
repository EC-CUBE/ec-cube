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

namespace Eccube\Tests\Service\Mcp;

use Eccube\Repository\BaseInfoRepository;

/**
 * MCP 機能フラグ (`BaseInfo::mcp_enabled`) を切り替えるテスト用ヘルパ。
 *
 * `mcp_enabled` は既定 OFF のため、 HTTP エンドポイント (`^/admin/mcp`) や CLI ツールの実挙動を検証する
 * テストは、 明示的に ON にしないと `McpEnabledListener` が 404 を返す / `McpCliToolInvoker` が例外を投げる。
 * EccubeTestCase を継承したテストで use する前提 (`$this->entityManager` に依存)。
 */
trait EnablesMcpTrait
{
    private function setMcpEnabled(bool $enabled): void
    {
        static::getContainer()->get(BaseInfoRepository::class)->get()->setMcpEnabled($enabled);
        $this->entityManager->flush();
    }
}

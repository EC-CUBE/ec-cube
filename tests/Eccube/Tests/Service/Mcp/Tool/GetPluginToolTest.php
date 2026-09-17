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

namespace Eccube\Tests\Service\Mcp\Tool;

use Eccube\Service\Mcp\Tool\GetPluginTool;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * `GetPluginTool` の DB 結合テスト。 Api44 を題材に composer.json マージまで確認する。
 */
#[Group('mcp')]
final class GetPluginToolTest extends EccubeTestCase
{
    private ?GetPluginTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(GetPluginTool::class);
    }

    public function testReturnsPluginByCode(): void
    {
        $result = $this->tool->get(code: 'Api44');

        $this->assertSame('Api44', $result['code']);
        $this->assertArrayHasKey('composer', $result);
    }

    public function testReturnsEmptyWhenNotFound(): void
    {
        $result = $this->tool->get(code: 'NoSuchPlugin');

        $this->assertSame(['found' => false], $result);
    }

    public function testReturnsEmptyWhenNeitherIdNorCode(): void
    {
        $result = $this->tool->get();

        $this->assertSame(['found' => false], $result);
    }

    public function testIncludesComposerJsonDescriptionAndRequire(): void
    {
        $result = $this->tool->get(code: 'Api44');

        $composer = $result['composer'] ?? null;
        $this->assertIsArray($composer, 'composer キーが配列で含まれる');
        $this->assertArrayHasKey('description', $composer);
        $this->assertArrayHasKey('require', $composer);
        $this->assertIsArray($composer['require']);
        // Api44 は league/oauth2-server-bundle を require している
        $this->assertArrayHasKey('league/oauth2-server-bundle', $composer['require']);
    }
}

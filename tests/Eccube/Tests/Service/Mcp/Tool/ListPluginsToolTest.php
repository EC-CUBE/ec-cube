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

use Eccube\Service\Mcp\Tool\ListPluginsTool;
use Eccube\Tests\EccubeTestCase;

/**
 * `ListPluginsTool` の DB 結合テスト。 Api44 自身が install + enabled されている前提のため、
 * 最低 1 件 (Api44) が一覧に出ることを期待する。
 */
final class ListPluginsToolTest extends EccubeTestCase
{
    private ?ListPluginsTool $tool = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->tool = static::getContainer()->get(ListPluginsTool::class);
    }

    public function testReturnsPluginsWithScope(): void
    {
        $result = $this->tool->list();

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertGreaterThanOrEqual(1, $result['total'], 'Api44 が install されているので最低 1 件');
        $this->assertContains(
            'Api44',
            array_column($result['items'], 'code'),
            'install 済みの Api44 が一覧に含まれる',
        );
    }

    public function testEnabledFilterReturnsOnlyEnabled(): void
    {
        $enabled = $this->tool->list(enabledOnly: true);

        // 空配列だと foreach が無検証で通るため、 enabled な Api44 が居ることを先に担保する。
        $this->assertNotEmpty($enabled['items'], 'enabled なプラグイン (Api44) が居るので空ではない');
        foreach ($enabled['items'] as $item) {
            $this->assertTrue($item['enabled'] ?? false, sprintf('プラグイン "%s" は enabled のはず', $item['code'] ?? '?'));
        }
    }

    public function testItemFieldsAreSubsetOfPluginAllowList(): void
    {
        $result = $this->tool->list();
        $this->assertNotEmpty($result['items']);

        $allowed = ['id', 'name', 'code', 'enabled', 'version', 'source', 'initialized', 'create_date', 'update_date'];

        foreach ($result['items'] as $item) {
            foreach (array_keys($item) as $key) {
                $this->assertContains($key, $allowed, sprintf('出力フィールド "%s" は Plugin allow_list 外', $key));
            }
        }
    }
}

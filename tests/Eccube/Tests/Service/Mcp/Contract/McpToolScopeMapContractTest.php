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

namespace Eccube\Tests\Service\Mcp\Contract;

use Eccube\Service\Mcp\McpToolScopeMap;
use Eccube\Service\Mcp\Tool\SearchProductsTool;
use Mcp\Capability\Attribute\McpTool;
use PHPUnit\Framework\TestCase;

/**
 * `McpToolScopeMap` と実 Tool 群の整合を保証する契約テスト。
 *
 * scope 強制は `ScopeEnforcingReferenceHandler` が中央マップを引いて行い、 マップ未登録の Tool は
 * fail-closed で deny される。 その deny は実行時にしか起きないため、 「新規 Tool を追加したが scope
 * 登録を忘れて、 誰にも呼べないまま気付かれない」 事態を本テストで CI 時に検出する。
 *
 * Tool ディレクトリを実走査して `#[McpTool]` を全件発見するので、 列挙の更新漏れに依存しない。
 */
final class McpToolScopeMapContractTest extends TestCase
{
    public function testEveryToolHasScopeMapping(): void
    {
        $toolNames = $this->discoverToolNames();
        $this->assertNotEmpty($toolNames, 'Tool ディレクトリから #[McpTool] を発見できる');

        foreach ($toolNames as $toolName) {
            $this->assertNotNull(
                McpToolScopeMap::requiredRole($toolName),
                sprintf('Tool "%s" の必要 scope が McpToolScopeMap に登録されていない (fail-closed で全 deny になる)', $toolName),
            );
        }
    }

    public function testScopeMapHasNoStaleEntries(): void
    {
        $toolNames = $this->discoverToolNames();

        foreach (array_keys(McpToolScopeMap::MAP) as $mappedName) {
            $this->assertContains(
                $mappedName,
                $toolNames,
                sprintf('McpToolScopeMap の "%s" は実在する Tool に対応していない (typo / 削除済み Tool の残骸)', $mappedName),
            );
        }
    }

    /**
     * `src/Eccube/Service/Mcp/Tool/` を走査し、 各クラスの `#[McpTool]` 属性から tool 名を集める。
     *
     * @return list<string>
     */
    private function discoverToolNames(): array
    {
        $toolDir = \dirname((string) (new \ReflectionClass(SearchProductsTool::class))->getFileName());
        $files = glob($toolDir.'/*.php');
        $this->assertIsArray($files);

        $names = [];
        foreach ($files as $file) {
            $class = 'Eccube\\Service\\Mcp\\Tool\\'.basename($file, '.php');
            if (!class_exists($class)) {
                continue;
            }

            foreach ((new \ReflectionClass($class))->getMethods() as $method) {
                foreach ($method->getAttributes(McpTool::class) as $attribute) {
                    /** @var McpTool $instance */
                    $instance = $attribute->newInstance();
                    $names[] = $instance->name;
                }
            }
        }

        return $names;
    }
}

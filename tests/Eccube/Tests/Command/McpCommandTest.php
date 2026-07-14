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

namespace Eccube\Tests\Command;

use Eccube\Command\McpCallCommand;
use Eccube\Command\McpToolsCommand;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * MCP の CLI ディスパッチャ (`eccube:mcp:tools` / `eccube:mcp:call`) の結合テスト。
 *
 * ツール集合・射影は mcp-bundle の Registry と Api44 の allow_list に依存するため、
 * Api44 導入済みの mcp ジョブで実走する。
 */
#[Group('mcp')]
final class McpCommandTest extends EccubeTestCase
{
    public function testToolsListsDiscoveredTools(): void
    {
        $command = static::getContainer()->get(McpToolsCommand::class);
        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());

        $tools = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($tools);
        $names = array_column($tools, 'name');
        $this->assertContains('search_products', $names);
        $this->assertContains('get_product', $names);

        // 各要素が tools/list と同じ name/description/inputSchema を持つ
        foreach ($tools as $tool) {
            $this->assertArrayHasKey('name', $tool);
            $this->assertArrayHasKey('description', $tool);
            $this->assertArrayHasKey('inputSchema', $tool);
        }
    }

    public function testToolsNamesOnly(): void
    {
        $command = static::getContainer()->get(McpToolsCommand::class);
        $tester = new CommandTester($command);
        $tester->execute(['--names-only' => true]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());

        $names = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($names);
        $this->assertContains('search_products', $names);
    }

    public function testCallReturnsSummaryShape(): void
    {
        $product = $this->createProduct('MCPCLI Test Product');

        $command = static::getContainer()->get(McpCallCommand::class);
        $tester = new CommandTester($command);
        $tester->execute([
            'tool' => 'search_products',
            '--args' => json_encode(['keyword' => 'MCPCLI Test Product', 'limit' => 5]),
        ]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());

        $result = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);

        $item = null;
        foreach ($result['items'] as $candidate) {
            if (($candidate['id'] ?? null) === $product->getId()) {
                $item = $candidate;
                break;
            }
        }
        $this->assertNotNull($item, '作成した商品が検索結果に含まれること');

        // search_* はサマリ射影: price/stock の集約はあるが、 詳細フィールドは含まない
        $this->assertArrayHasKey('price', $item);
        $this->assertArrayHasKey('stock', $item);
        $this->assertArrayNotHasKey('description_detail', $item);
    }

    public function testCallUnknownToolFails(): void
    {
        $command = static::getContainer()->get(McpCallCommand::class);
        $tester = new CommandTester($command);
        $tester->execute(['tool' => 'no_such_tool']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
        $this->assertStringContainsString('search_products', $tester->getDisplay());
    }

    public function testCallInvalidJsonFails(): void
    {
        $command = static::getContainer()->get(McpCallCommand::class);
        $tester = new CommandTester($command);
        $tester->execute(['tool' => 'search_products', '--args' => 'not-json']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testCallJsonArrayArgsFails(): void
    {
        // 非空の JSON 配列は引数名で解決できず全既定値実行になるため INVALID で弾く
        $command = static::getContainer()->get(McpCallCommand::class);
        $tester = new CommandTester($command);
        $tester->execute(['tool' => 'search_products', '--args' => '[1,2,3]']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }
}

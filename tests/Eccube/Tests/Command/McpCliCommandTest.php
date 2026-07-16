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

use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * `eccube:cli:<tool>` 動的コマンド群（{@see \Eccube\Command\EccubeCliToolCommand}）の結合テスト。
 *
 * コマンドは McpCliCommandPass が MCP registry から生成する。 ツール集合・射影は Api44 の
 * allow_list に依存するため、 Api44 導入済みの mcp ジョブで実走する。
 */
#[Group('mcp')]
final class McpCliCommandTest extends EccubeTestCase
{
    private ?Application $application = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->application = new Application(static::$kernel);
    }

    public function testToolsAreRegisteredAsCommands(): void
    {
        $names = array_keys($this->application->all('eccube:cli'));

        $this->assertContains('eccube:cli:search_products', $names);
        $this->assertContains('eccube:cli:get_product', $names);
        $this->assertContains('eccube:cli:get_customer_orders', $names);
    }

    public function testSearchProductsReturnsMarkdownTable(): void
    {
        $product = $this->createProduct('MCPCLI Markdown Product');

        $tester = $this->execute('eccube:cli:search_products', [
            '--keyword' => 'MCPCLI Markdown Product',
            '--limit' => '5',
        ]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $display = $tester->getDisplay();

        // Markdown 表のヘッダと、 作成商品の行が出る
        $this->assertStringContainsString('| id | name |', $display);
        $this->assertStringContainsString($product->getName(), $display);
        // 詳細フィールドは含まない (サマリ射影)
        $this->assertStringNotContainsString('description_detail', $display);
    }

    public function testGetProductReturnsMarkdownDetail(): void
    {
        $product = $this->createProduct('MCPCLI Detail Product');

        $tester = $this->execute('eccube:cli:get_product', ['--id' => (string) $product->getId()]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $display = $tester->getDisplay();

        // 詳細系: スカラーは定義リスト、 ネスト関連は小見出し
        $this->assertStringContainsString('- **id**: '.$product->getId(), $display);
        $this->assertStringContainsString('### ProductClasses', $display);
    }

    public function testMissingRequiredOptionFails(): void
    {
        // get_customer_orders は customerId が必須
        $tester = $this->execute('eccube:cli:get_customer_orders', []);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
        $this->assertStringContainsString('customerId', $tester->getDisplay());
    }

    public function testInvalidNumericOptionFails(): void
    {
        // 数値型に非数値を渡すと黙って 0 にせず INVALID で弾く
        $tester = $this->execute('eccube:cli:search_products', ['--limit' => 'abc']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testSearchProductsWithUnresolvableStatusReturnsNoData(): void
    {
        // 明示 statusIds が 1 つも解決しないときは、 全公開状態に広がらず該当なしを返す
        $this->createProduct('MCPCLI Status Guard Product');

        $tester = $this->execute('eccube:cli:search_products', [
            '--keyword' => 'MCPCLI Status Guard Product',
            '--statusIds' => ['999'],
        ]);

        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertStringContainsString('該当なし', $tester->getDisplay());
    }

    /**
     * @param array<string, string|list<string>> $input
     */
    private function execute(string $name, array $input): CommandTester
    {
        $tester = new CommandTester($this->application->find($name));
        $tester->execute($input);

        return $tester;
    }
}

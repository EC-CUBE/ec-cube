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

namespace Eccube\Tests\Service\Security;

use Eccube\Service\Security\FilesystemKeyStore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 tests for FilesystemKeyStore (feature / purpose のパストラバーサル防御と feature 別パス解決).
 */
final class FilesystemKeyStoreTest extends TestCase
{
    /**
     * @return \Iterator<string, array{string}>
     */
    public static function invalidPurposeProvider(): \Iterator
    {
        yield 'parent traversal' => ['../../../etc/passwd'];
        yield 'slash' => ['foo/bar'];
        yield 'dot' => ['ucp.signing'];
        yield 'uppercase' => ['UcpSigning'];
        yield 'empty' => [''];
    }

    #[DataProvider(methodName: 'invalidPurposeProvider')]
    public function testReadRejectsInvalidPurpose(string $purpose): void
    {
        $store = new FilesystemKeyStore(sys_get_temp_dir(), 'agent-commerce');

        $this->expectException(\InvalidArgumentException::class);
        $store->read($purpose);
    }

    /**
     * @return \Iterator<string, array{string}>
     */
    public static function invalidFeatureProvider(): \Iterator
    {
        yield 'parent traversal' => ['../etc'];
        yield 'slash' => ['foo/bar'];
        yield 'dot' => ['agent.commerce'];
        yield 'uppercase' => ['AgentCommerce'];
        yield 'empty' => [''];
    }

    #[DataProvider(methodName: 'invalidFeatureProvider')]
    public function testConstructorRejectsInvalidFeature(string $feature): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FilesystemKeyStore(sys_get_temp_dir(), $feature);
    }

    public function testValidPurposeIsAccepted(): void
    {
        // 許可文字のみの purpose は例外を投げず、未生成キーは null を返す。
        $store = new FilesystemKeyStore(sys_get_temp_dir().'/eccube-keystore-test-'.bin2hex(random_bytes(4)), 'agent-commerce');

        $this->assertNull($store->read('ucp_signing'), '未生成の鍵は null を返す');
    }

    public function testWriteResolvesPathUnderFeatureDirectory(): void
    {
        // feature がパスに反映され、app/keystore/<feature>/<purpose>.key に生成されることを確認する。
        $projectDir = sys_get_temp_dir().'/eccube-keystore-test-'.bin2hex(random_bytes(4));
        $store = new FilesystemKeyStore($projectDir, 'mcp');

        $store->write('example', 'PEM-CONTENT');

        $expected = $projectDir.'/app/keystore/mcp/example.key';
        $this->assertFileExists($expected, 'feature 別ディレクトリ配下に鍵が生成される');
        $this->assertSame('PEM-CONTENT', file_get_contents($expected));
        $this->assertSame('PEM-CONTENT', $store->read('example'), 'read は書き込んだ内容を返す');

        // 後片付け
        unlink($expected);
        rmdir($projectDir.'/app/keystore/mcp');
        rmdir($projectDir.'/app/keystore');
        rmdir($projectDir.'/app');
        rmdir($projectDir);
    }
}

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

namespace Eccube\Tests\Service\AgentCommerce\Security;

use Eccube\Service\AgentCommerce\Security\FilesystemKeyStore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Layer 1 tests for FilesystemKeyStore (purpose のパストラバーサル防御とパーミッション).
 */
final class FilesystemKeyStoreTest extends TestCase
{
    private const KEY_PATH = '/app/keystore/agent-commerce/ucp_signing.key';

    private string $projectDir;

    private Filesystem $fs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->projectDir = sys_get_temp_dir().'/eccube-keystore-'.bin2hex(random_bytes(6));
        $this->fs->mkdir($this->projectDir);
        // 祖先の権限で判定がぶれないよう, 一時ディレクトリの権限は umask に依らず固定する.
        $this->fs->chmod($this->projectDir, 0755);
    }

    protected function tearDown(): void
    {
        if (isset($this->fs)) {
            $this->fs->chmod($this->projectDir, 0755, 0000, true);
            $this->fs->remove($this->projectDir);
        }

        parent::tearDown();
    }

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
        $store = new FilesystemKeyStore($this->projectDir);

        $this->expectException(\InvalidArgumentException::class);
        $store->read($purpose);
    }

    public function testValidPurposeIsAccepted(): void
    {
        // 許可文字のみの purpose は例外を投げず、未生成キーは null を返す。
        $store = new FilesystemKeyStore($this->projectDir);

        $this->assertNull($store->read('ucp_signing'), '未生成の鍵は null を返す');
    }

    public function testWriteThenRead(): void
    {
        $store = new FilesystemKeyStore($this->projectDir);
        $store->write('ucp_signing', 'PEM-BODY');

        $this->assertSame('PEM-BODY', $store->read('ucp_signing'));
    }

    public function testGetPathUsesDefaultLocation(): void
    {
        $store = new FilesystemKeyStore($this->projectDir);

        $this->assertSame($this->projectDir.self::KEY_PATH, $store->getPath('ucp_signing'));
    }

    public function testGetPathHonorsEnvOverride(): void
    {
        $store = new FilesystemKeyStore($this->projectDir, ['ucp_signing' => '/secure/ucp.key']);

        $this->assertSame('/secure/ucp.key', $store->getPath('ucp_signing'));
    }

    /**
     * 既定では Web サーバーからも読める権限で作る.
     *
     * 所有者専用 (0700 / 0600) にすると, Web サーバーと CLI を別ユーザーに分けた構成で
     * CLI が配置した鍵を Web サーバーが読めなくなる. chgrp できない環境があるため既定にはしない.
     */
    public function testWriteCreatesWebReadableKeyByDefault(): void
    {
        $path = $this->write(new FilesystemKeyStore($this->projectDir));

        $this->assertSame(0644, fileperms($path) & 0777, '鍵ファイルは 0644');
        $this->assertSame(0755, fileperms(\dirname($path)) & 0777, '格納ディレクトリは 0755');
    }

    public function testWriteCreatesOwnerOnlyKeyInStrictMode(): void
    {
        $path = $this->write(new FilesystemKeyStore($this->projectDir, [], true));

        $this->assertSame(0600, fileperms($path) & 0777, '鍵ファイルは 0600');
        $this->assertSame(0700, fileperms(\dirname($path)) & 0777, '格納ディレクトリは 0700');
    }

    /**
     * umask が厳しくてもモードは明示する.
     *
     * 鍵を Web サーバーが読めるかどうかは機能要件のため, ECCUBE_UMASK の設定に左右させない.
     */
    public function testWriteAppliesModeRegardlessOfUmask(): void
    {
        $previous = umask(0077);
        try {
            $path = $this->write(new FilesystemKeyStore($this->projectDir));
        } finally {
            umask($previous);
        }

        $this->assertSame(0644, fileperms($path) & 0777);
        $this->assertSame(0755, fileperms(\dirname($path)) & 0777);
        // 途中に作られた階層も通り抜けられなければ鍵は読めない
        $this->assertSame(0755, fileperms($this->projectDir.'/app/keystore') & 0777);
        $this->assertSame(0755, fileperms($this->projectDir.'/app') & 0777);
    }

    /**
     * 厳格モードで作った鍵を作り直すと, ファイルのモードは既定へ戻る.
     *
     * ディレクトリは既にあるため変更しない (運用側で設定した権限を上書きしないため).
     */
    public function testWriteRelaxesFileModeOfAnExistingKey(): void
    {
        $this->write(new FilesystemKeyStore($this->projectDir, [], true));
        $path = $this->write(new FilesystemKeyStore($this->projectDir));

        $this->assertSame(0644, fileperms($path) & 0777, 'ファイルのモードは作り直しで更新する');
        $this->assertSame(0700, fileperms(\dirname($path)) & 0777, '既存ディレクトリのモードは変更しない');
    }

    private function write(FilesystemKeyStore $store): string
    {
        $store->write('ucp_signing', 'PEM-BODY');
        clearstatcache();

        return $this->projectDir.self::KEY_PATH;
    }
}

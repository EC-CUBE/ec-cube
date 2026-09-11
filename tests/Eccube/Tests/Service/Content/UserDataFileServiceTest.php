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

namespace Eccube\Tests\Service\Content;

use Eccube\Exception\ContentValidationException;
use Eccube\Service\Content\ContentStatus;
use Eccube\Service\Content\UserDataFileService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * html/user_data の境界検査と名前検証を verify する.
 *
 * 一時ディレクトリを root にして直接インスタンス化するため, 実際の html/user_data には触れない.
 */
final class UserDataFileServiceTest extends TestCase
{
    private string $baseDir;

    private string $root;

    private Filesystem $fs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->baseDir = sys_get_temp_dir().'/eccube-user-data-'.bin2hex(random_bytes(6));
        $this->root = $this->baseDir.'/user_data';
        $this->fs->mkdir($this->root);
        // 区切り文字を含めずに前方一致すると配下と誤判定される兄弟ディレクトリ
        $this->fs->mkdir($this->baseDir.'/user_data_evil');
        $this->fs->dumpFile($this->baseDir.'/user_data_evil/secret.txt', 'secret');
        $this->fs->dumpFile($this->baseDir.'/outside.txt', 'outside');
    }

    protected function tearDown(): void
    {
        if (isset($this->fs)) {
            $this->fs->remove($this->baseDir);
        }

        parent::tearDown();
    }

    public function testResolveReturnsPathUnderRoot(): void
    {
        $this->assertSame($this->root, $this->service()->resolve(null));
        $this->assertSame($this->root, $this->service()->resolve('/'));
        $this->assertSame($this->root.'/assets/css/customize.css', $this->service()->resolve('/assets/css/customize.css'));
        $this->assertSame($this->root.'/assets/css/customize.css', $this->service()->resolve('assets/css/customize.css'));
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function dataProviderTraversalPaths(): iterable
    {
        yield '親ディレクトリ' => ['../outside.txt'];
        yield '先頭スラッシュ付きの親ディレクトリ' => ['/../outside.txt'];
        yield '途中の親ディレクトリ' => ['assets/../../outside.txt'];
        yield '兄弟ディレクトリ' => ['../user_data_evil/secret.txt'];
        yield 'ヌルバイト' => ["assets/customize.css\0.php"];
    }

    #[DataProvider(methodName: 'dataProviderTraversalPaths')]
    public function testResolveRejectsPathsOutsideRoot(string $path): void
    {
        $this->assertNull($this->service()->tryResolve($path));

        $this->expectException(ContentValidationException::class);
        $this->service()->resolve($path);
    }

    /**
     * 区切り文字を含めない前方一致では, 同じ接頭辞の兄弟ディレクトリを配下と誤判定する.
     *
     * FileController::checkDir() は str_starts_with(realpath($target), realpath($top)) で
     * 判定していたため, user_data_evil が user_data の配下と扱われていた.
     */
    public function testContainsRejectsSiblingDirectoryWithSamePrefix(): void
    {
        $this->assertFalse($this->service()->contains($this->baseDir.'/user_data_evil'));
        $this->assertFalse($this->service()->contains($this->baseDir.'/user_data_evil/secret.txt'));
        $this->assertTrue($this->service()->contains($this->root));
        $this->assertTrue($this->service()->contains($this->root.'/assets'));
    }

    public function testResolveRejectsSymlinkEscape(): void
    {
        $this->skipIfSymlinkIsUnavailable();

        symlink($this->baseDir.'/user_data_evil', $this->root.'/link');

        $this->assertNull($this->service()->tryResolve('link'));
        $this->assertNull($this->service()->tryResolve('link/secret.txt'));
    }

    /**
     * リンク先が存在しないシンボリックリンクは解決できないため拒否する.
     *
     * 解決せずにパスを組み立てると, 外部を指す壊れたリンク越しにファイルを作成できてしまう.
     */
    public function testResolveRejectsBrokenSymlink(): void
    {
        $this->skipIfSymlinkIsUnavailable();

        symlink($this->baseDir.'/not-exists', $this->root.'/broken');

        $this->assertNull($this->service()->tryResolve('broken'));
        $this->assertNull($this->service()->tryResolve('broken/created.txt'));
    }

    /**
     * 未作成のパスも解決できる (put の配置先に使うため).
     */
    public function testResolveAcceptsMissingPath(): void
    {
        $this->assertSame($this->root.'/not/created/yet.css', $this->service()->resolve('not/created/yet.css'));
    }

    public function testToRelative(): void
    {
        $this->assertSame('/', $this->service()->toRelative($this->root));
        $this->assertSame('/assets/css', $this->service()->toRelative($this->root.'/assets/css'));
    }

    public function testWriteCreatesIntermediateDirectories(): void
    {
        $result = $this->service()->write('assets/css/customize.css', 'body{}');

        $this->assertSame(ContentStatus::Created, $result->status);
        $this->assertSame('/assets/css/customize.css', $result->identifier);
        $this->assertSame('body{}', file_get_contents($this->root.'/assets/css/customize.css'));
    }

    public function testWriteIsIdempotent(): void
    {
        $this->service()->write('sample.css', 'body{}');
        $result = $this->service()->write('sample.css', 'body{}');

        $this->assertSame(ContentStatus::Unchanged, $result->status);
        $this->assertSame([], $result->writtenPaths);
    }

    public function testWriteDryRunDoesNotWrite(): void
    {
        $result = $this->service()->write('sample.css', 'body{}', true);

        $this->assertSame(ContentStatus::Created, $result->status);
        $this->assertSame([], $result->writtenPaths);
        $this->assertFileDoesNotExist($this->root.'/sample.css');
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function dataProviderInvalidNames(): iterable
    {
        yield '許可されていない拡張子' => ['evil.php'];
        yield '拡張子なし' => ['README'];
        yield 'ドットファイル' => ['.htaccess'];
        yield '使用できない文字' => ["'quote'.txt"];
        yield '不正な中間ディレクトリ' => ['ディレクトリ/sample.css'];
        yield 'ドットで始まる中間ディレクトリ' => ['.hidden/sample.css'];
    }

    /**
     * html/ はドキュメントルートのため, 名前と拡張子の検証は CLI にも適用する.
     */
    #[DataProvider(methodName: 'dataProviderInvalidNames')]
    public function testWriteRejectsInvalidNames(string $path): void
    {
        $this->expectException(ContentValidationException::class);

        $this->service()->write($path, 'x');
    }

    public function testWriteRejectsRoot(): void
    {
        $this->expectException(ContentValidationException::class);

        $this->service()->write('/', 'x');
    }

    public function testReadThrowsWhenMissing(): void
    {
        $this->expectException(ContentValidationException::class);

        $this->service()->read('not-exists.css');
    }

    public function testList(): void
    {
        $this->service()->write('assets/css/customize.css', 'body{}');
        $this->service()->write('sample.txt', 'text');

        $paths = array_column($this->service()->list(), 'path');
        $this->assertSame(['/assets', '/sample.txt'], $paths);

        $recursive = array_column($this->service()->list(null, true), 'path');
        $this->assertContains('/assets/css/customize.css', $recursive);
    }

    public function testListRejectsFile(): void
    {
        $this->service()->write('sample.txt', 'text');

        $this->expectException(ContentValidationException::class);

        $this->service()->list('sample.txt');
    }

    public function testRemoveRequiresRecursiveForNonEmptyDirectory(): void
    {
        $this->service()->write('assets/css/customize.css', 'body{}');

        try {
            $this->service()->remove('assets');
            self::fail('空でないディレクトリは --recursive なしで削除できない');
        } catch (ContentValidationException) {
            $this->assertDirectoryExists($this->root.'/assets');
        }

        $result = $this->service()->remove('assets', true);

        $this->assertSame(ContentStatus::Removed, $result->status);
        $this->assertDirectoryDoesNotExist($this->root.'/assets');
    }

    public function testRemoveRejectsRoot(): void
    {
        $this->expectException(ContentValidationException::class);

        $this->service()->remove('/');
    }

    public function testRemoveDryRunKeepsFile(): void
    {
        $this->service()->write('sample.txt', 'text');

        $result = $this->service()->remove('sample.txt', false, true);

        $this->assertSame(ContentStatus::Removed, $result->status);
        $this->assertFileExists($this->root.'/sample.txt');
    }

    private function service(): UserDataFileService
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id): string => $id);

        return new UserDataFileService(
            $this->root,
            ['css', 'js', 'txt', 'html', 'png'],
            new Filesystem(),
            $translator
        );
    }

    private function skipIfSymlinkIsUnavailable(): void
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            self::markTestSkipped('Windows ではシンボリックリンクを作成できない場合がある.');
        }
    }
}

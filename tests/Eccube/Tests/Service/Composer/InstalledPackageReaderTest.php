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

namespace Eccube\Tests\Service\Composer;

use Eccube\Service\Composer\InstalledPackageReader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class InstalledPackageReaderTest extends TestCase
{
    private string $dir;

    private string $path;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/installed-package-reader.'.bin2hex(random_bytes(8));
        mkdir($this->dir);
        $this->path = $this->dir.'/installed.json';
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->dir);
    }

    /**
     * composer 2 の形式 (packages の下) と composer 1 の形式 (トップレベルが配列) の両方を読む.
     * パッケージ名の大文字小文字は区別しない.
     */
    #[DataProvider(methodName: 'provideFormat')]
    public function testFind(bool $composer1): void
    {
        $packages = [
            ['name' => 'vendor/library', 'type' => 'library'],
            ['name' => 'ec-cube/foo', 'type' => 'eccube-plugin', 'extra' => ['code' => 'Foo', 'id' => 100]],
        ];
        file_put_contents($this->path, json_encode($composer1 ? $packages : ['packages' => $packages, 'dev' => true]));

        $reader = new InstalledPackageReader($this->path);

        $this->assertSame($packages[1], $reader->find('ec-cube/Foo'));
        $this->assertNull($reader->find('ec-cube/bar'));
    }

    /**
     * @return \Iterator<string, array{bool}>
     */
    public static function provideFormat(): \Iterator
    {
        yield 'composer 2' => [false];
        yield 'composer 1' => [true];
    }

    /**
     * 呼ぶたびに読み直す (composer を実行すると書き換わるため).
     */
    public function testFindReadsFileEveryTime(): void
    {
        file_put_contents($this->path, json_encode(['packages' => []]));
        $reader = new InstalledPackageReader($this->path);
        $this->assertNull($reader->find('ec-cube/foo'));

        file_put_contents($this->path, json_encode(['packages' => [['name' => 'ec-cube/foo']]]));
        $this->assertSame(['name' => 'ec-cube/foo'], $reader->find('ec-cube/foo'));
    }

    /**
     * ファイルが無い・壊れている場合は, 例外にせず null を返す.
     */
    #[DataProvider(methodName: 'provideBrokenFile')]
    public function testFindReturnsNullForBrokenFile(?string $contents): void
    {
        if ($contents !== null) {
            file_put_contents($this->path, $contents);
        }

        $this->assertNull((new InstalledPackageReader($this->path))->find('ec-cube/foo'));
    }

    /**
     * @return \Iterator<string, array{string|null}>
     */
    public static function provideBrokenFile(): \Iterator
    {
        yield 'ファイルが無い' => [null];
        yield 'JSON でない' => ['{'];
        yield 'packages が配列でない' => ['{"packages": "ec-cube/foo"}'];
        yield 'パッケージが配列でない・name が文字列でない' => ['{"packages": ["ec-cube/foo", {"name": ["ec-cube/foo"]}]}'];
    }
}

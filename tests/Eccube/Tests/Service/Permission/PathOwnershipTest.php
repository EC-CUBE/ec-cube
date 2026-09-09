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

namespace Eccube\Tests\Service\Permission;

use Eccube\Service\Permission\PathOwnership;
use Eccube\Service\Permission\UserIdentity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * uid / gid / パーミッションビットからの書き込み・読み取り可否の推定を検証する.
 *
 * 実ファイルシステムには依存させない. chmod を使う検証は root で実行した場合に常に書き込み可能となり,
 * Docker 開発環境と CI で結果が変わるため.
 */
final class PathOwnershipTest extends TestCase
{
    #[DataProvider(methodName: 'writableCases')]
    public function testIsWritableBy(int $ownerUid, int $ownerGid, int $permissions, int $uid, int $gid, bool $expected): void
    {
        $ownership = new PathOwnership('/path', true, $ownerUid, $ownerGid, $permissions, true);

        $this->assertSame($expected, $ownership->isWritableBy(new UserIdentity($uid, $gid, 'test')));
    }

    /**
     * @return iterable<string, array{int, int, int, int, int, bool}>
     */
    public static function writableCases(): iterable
    {
        // 所有者一致
        yield 'owner with write bit' => [1000, 1000, 0700, 1000, 1000, true];
        yield 'owner without write bit' => [1000, 1000, 0500, 1000, 1000, false];

        // グループ一致 (所有者は不一致)
        yield 'group with write bit' => [1000, 33, 0070, 33, 33, true];
        yield 'group without write bit' => [1000, 33, 0050, 33, 33, false];

        // other
        yield 'other with write bit' => [1000, 1000, 0007, 33, 33, true];
        yield 'other without write bit' => [1000, 1000, 0005, 33, 33, false];

        // POSIX は owner / group / other のうち 1 つのクラスだけを見る.
        // other が緩くても, 一致したクラスのビットで可否が決まる
        yield 'owner class is used even when other is permissive' => [1000, 1000, 0006, 1000, 1000, false];
        yield 'group class is used even when other is permissive' => [1000, 33, 0006, 33, 33, false];
        yield 'owner class is used even when group is permissive' => [1000, 33, 0070, 1000, 33, false];

        // レーン S で守りたい形: SSH ユーザー所有 0755 は Web サーバーから書けない
        yield 'ssh owned 0755 is not writable by web server' => [1000, 1000, 0755, 33, 33, false];
        // group 書き込みを付けると Web サーバーから書けてしまう
        yield 'ssh owned 0775 with shared group is writable by web server' => [1000, 33, 0775, 33, 33, true];

        // root はパーミッションビットに関わらず書き込める
        yield 'root ignores permission bits' => [1000, 1000, 0000, 0, 0, true];
    }

    #[DataProvider(methodName: 'readableCases')]
    public function testIsReadableBy(int $ownerUid, int $ownerGid, int $permissions, int $uid, int $gid, bool $expected): void
    {
        $ownership = new PathOwnership('/path', true, $ownerUid, $ownerGid, $permissions, true);

        $this->assertSame($expected, $ownership->isReadableBy(new UserIdentity($uid, $gid, 'test')));
    }

    /**
     * @return iterable<string, array{int, int, int, int, int, bool}>
     */
    public static function readableCases(): iterable
    {
        yield 'owner with read bit' => [1000, 1000, 0500, 1000, 1000, true];
        yield 'group with read bit' => [1000, 33, 0050, 33, 33, true];
        yield 'other with read bit' => [1000, 1000, 0005, 33, 33, true];
        yield 'no read bit for other' => [1000, 1000, 0770, 33, 33, false];

        // 書き込みと同じく, 一致したクラスのビットだけで決まる
        yield 'owner class is used even when other is permissive' => [1000, 1000, 0004, 1000, 1000, false];
        yield 'group class is used even when other is permissive' => [1000, 33, 0004, 33, 33, false];
    }

    public function testDirectoryRequiresTheExecuteBit(): void
    {
        // エントリの作成・削除には w に加えて x が, 配下のファイルを開くには x が必要になる
        $dir = new PathOwnership('/path', true, 1000, 1000, 0600, true);
        $user = new UserIdentity(1000, 1000, 'test');

        $this->assertFalse($dir->isWritableBy($user));
        $this->assertFalse($dir->isReadableBy($user));
        $this->assertFalse($dir->isTraversableBy($user));
    }

    public function testFileDoesNotRequireTheExecuteBit(): void
    {
        $file = new PathOwnership('/path/.env', true, 1000, 1000, 0600, false);
        $user = new UserIdentity(1000, 1000, 'test');

        $this->assertTrue($file->isWritableBy($user));
        $this->assertTrue($file->isReadableBy($user));
    }

    public function testUnreachableAncestorIsTheShallowestOne(): void
    {
        $webServer = new UserIdentity(33, 33, 'test');
        $ownership = new PathOwnership('/project/html/upload/temp_image', true, 33, 33, 0755, true, [
            new PathOwnership('/', true, 0, 0, 0755, true),
            new PathOwnership('/project', true, 1000, 1000, 0711, true),
            new PathOwnership('/project/html', true, 1000, 1000, 0700, true),
            new PathOwnership('/project/html/upload', true, 1000, 1000, 0700, true),
        ]);

        // 対象自身は Web サーバー所有 0755 でも, 祖先を通り抜けられなければ到達できない
        $this->assertSame('/project/html', $ownership->unreachableAncestorFor($webServer)?->path);
        $this->assertFalse($ownership->hasUnknownAncestor());
    }

    public function testReachableAncestorsReturnNull(): void
    {
        $ownership = new PathOwnership('/project/var', true, 33, 33, 0755, true, [
            new PathOwnership('/', true, 0, 0, 0755, true),
            new PathOwnership('/project', true, 1000, 1000, 0711, true),
        ]);

        $this->assertNotInstanceOf(PathOwnership::class, $ownership->unreachableAncestorFor(new UserIdentity(33, 33, 'test')));
    }

    public function testAncestorThatCannotBeStattedIsUnknown(): void
    {
        // open_basedir 等で参照できない祖先は, 通り抜けられないと断定しない
        $ownership = new PathOwnership('/project/var', true, 33, 33, 0755, true, [
            new PathOwnership('/', false, -1, -1, 0, false),
            new PathOwnership('/project', true, 1000, 1000, 0711, true),
        ]);

        $this->assertTrue($ownership->hasUnknownAncestor());
        $this->assertNotInstanceOf(PathOwnership::class, $ownership->unreachableAncestorFor(new UserIdentity(33, 33, 'test')));
    }

    public function testAncestorsIncludeTheResolvedPathOfASymlink(): void
    {
        // stat() はリンクを解決するため, 対象自身の権限はリンク先のものになる.
        // リンク先の親を通り抜けられなければ到達できない
        $root = sys_get_temp_dir().'/eccube-path-'.bin2hex(random_bytes(6));
        $fs = new Filesystem();
        $fs->mkdir($root.'/physical/target', 0755);
        // mkdir() は umask の影響を受けるため, 判定に使うビットは chmod で明示する
        $fs->chmod($root, 0755);
        $fs->chmod($root.'/physical', 0700);
        symlink($root.'/physical/target', $root.'/visible');

        // 所有者と一致すると 0700 でも通り抜けられるため, テストを実行するユーザーとは別の uid / gid で判定する
        $other = new UserIdentity((int) fileowner($root.'/physical') + 1, (int) filegroup($root.'/physical') + 1, 'test');

        try {
            $ancestors = [];
            foreach (PathOwnership::of($root.'/visible')->ancestors as $ancestor) {
                $ancestors[$ancestor->path] = $ancestor;
            }

            $this->assertArrayHasKey($root, $ancestors, '論理パスの祖先を評価すること');
            $this->assertArrayHasKey($root.'/physical', $ancestors, 'リンク解決後の物理パスの祖先を評価すること');
            $this->assertTrue($ancestors[$root]->isTraversableBy($other));
            $this->assertFalse($ancestors[$root.'/physical']->isTraversableBy($other), 'リンク先の親を通り抜けられない');
        } finally {
            $fs->chmod($root.'/physical', 0755);
            $fs->remove($root);
        }
    }

    public function testOfCollectsAncestorsFromTheRoot(): void
    {
        $ownership = PathOwnership::of(__FILE__);

        $this->assertNotSame([], $ownership->ancestors);
        $this->assertSame('/', $ownership->ancestors[0]->path);
        $this->assertSame(__DIR__, $ownership->ancestors[array_key_last($ownership->ancestors)]->path);
        $this->assertTrue($ownership->ancestors[0]->isDir);
    }

    public function testIsWorldWritable(): void
    {
        $this->assertTrue((new PathOwnership('/path', true, 33, 33, 0777, true))->isWorldWritable());
        $this->assertFalse((new PathOwnership('/path', true, 33, 33, 0775, true))->isWorldWritable());
        $this->assertFalse((new PathOwnership('/path', false, -1, -1, 0, false))->isWorldWritable());
    }

    public function testNotExistsIsNeitherWritableNorReadable(): void
    {
        $ownership = new PathOwnership('/path', false, -1, -1, 0, false);
        $user = new UserIdentity(0, 0, 'test');

        $this->assertFalse($ownership->isWritableBy($user));
        $this->assertFalse($ownership->isReadableBy($user));
    }

    public function testOfReturnsNotExistsForMissingPath(): void
    {
        $ownership = PathOwnership::of(__DIR__.'/no-such-path');

        $this->assertFalse($ownership->exists);
    }

    public function testOfReadsOwnershipOfExistingPath(): void
    {
        $ownership = PathOwnership::of(__FILE__);

        $this->assertTrue($ownership->exists);
        $this->assertFalse($ownership->isDir);
        $this->assertSame(fileowner(__FILE__), $ownership->uid);
        $this->assertMatchesRegularExpression('/\A\d{4}\z/', $ownership->permissionsString());
    }
}

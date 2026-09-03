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
        yield 'owner with read bit' => [1000, 1000, 0400, 1000, 1000, true];
        yield 'group with read bit' => [1000, 33, 0040, 33, 33, true];
        yield 'other with read bit' => [1000, 1000, 0004, 33, 33, true];
        yield 'no read bit for other' => [1000, 1000, 0770, 33, 33, false];

        // 書き込みと同じく, 一致したクラスのビットだけで決まる
        yield 'owner class is used even when other is permissive' => [1000, 1000, 0004, 1000, 1000, false];
        yield 'group class is used even when other is permissive' => [1000, 33, 0004, 33, 33, false];
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

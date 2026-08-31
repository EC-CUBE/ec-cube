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

namespace Eccube\Service\Permission;

/**
 * パスの所有者 uid / グループ gid / パーミッションビット.
 *
 * 指定ユーザーから書き込めるかどうかは is_writable() では判定できない.
 * is_writable() が返すのは実行ユーザー (CLI 実行なら SSH ユーザー) から見た可否だけで,
 * Web サーバーから書けるかどうかは分からないため, パーミッションビットから推定する.
 * 補助グループ・ACL・SELinux までは判定できないため, 結果はあくまで推定として扱うこと.
 */
final readonly class PathOwnership
{
    public function __construct(
        public string $path,
        public bool $exists,
        public int $uid,
        public int $gid,
        public int $permissions,
        public bool $isDir,
    ) {
    }

    public static function of(string $path): self
    {
        $stat = file_exists($path) ? stat($path) : false;

        if ($stat === false) {
            return new self($path, false, -1, -1, 0, false);
        }

        return new self($path, true, $stat['uid'], $stat['gid'], $stat['mode'] & 07777, is_dir($path));
    }

    public function isWritableBy(UserIdentity $user): bool
    {
        return $this->isAllowedFor($user, 0002, 0200, 0020);
    }

    public function isReadableBy(UserIdentity $user): bool
    {
        return $this->isAllowedFor($user, 0004, 0400, 0040);
    }

    /**
     * 所有者・グループに関係なく, 任意のローカルユーザーから書き込めるか.
     */
    public function isWorldWritable(): bool
    {
        return $this->exists && ($this->permissions & 0002) !== 0;
    }

    public function permissionsString(): string
    {
        return sprintf('%04o', $this->permissions);
    }

    /**
     * @param int $otherBit other のパーミッションビット
     * @param int $ownerBit owner のパーミッションビット
     * @param int $groupBit group のパーミッションビット
     */
    private function isAllowedFor(UserIdentity $user, int $otherBit, int $ownerBit, int $groupBit): bool
    {
        if (!$this->exists) {
            return false;
        }

        // root はパーミッションビットに関わらずアクセスできる
        if ($user->uid === 0) {
            return true;
        }

        if (($this->permissions & $otherBit) !== 0) {
            return true;
        }

        if ($user->uid === $this->uid && ($this->permissions & $ownerBit) !== 0) {
            return true;
        }

        return $user->gid === $this->gid && ($this->permissions & $groupBit) !== 0;
    }
}

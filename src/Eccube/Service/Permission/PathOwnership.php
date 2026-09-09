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
 *
 * ディレクトリはエントリの作成・削除に w と x が, 配下のファイルを開くのに x が必要になるため,
 * 自身のビットに加えて祖先ディレクトリの x も評価する.
 * 補助グループ・ACL・SELinux までは判定できないため, 結果はあくまで推定として扱うこと.
 */
final readonly class PathOwnership
{
    /**
     * @param list<self> $ancestors 祖先ディレクトリ. ルート (/) から親ディレクトリまでを浅い順に並べる
     */
    public function __construct(
        public string $path,
        public bool $exists,
        public int $uid,
        public int $gid,
        public int $permissions,
        public bool $isDir,
        public array $ancestors = [],
    ) {
    }

    public static function of(string $path): self
    {
        $ancestors = array_map(static fn (string $ancestor): self => self::statOf($ancestor), self::ancestorPaths($path));

        return self::statOf($path, $ancestors);
    }

    public function isWritableBy(UserIdentity $user): bool
    {
        // ディレクトリはエントリの作成・削除に x も必要
        return $this->isAllowedFor($user, 0002, 0200, 0020)
            && (!$this->isDir || $this->isTraversableBy($user));
    }

    public function isReadableBy(UserIdentity $user): bool
    {
        // ディレクトリは配下のファイルを開くために x も必要
        return $this->isAllowedFor($user, 0004, 0400, 0040)
            && (!$this->isDir || $this->isTraversableBy($user));
    }

    /**
     * ディレクトリを通り抜けて配下へ到達できるか.
     */
    public function isTraversableBy(UserIdentity $user): bool
    {
        return $this->isAllowedFor($user, 0001, 0100, 0010);
    }

    /**
     * 到達を妨げている祖先ディレクトリ. すべて通り抜けられる場合は null.
     *
     * 論理パスの祖先, 解決後の物理パスの祖先の順に, それぞれ浅い方から探す.
     */
    public function unreachableAncestorFor(UserIdentity $user): ?self
    {
        foreach ($this->ancestors as $ancestor) {
            if ($ancestor->exists && !$ancestor->isTraversableBy($user)) {
                return $ancestor;
            }
        }

        return null;
    }

    /**
     * 権限を確認できなかった祖先ディレクトリがあるか.
     *
     * open_basedir で上位ディレクトリを参照できない場合に発生する.
     */
    public function hasUnknownAncestor(): bool
    {
        foreach ($this->ancestors as $ancestor) {
            if (!$ancestor->exists) {
                return true;
            }
        }

        return false;
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
     * @param list<self> $ancestors
     */
    private static function statOf(string $path, array $ancestors = []): self
    {
        // open_basedir の制限下では警告が発生するため抑制する. 参照できないことは exists=false で表す
        $stat = @stat($path);

        if ($stat === false) {
            return new self($path, false, -1, -1, 0, false, $ancestors);
        }

        return new self(
            $path,
            true,
            $stat['uid'],
            $stat['gid'],
            $stat['mode'] & 07777,
            ($stat['mode'] & 0170000) === 0040000,
            $ancestors
        );
    }

    /**
     * 通り抜ける必要がある祖先ディレクトリ.
     *
     * stat() はシンボリックリンクを解決するため, リンクを含むパスへ到達するには
     * 論理パスの祖先 (リンク自体へ辿り着くまで) と, 解決後の物理パスの祖先の両方が必要になる.
     * それぞれ浅い順に並べ, 重複は取り除く.
     *
     * @return list<string>
     */
    private static function ancestorPaths(string $path): array
    {
        $paths = self::parentPaths($path);

        // open_basedir の制限下では警告が発生するため抑制する
        $resolved = @realpath($path);
        if ($resolved === false || $resolved === $path) {
            return $paths;
        }

        foreach (self::parentPaths($resolved) as $parent) {
            if (!in_array($parent, $paths, true)) {
                $paths[] = $parent;
            }
        }

        return $paths;
    }

    /**
     * ルート (/) から親ディレクトリまでを浅い順に返す.
     *
     * @return list<string>
     */
    private static function parentPaths(string $path): array
    {
        $paths = [];
        $current = dirname($path);

        while (true) {
            $paths[] = $current;
            $parent = dirname($current);
            if ($parent === $current) {
                break;
            }

            $current = $parent;
        }

        return array_reverse($paths);
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

        // POSIX は owner / group / other のうち 1 つのクラスだけを見る.
        // 所有者が一致すれば, other が緩くても owner のビットで可否が決まる
        $bit = match (true) {
            $user->uid === $this->uid => $ownerBit,
            $user->gid === $this->gid => $groupBit,
            default => $otherBit,
        };

        return ($this->permissions & $bit) !== 0;
    }
}

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

namespace Eccube\Service\AgentCommerce\Security;

use Eccube\Service\Permission\PathOwnership;
use Eccube\Service\Permission\UserIdentity;
use Eccube\Service\Permission\WebServerUserResolver;

/**
 * キーストアの状態を purpose 単位で調べる.
 *
 * eccube:keystore:* の出力源. 鍵の有無・保管先・権限に加え, 署名は実行時に行われるため
 * Web サーバーから読み取れるかを判定する (PermissionDiagnostic と同じ推定方法).
 */
final readonly class KeyStoreInspector
{
    public function __construct(
        private KeyStoreInterface $keyStore,
        private KeyPurposeRegistry $keyPurposeRegistry,
        private WebServerUserResolver $webServerUserResolver,
    ) {
    }

    /**
     * 登録されているすべての purpose を調べる.
     *
     * @return list<KeyStoreEntry>
     */
    public function inspectAll(bool $withDetails = false): array
    {
        return array_values(array_map(
            fn (KeyPurposeInterface $purpose): KeyStoreEntry => $this->inspect($purpose, $withDetails),
            $this->keyPurposeRegistry->all()
        ));
    }

    /**
     * @param bool $withDetails describe() を呼び, 公開情報を含めるか
     */
    public function inspect(KeyPurposeInterface $purpose, bool $withDetails = false): KeyStoreEntry
    {
        $name = $purpose->getPurpose();
        $path = $this->keyStore instanceof KeyStorePathAwareInterface ? $this->keyStore->getPath($name) : null;

        $ownership = null;
        $exists = false;
        $permissions = null;
        $owner = null;
        $updatedAt = null;

        if ($path !== null) {
            clearstatcache(true, $path);
            $exists = is_file($path);
            if ($exists) {
                $ownership = PathOwnership::of($path);
                $permissions = $ownership->permissionsString();
                $owner = sprintf('%d:%d', $ownership->uid, $ownership->gid);
                $mtime = @filemtime($path);
                $updatedAt = $mtime === false ? null : date('Y-m-d H:i:s', $mtime);
            }
        }

        // 読めない鍵は read() が null を返すため, 有無と読み取り可否を分けて持つ.
        // 分けないと「読めないだけの鍵」を未生成とみなして上書きしてしまう.
        $material = $this->keyStore->read($name);
        $readable = $material !== null && trim($material) !== '';
        if ($path === null) {
            $exists = $readable;
        }

        $details = [];
        $error = null;
        if ($readable && $withDetails) {
            try {
                $details = $purpose->describe((string) $material);
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }
        if ($exists && !$readable) {
            $error = '実行中のユーザーからは鍵を読み取れません. 鍵を所有するユーザーで実行してください.';
        }

        [$webReadability, $hint] = $this->evaluateWebReadability($path, $exists, $ownership);

        return new KeyStoreEntry(
            $name,
            $purpose->getLabel(),
            $exists,
            $readable,
            $path,
            $permissions,
            $owner,
            $updatedAt,
            $webReadability,
            $hint,
            $details,
            $error
        );
    }

    /**
     * Web サーバーから鍵を読み取れるかを判定する.
     *
     * @return array{0: WebReadability, 1: string|null}
     */
    private function evaluateWebReadability(?string $path, bool $exists, ?PathOwnership $ownership): array
    {
        if ($path === null) {
            return [WebReadability::Unknown, 'キーストアがファイルではないため判定しません.'];
        }

        if (!$exists || !$ownership instanceof PathOwnership) {
            return [WebReadability::Unknown, null];
        }

        $webServer = $this->webServerUserResolver->resolve();
        if (!$webServer instanceof UserIdentity) {
            return [WebReadability::Unknown, 'Web サーバーの実行ユーザーを特定できないため判定できません.'];
        }

        if ($ownership->hasUnknownAncestor()) {
            return [WebReadability::Unknown, '祖先ディレクトリの権限を確認できないため判定できません.'];
        }

        $unreachable = $ownership->unreachableAncestorFor($webServer);
        if ($unreachable instanceof PathOwnership) {
            return [WebReadability::Unreadable, sprintf(
                '祖先ディレクトリ %s (uid=%d gid=%d %s) を通り抜けられません. chmod 0755 %s で実行権限を付与してください.',
                $unreachable->path,
                $unreachable->uid,
                $unreachable->gid,
                $unreachable->permissionsString(),
                $unreachable->path
            )];
        }

        if (!$ownership->isReadableBy($webServer)) {
            return [WebReadability::Unreadable, sprintf(
                '鍵ファイルの権限が %s のため Web サーバー (uid=%d gid=%d) から読み取れません. chmod 0644 %s で読み取りを許可するか, Web サーバーが属するグループへ chgrp してください.',
                $ownership->permissionsString(),
                $webServer->uid,
                $webServer->gid,
                $path
            )];
        }

        return [WebReadability::Readable, null];
    }
}

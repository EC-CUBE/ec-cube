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

namespace Eccube\Util;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

/**
 * cache:clear で %eccube_runtime_dir%/pools を削除する.
 *
 * cache:clear は kernel.cache_dir と kernel.build_dir を物理削除するだけで, cache pool を
 * 論理的にはクリアしない (cache.global_clearer / cache.system_clearer は kernel.cache_clearer
 * タグを持たないため, ChainCacheClearer には 1 件も登録されない). cache pool が
 * kernel.cache_dir 配下にあった頃は, ディレクトリごと消えることで結果的にクリアされていた.
 *
 * cache pool をランタイムディレクトリへ移したことでこの副作用が失われ, cache:clear の後も
 * 古い内容が残るようになった. とくに Doctrine のメタデータキャッシュが残ると, プラグインの
 * 更新でエンティティに追加されたカラムがスキーマ差分に現れなくなる.
 *
 * 従来と同じ範囲 (ファイルシステム上の pool) だけを削除する. Redis 等の外部ストアを
 * 使用している場合の挙動は変えない.
 */
final class RuntimeCachePoolClearer implements CacheClearerInterface
{
    /**
     * 削除できずに残った pool ディレクトリ. 削除できた場合と対象が無い場合は null.
     *
     * 権限を分離した構成ではランタイムディレクトリが Web サーバー所有のため, CLI ユーザーの
     * cache:clear からは削除できない. 例外にすると cache:clear がビルド生成物の削除まで含めて
     * 失敗するため, ここでは中断せずに結果を残し, 呼び出し側 (RuntimeCachePoolClearListener) が
     * 未削除であることと対処方法を通知する.
     */
    private ?string $unclearedPath = null;

    public function __construct(private readonly string $runtimeDir)
    {
    }

    #[\Override]
    public function clear(string $cacheDir): void
    {
        $this->unclearedPath = null;

        $pools = rtrim($this->runtimeDir, '/').'/pools';
        if (!is_dir($pools)) {
            return;
        }

        try {
            (new Filesystem())->remove($pools);
        } catch (IOException) {
            // 実行時キャッシュの削除は cache:pool:clear (Web サーバーのユーザー) に委ねる.
            // Filesystem::remove() は一部でも削除できないと例外を投げるため, ここで残存を検知できる.
            $this->unclearedPath = $pools;
        }
    }

    /**
     * 直前の clear() で削除できずに残った pool ディレクトリを返す.
     */
    public function getUnclearedPath(): ?string
    {
        return $this->unclearedPath;
    }
}

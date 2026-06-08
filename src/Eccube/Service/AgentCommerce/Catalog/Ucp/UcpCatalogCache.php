<?php

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

namespace Eccube\Service\AgentCommerce\Catalog\Ucp;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Lock\LockFactory;

/**
 * UCP Catalog レスポンスのファイルシステムキャッシュ.
 *
 * UCP Catalog は POST RPC のため ETag は使えず、リクエストボディの hash をキャッシュキー
 * とする。共有レンサバ前提のため symfony/cache の FilesystemAdapter を既定とする
 * (var/cache/<env>/agent-commerce/catalog 配下)。
 *
 * キャッシュミス時の再生成スタンピードを防ぐため symfony/lock の LockFactory で
 * 鍵ごとに排他し、ロック取得失敗時はブロックして待機する (取得後に再度キャッシュ確認)。
 *
 * 値は gzip 圧縮済みの本文をそのまま保持してよい (Content-Encoding: gzip での再利用)。
 */
class UcpCatalogCache
{
    /**
     * キャッシュ名前空間.
     */
    private const NAMESPACE = 'agent_commerce_ucp_catalog';

    private readonly FilesystemAdapter $adapter;

    /**
     * @param int $defaultLifetime キャッシュ TTL (秒). 0 で無期限
     */
    public function __construct(
        private readonly LockFactory $lockFactory,
        string $cacheDir,
        private readonly int $defaultLifetime = 3600,
    ) {
        $this->adapter = new FilesystemAdapter(
            self::NAMESPACE,
            $this->defaultLifetime,
            rtrim($cacheDir, '/').'/agent-commerce/catalog',
        );
    }

    /**
     * リクエストボディから安定したキャッシュキーを生成する.
     *
     * @param string $operation search / lookup / product 等のオペレーション識別子
     * @param string $rawBody   リクエストボディ (生 JSON)
     */
    public function buildKey(string $operation, string $rawBody): string
    {
        return $operation.'_'.hash('sha256', $rawBody);
    }

    /**
     * キャッシュ済みの値を取得する. 未キャッシュなら null.
     */
    public function get(string $key): ?string
    {
        $item = $this->adapter->getItem($key);
        if (!$item->isHit()) {
            return null;
        }

        $value = $item->get();

        return is_string($value) ? $value : null;
    }

    /**
     * 値をキャッシュへ格納する.
     */
    public function set(string $key, string $value): void
    {
        $item = $this->adapter->getItem($key);
        $item->set($value);
        $item->expiresAfter($this->defaultLifetime > 0 ? $this->defaultLifetime : null);
        $this->adapter->save($item);
    }

    /**
     * キャッシュ取得 → ミス時のみ生成、を stampede 防止付きで実行する.
     *
     * 1. キャッシュヒットなら即返す。
     * 2. ミスなら鍵ごとのロックを取得 (取得まで待機)。
     * 3. ロック取得後に再度キャッシュ確認 (待機中に他プロセスが生成済みなら再利用)。
     * 4. 依然ミスなら $generator で生成して格納し返す。
     *
     * @param callable():string $generator キャッシュミス時に値を生成するコールバック
     */
    public function getOrCompute(string $key, callable $generator): string
    {
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $lock = $this->lockFactory->createLock('ucp_catalog_'.$key);
        $lock->acquire(true);
        try {
            // 待機中に他プロセスが生成済みの可能性があるため再確認
            $cached = $this->get($key);
            if ($cached !== null) {
                return $cached;
            }

            $value = $generator();
            $this->set($key, $value);

            return $value;
        } finally {
            $lock->release();
        }
    }

    /**
     * 全キャッシュを破棄する (UCP「キャッシュクリア」相当).
     */
    public function clear(): void
    {
        $this->adapter->clear();
    }

    /**
     * 指定キーへ値を事前生成して格納する (UCP「生成」相当).
     *
     * @param callable():string $generator 値を生成するコールバック
     */
    public function warmup(string $key, callable $generator): void
    {
        $this->set($key, $generator());
    }

    /**
     * 内部アダプタの CacheItem を直接扱う必要がある場合の入口 (テスト / 高度な制御用).
     */
    public function getItem(string $key): CacheItemInterface
    {
        return $this->adapter->getItem($key);
    }
}

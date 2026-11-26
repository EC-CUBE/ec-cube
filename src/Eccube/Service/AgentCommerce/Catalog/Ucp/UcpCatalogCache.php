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
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Routing\RequestContext;

/**
 * UCP Catalog レスポンスのファイルシステムキャッシュ.
 *
 * UCP Catalog は POST RPC のため ETag は使えず、正規化したリクエストパラメータとオリジンの hash を
 * キャッシュキーとする ({@link buildKey()})。共有レンサバ前提のため symfony/cache の FilesystemAdapter を既定とする
 * (var/runtime/<env>/agent-commerce/catalog 配下)。
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
        private readonly LoggerInterface $logger,
        private readonly RequestContext $requestContext,
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
     * 正規化したリクエストパラメータと、応答の URL を決めるオリジンから安定したキャッシュキーを生成する.
     *
     * 生ボディをそのままキーにすると、意味の同じリクエストでも空白やキーの並びを変えるだけで別のキーになり、
     * キャッシュファイルとロックファイルを際限なく増やせる。呼び出し側は解釈済みのパラメータだけを渡す。
     *
     * 応答に含む商品・画像の URL はリクエストの Host / scheme / baseUrl (RequestContext) から生成されるため、
     * オリジンもキーに含める。含めないと、偽の Host ヘッダで生成した URL がキャッシュされ、
     * 同じリクエストを送った全員へ配られる (trusted_hosts を設定していない環境)。
     *
     * @param string               $operation search / lookup / product 等のオペレーション識別子
     * @param array<string, mixed> $params    正規化済みのリクエストパラメータ
     */
    public function buildKey(string $operation, array $params): string
    {
        $context = $this->requestContext;
        $origin = $context->getScheme().'://'.$context->getHost().':'
            .($context->getScheme() === 'https' ? $context->getHttpsPort() : $context->getHttpPort())
            .$context->getBaseUrl();

        ksort($params);

        return $operation.'_'.hash('sha256', $origin."\n".json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * search のキャッシュキーに使う正規化済みパラメータ.
     *
     * Catalog の search と、管理画面・CLI の事前生成で同じキーになるよう、組み立てをここに集める。
     *
     * @return array<string, mixed>
     */
    public static function searchParams(?string $query, int $limit, int $offset): array
    {
        return ['query' => $query, 'limit' => $limit, 'offset' => $offset];
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
        if (!$this->adapter->save($item)) {
            // I/O エラー等で永続化に失敗してもキャッシュは再計算可能なため処理は継続する。
            $this->logger->error('Failed to persist UCP catalog cache item.', ['key' => $key]);
        }
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
        if (!$this->adapter->clear()) {
            // クリア失敗時は古いレスポンスが残り得るため、運用者が気付けるようログへ残す。
            $this->logger->error('Failed to clear UCP catalog cache.');
        }
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

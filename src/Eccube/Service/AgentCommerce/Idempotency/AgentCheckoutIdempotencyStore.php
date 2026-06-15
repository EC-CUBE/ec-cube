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

namespace Eccube\Service\AgentCommerce\Idempotency;

use Eccube\Service\AgentCommerce\Exception\IdempotencyConflictException;
use Psr\Cache\CacheItemPoolInterface;

/**
 * エージェントチェックアウトの Idempotency-Key 処理.
 *
 * 状態変更操作 (POST/PUT) で `Idempotency-Key` ヘッダが付与された場合、初回はハンドラを実行し
 * 結果 (HTTP ステータス + レスポンスボディ + リクエストハッシュ) をキャッシュへ保存する。
 * 同一キーの再送はハンドラを再実行せずキャッシュ結果をリプレイする (副作用の再実行なし)。
 * 同一キーが**異なるリクエスト内容**で再利用された場合は {@link IdempotencyConflictException} を投げる。
 *
 * 標準は PSR-6 キャッシュ (FilesystemAdapter フォールバックが常に成立) を用いる。
 * キャッシュクリアでリプレイ記録が消える点に留意 (堅牢化は DB バックエンドを opt-in で差し替え可能)。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout-rest.md (Idempotency-Key, store >= 24h)
 */
class AgentCheckoutIdempotencyStore
{
    /**
     * @param CacheItemPoolInterface $cache 冪等性記録の保管先 (既定は cache.app)
     * @param int                    $ttl   保管期間 (秒)。UCP は 24h 以上を要求するため既定 86400
     */
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly int $ttl = 86400,
    ) {
    }

    /**
     * Idempotency-Key を考慮してハンドラを実行する.
     *
     * @param string|null                                                 $key         Idempotency-Key ヘッダ値 (null/空ならキー無しとして都度実行)
     * @param string                                                      $requestHash リクエスト内容のハッシュ (異パラメータ再利用検知用)
     * @param callable(): array{status: int, body: array<string, mixed>} $compute     初回に実行するハンドラ
     *
     * @return array{status: int, body: array<string, mixed>, replayed: bool}
     *
     * @throws IdempotencyConflictException 同一キーが異なる requestHash で再利用された場合
     */
    public function execute(?string $key, string $requestHash, callable $compute): array
    {
        if ($key === null || $key === '') {
            $result = $compute();

            return ['status' => $result['status'], 'body' => $result['body'], 'replayed' => false];
        }

        $item = $this->cache->getItem($this->normalizeKey($key));
        if ($item->isHit()) {
            /** @var array{requestHash: string, status: int, body: array<string, mixed>} $stored */
            $stored = $item->get();
            if ($stored['requestHash'] !== $requestHash) {
                throw new IdempotencyConflictException(sprintf('Idempotency-Key "%s" was reused with different request parameters.', $key));
            }

            return ['status' => $stored['status'], 'body' => $stored['body'], 'replayed' => true];
        }

        $result = $compute();

        $item->set(['requestHash' => $requestHash, 'status' => $result['status'], 'body' => $result['body']]);
        $item->expiresAfter($this->ttl);
        $this->cache->save($item);

        return ['status' => $result['status'], 'body' => $result['body'], 'replayed' => false];
    }

    /**
     * リクエスト内容から安定したハッシュを生成する (キー再利用検知用).
     *
     * @param array<string, mixed> $payload
     */
    public function hashRequest(array $payload): string
    {
        return hash('sha256', (string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * PSR-6 で予約されている文字 ({}()/\@:) を避けるためキーをハッシュ化する.
     */
    private function normalizeKey(string $key): string
    {
        return 'agent_commerce_idempotency.'.hash('sha256', $key);
    }
}

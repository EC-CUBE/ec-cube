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

namespace Eccube\Tests\Service\AgentCommerce\Idempotency;

use Eccube\Service\AgentCommerce\Exception\IdempotencyConflictException;
use Eccube\Service\AgentCommerce\Idempotency\AgentCheckoutIdempotencyStore;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Layer 1/2 (Idempotency) tests for AgentCheckoutIdempotencyStore.
 *
 * - 同一キーの再送は副作用を再実行せずキャッシュ結果をリプレイする (MUST NOT re-execute)。
 * - 同一キーが異なるリクエスト内容で再利用された場合は 409 相当の例外。
 * - キー無し (null) は都度実行する。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout-rest.md (Idempotency-Key)
 */
final class AgentCheckoutIdempotencyStoreTest extends TestCase
{
    private AgentCheckoutIdempotencyStore $store;

    protected function setUp(): void
    {
        $this->store = new AgentCheckoutIdempotencyStore(new ArrayAdapter());
    }

    public function testReplaysSameKeyWithoutReexecutingSideEffects(): void
    {
        $calls = 0;
        $compute = function () use (&$calls): array {
            ++$calls;

            return ['status' => 201, 'body' => ['id' => 'cs_1']];
        };

        $first = $this->store->execute('key-1', 'hash-A', $compute);
        $second = $this->store->execute('key-1', 'hash-A', $compute);

        $this->assertSame(1, $calls, 'MUST NOT re-execute side effects on replay');
        $this->assertFalse($first['replayed']);
        $this->assertTrue($second['replayed']);
        $this->assertSame($first['body'], $second['body']);
        $this->assertSame(201, $second['status']);
    }

    public function testConflictOnSameKeyDifferentRequest(): void
    {
        $this->store->execute('key-2', 'hash-A', static fn (): array => ['status' => 201, 'body' => []]);

        $this->expectException(IdempotencyConflictException::class);
        $this->store->execute('key-2', 'hash-B', static fn (): array => ['status' => 201, 'body' => []]);
    }

    public function testNullKeyExecutesEveryTime(): void
    {
        $calls = 0;
        $compute = function () use (&$calls): array {
            ++$calls;

            return ['status' => 200, 'body' => []];
        };

        $this->store->execute(null, 'hash-A', $compute);
        $this->store->execute(null, 'hash-A', $compute);

        $this->assertSame(2, $calls, 'キー無しは冪等化せず都度実行する');
    }

    public function testHashRequestIsStableAcrossKeyOrder(): void
    {
        $this->assertSame(
            $this->store->hashRequest(['a' => 1, 'b' => 2]),
            $this->store->hashRequest(['a' => 1, 'b' => 2]),
        );
    }
}

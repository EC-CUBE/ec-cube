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

use Eccube\Entity\AgentCheckoutIdempotency;
use Eccube\Repository\AgentCheckoutIdempotencyRepository;
use Eccube\Service\AgentCommerce\Exception\IdempotencyConflictException;
use Eccube\Service\AgentCommerce\Idempotency\AgentCheckoutIdempotencyStore;
use Eccube\Tests\EccubeTestCase;

/**
 * Layer 1/2 (Idempotency) tests for AgentCheckoutIdempotencyStore (DB 一意制約ベース).
 *
 * - 同一キーの再送は副作用を再実行せず保存済みレスポンスをリプレイする (MUST NOT re-execute)。
 * - 同一キーが異なるリクエスト内容で再利用された場合は 409 相当の例外。
 * - 主体 (subject) で名前空間化し、別エージェントの越境リプレイを防ぐ。
 * - DB の一意制約により、ノードローカルな分散ロックや共有キャッシュ無しで二重実行を防ぐ。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout-rest.md (Idempotency-Key)
 */
final class AgentCheckoutIdempotencyStoreTest extends EccubeTestCase
{
    private ?AgentCheckoutIdempotencyStore $store = null;

    private ?AgentCheckoutIdempotencyRepository $repository = null;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var AgentCheckoutIdempotencyRepository $repository */
        $repository = $this->entityManager->getRepository(AgentCheckoutIdempotency::class);
        $this->repository = $repository;
        $this->store = new AgentCheckoutIdempotencyStore($this->entityManager, $this->repository);
    }

    /**
     * @return callable(): array{status: int, body: array<string, mixed>}
     */
    private function countingCompute(int &$calls, int $status = 201): callable
    {
        return function () use (&$calls, $status): array {
            ++$calls;

            return ['status' => $status, 'body' => ['order' => 'ord-'.$calls]];
        };
    }

    public function testReplaysSameKeyWithoutReexecutingSideEffects(): void
    {
        $key = 'idem-'.bin2hex(random_bytes(6));
        $hash = $this->store->hashRequest(['a' => 1]);
        $calls = 0;
        $compute = $this->countingCompute($calls);

        $first = $this->store->execute($key, $hash, $compute);
        $second = $this->store->execute($key, $hash, $compute);

        $this->assertSame(1, $calls, 'MUST NOT re-execute side effects on replay');
        $this->assertFalse($first['replayed']);
        $this->assertTrue($second['replayed'], '2 回目はリプレイ');
        $this->assertSame($first['body'], $second['body'], '同一レスポンスを返す');
        $this->assertSame(201, $second['status']);
    }

    public function testConflictOnSameKeyDifferentRequest(): void
    {
        $key = 'idem-'.bin2hex(random_bytes(6));
        $calls = 0;
        $this->store->execute($key, $this->store->hashRequest(['a' => 1]), $this->countingCompute($calls));

        $this->expectException(IdempotencyConflictException::class);
        $this->store->execute($key, $this->store->hashRequest(['a' => 2]), $this->countingCompute($calls));
    }

    public function testNullKeyExecutesEveryTime(): void
    {
        $calls = 0;
        $compute = $this->countingCompute($calls);
        $this->store->execute(null, 'h', $compute);
        $this->store->execute(null, 'h', $compute);
        $this->assertSame(2, $calls, 'キー無しは冪等化せず都度実行する');
    }

    public function testDifferentSubjectsDoNotCollide(): void
    {
        $key = 'idem-'.bin2hex(random_bytes(6));
        $hash = $this->store->hashRequest(['a' => 1]);
        $calls = 0;
        $compute = $this->countingCompute($calls);

        // 同一キーでも主体 (subject) が異なれば別記録として両方実行される (越境リプレイ防止)。
        $a = $this->store->execute($key, $hash, $compute, 'https://agent-a.example/.well-known/ucp');
        $b = $this->store->execute($key, $hash, $compute, 'https://agent-b.example/.well-known/ucp');

        $this->assertSame(2, $calls, '別主体は衝突せず各々実行される');
        $this->assertFalse($a['replayed']);
        $this->assertFalse($b['replayed']);
    }

    public function testComputeFailureRemovesReservationForRetry(): void
    {
        $key = 'idem-'.bin2hex(random_bytes(6));
        $hash = $this->store->hashRequest(['a' => 1]);

        try {
            $this->store->execute($key, $hash, function (): array {
                throw new \RuntimeException('payment failed');
            });
            self::fail('compute の例外は伝播する');
        } catch (\RuntimeException $e) {
            $this->assertSame('payment failed', $e->getMessage());
        }

        // 予約行が削除され、再試行可能な状態に戻っていること。
        $this->assertNotInstanceOf(AgentCheckoutIdempotency::class, $this->repository->findOneByKeyAndSubject($key, ''), 'compute 失敗時は予約が消えて再試行できる');
    }

    public function testInProgressReservationReturnsConflict(): void
    {
        $key = 'idem-'.bin2hex(random_bytes(6));
        $hash = $this->store->hashRequest(['a' => 1]);

        // レスポンス未確定 (処理中) の予約を先に作る。
        $reservation = (new AgentCheckoutIdempotency())
            ->setIdempotencyKey($key)
            ->setSubject('')
            ->setRequestHash($hash);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        // 同一キーの並行リクエストは「処理中」競合として弾かれる (副作用は実行しない)。
        $calls = 0;
        $this->expectException(IdempotencyConflictException::class);
        try {
            $this->store->execute($key, $hash, $this->countingCompute($calls));
        } finally {
            $this->assertSame(0, $calls, '処理中の並行リクエストは compute を実行しない');
        }
    }
}

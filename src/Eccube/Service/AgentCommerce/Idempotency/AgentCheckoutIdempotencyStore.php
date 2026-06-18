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

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\AgentCheckoutIdempotency;
use Eccube\Repository\AgentCheckoutIdempotencyRepository;
use Eccube\Service\AgentCommerce\Exception\IdempotencyConflictException;

/**
 * エージェントチェックアウトの Idempotency-Key 処理 (DB 一意制約ベース).
 *
 * 状態変更操作 (complete 等) で `Idempotency-Key` ヘッダが付与された場合、初回はハンドラを実行し
 * 結果を `dtb_agent_checkout_idempotency` へ保存する。同一キーの再送はハンドラを再実行せず保存済み
 * レスポンスをリプレイする (副作用の再実行なし)。
 *
 * **`(idempotency_key, subject)` の DB 一意制約**で直列化するため、Redis 等の共有キャッシュや
 * ノードローカルな分散ロックに依存せず、**マルチインスタンス (AWS 等) でも単一の共有 DB だけで**
 * 並行・越境の二重実行を防ぐ。`subject` は認証済みエージェント識別子で名前空間化する。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout-rest.md (Idempotency-Key)
 */
class AgentCheckoutIdempotencyStore
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AgentCheckoutIdempotencyRepository $repository,
    ) {
    }

    /**
     * Idempotency-Key を考慮してハンドラを実行する.
     *
     * @param string|null                                                 $key         Idempotency-Key ヘッダ値 (null/空ならキー無しとして都度実行)
     * @param string                                                      $requestHash リクエスト内容のハッシュ (異パラメータ再利用検知用)
     * @param callable(): array{status: int, body: array<string, mixed>} $compute     初回に実行するハンドラ
     * @param string|null                                                 $subject     認証済み主体 (UCP-Agent profile 等)。未認証は null
     *
     * @return array{status: int, body: array<string, mixed>, replayed: bool}
     *
     * @throws IdempotencyConflictException 同一キーの異パラメータ再利用、または処理中の並行リクエスト
     */
    public function execute(?string $key, string $requestHash, callable $compute, ?string $subject = null): array
    {
        if ($key === null || $key === '') {
            $result = $compute();

            return ['status' => $result['status'], 'body' => $result['body'], 'replayed' => false];
        }

        $subjectKey = $subject ?? '';

        // 既存記録があればリプレイ / 競合 / 処理中を判定する (逐次再送はここで完結)。
        $existing = $this->repository->findOneByKeyAndSubject($key, $subjectKey);
        if ($existing !== null) {
            return $this->replay($existing->getRequestHash(), $existing->hasResponse(), (int) $existing->getResponseStatus(), $existing->getResponseBody() ?? [], $requestHash);
        }

        // 予約行を INSERT。一意制約で並行・越境の二重実行を直列化する。
        $reservation = (new AgentCheckoutIdempotency())
            ->setIdempotencyKey($key)
            ->setSubject($subjectKey)
            ->setRequestHash($requestHash);
        try {
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            // 並行する別リクエストが先に予約した (flush 失敗で EM は閉じるため DBAL で直接読む)。
            return $this->replayFromDbal($key, $subjectKey, $requestHash);
        }

        // 予約を獲得 → compute を実行。失敗時は予約を消して再試行可能にする。
        try {
            $result = $compute();
        } catch (\Throwable $e) {
            $this->deleteReservation($key, $subjectKey);

            throw $e;
        }

        $reservation->setResponseStatus($result['status'])->setResponseBody($result['body']);
        $this->entityManager->flush();

        return ['status' => $result['status'], 'body' => $result['body'], 'replayed' => false];
    }

    /**
     * リクエスト内容から安定したハッシュを生成する (キー再利用検知用).
     *
     * @param array<string, mixed> $payload
     */
    public function hashRequest(array $payload): string
    {
        // キーの並び順に依存しない安定したハッシュにする (同一内容・異キー順を同一リクエストと扱う)。
        ksort($payload);

        return hash('sha256', (string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * 既存記録から リプレイ結果を返す。異パラメータ再利用・処理中は競合例外.
     *
     * @param array<string, mixed> $storedBody
     *
     * @return array{status: int, body: array<string, mixed>, replayed: bool}
     */
    private function replay(string $storedHash, bool $hasResponse, int $storedStatus, array $storedBody, string $requestHash): array
    {
        if ($storedHash !== $requestHash) {
            throw new IdempotencyConflictException('Idempotency-Key was reused with different request parameters.');
        }
        if (!$hasResponse) {
            throw new IdempotencyConflictException('A request with the same Idempotency-Key is currently being processed.');
        }

        return ['status' => $storedStatus, 'body' => $storedBody, 'replayed' => true];
    }

    /**
     * 一意制約違反 (EM が閉じた後) に DBAL で既存記録を読み、リプレイ結果を返す.
     *
     * @return array{status: int, body: array<string, mixed>, replayed: bool}
     */
    private function replayFromDbal(string $key, string $subject, string $requestHash): array
    {
        $row = $this->entityManager->getConnection()->fetchAssociative(
            'SELECT request_hash, response_status, response_body FROM dtb_agent_checkout_idempotency WHERE idempotency_key = :k AND subject = :s',
            ['k' => $key, 's' => $subject],
        );
        if ($row === false) {
            // 競合相手がロールバック等で消えた場合は処理中扱い (再試行を促す)。
            throw new IdempotencyConflictException('A request with the same Idempotency-Key is currently being processed.');
        }

        $hasResponse = $row['response_status'] !== null;
        $decoded = is_string($row['response_body'] ?? null) ? json_decode($row['response_body'], true) : null;

        return $this->replay(
            (string) ($row['request_hash'] ?? ''),
            $hasResponse,
            (int) ($row['response_status'] ?? 0),
            is_array($decoded) ? $decoded : [],
            $requestHash,
        );
    }

    /**
     * 予約行を削除する (compute 失敗時の再試行を可能にするため。EM 状態に依存しない DBAL 経由).
     */
    private function deleteReservation(string $key, string $subject): void
    {
        $this->entityManager->getConnection()->delete(
            'dtb_agent_checkout_idempotency',
            ['idempotency_key' => $key, 'subject' => $subject],
        );
    }
}

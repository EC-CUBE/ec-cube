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

namespace Eccube\Service\AgentCommerce\Catalog\Acp;

use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedException;

/**
 * ACP Feed API (OpenAI ホスト) への push クライアント抽象.
 *
 * Feed API は push モデル (Agent=OpenAI がホスト、加盟店がクライアント) であり、
 * 本体は outbound Bearer api_key で認証する (inbound OAuth2 / eccube-api4 とは無関係)。
 * 実機送出は GA 前のためテストではモック実装に差し替える。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml
 */
interface AcpFeedClientInterface
{
    /**
     * POST /feeds — 新規 feed を登録し FeedMetadata を返す.
     *
     * @param string|null $targetCountry ISO 3166-1 alpha-2 (任意)
     *
     * @return array<string, mixed> FeedMetadata {id, target_country?, updated_at?}
     *
     * @throws AcpFeedException 設定不備 / 機能無効 / HTTP エラー時
     */
    public function createFeed(?string $targetCountry = null): array;

    /**
     * GET /feeds/{id} — feed の現状 FeedMetadata を取得する.
     *
     * @return array<string, mixed> FeedMetadata
     *
     * @throws AcpFeedException
     */
    public function getFeed(string $id): array;

    /**
     * GET /feeds/{id}/products — feed の現状 Product 一覧を取得する.
     *
     * @return array<int, array<string, mixed>> Product 配列
     *
     * @throws AcpFeedException
     */
    public function getFeedProducts(string $id): array;

    /**
     * PATCH /feeds/{id}/products — Product を部分 upsert する.
     *
     * 送信前に各 Product を schema.feed.json で検証する (サーバ ack が {accepted:bool} と
     * 粗いため client 側検証が主)。
     *
     * @param array<int, array<string, mixed>> $products upsert 対象 Product 連想配列の配列
     *
     * @return bool サーバ応答の accepted フラグ
     *
     * @throws AcpFeedException
     */
    public function upsertProducts(string $id, array $products): bool;

    /**
     * 全置換 push — products.jsonl / metadata.json を生成し、POST /feeds で feed を登録する.
     *
     * jsonl / metadata の取り込み自体は OpenAI 側の責務であり、本メソッドは生成物と
     * 登録した FeedMetadata を返す。生成した metadata は push 前に schema 検証する。
     *
     * @return array{products_jsonl: string, metadata: array<string, mixed>, feed: array<string, mixed>, product_count: int}
     *
     * @throws AcpFeedException
     */
    public function pushFullReplacement(string $feedId, ?\DateTimeImmutable $updatedAt = null): array;
}

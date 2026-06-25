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

use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\AgentCommerce\AddressMappingService;
use Eccube\Service\AgentCommerce\Catalog\CatalogMapper;
use Eccube\Service\AgentCommerce\Catalog\CatalogProviderInterface;
use Eccube\Service\AgentCommerce\Catalog\Exception\AcpFeedException;

/**
 * ACP Feed のオフライン全置換成果物 (products.jsonl / metadata.json) を生成する.
 *
 * - products.jsonl: 公開中 Product を 1 行 1 JSON で書き出す (full replacement)。
 *   CatalogProvider をストリームで回し、メモリに全件を蓄積しない。
 * - metadata.json: FeedMetadata (id / target_country / updated_at)。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/openapi/openapi.feed.yaml
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/blob/main/spec/2026-04-17/json-schema/schema.feed.json
 */
class AcpFeedGenerator
{
    public function __construct(
        private readonly CatalogProviderInterface $catalogProvider,
        private readonly CatalogMapper $catalogMapper,
        private readonly AcpFeedProductSerializer $serializer,
        private readonly AddressMappingService $addressMappingService,
        private readonly BaseInfoRepository $baseInfoRepository,
    ) {
    }

    /**
     * 公開中 Product を 1 行 1 JSON (JSONL) で与えられたストリームへ書き出す.
     *
     * メモリ蓄積を避けるため CatalogProvider のジェネレータをそのまま消費し、
     * 1 件ずつ fwrite する。書き出した Product 数を返す。
     *
     * @param resource $stream 書き込み可能なストリーム (例: fopen('php://output','w'))
     *
     * @return int 書き出した Product 数
     */
    public function generateProductsJsonl($stream): int
    {
        if (!\is_resource($stream)) {
            throw new AcpFeedException('generateProductsJsonl() requires a writable stream resource.');
        }

        $written = 0;
        foreach ($this->catalogProvider->provideDisplayProducts() as $product) {
            $dto = $this->catalogMapper->mapProduct($product);
            if ($dto === null) {
                continue;
            }

            $line = json_encode(
                $this->serializer->serialize($dto),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            );

            if (fwrite($stream, $line."\n") === false) {
                throw new AcpFeedException('Failed to write a product line to the feed stream.');
            }

            ++$written;
        }

        return $written;
    }

    /**
     * FeedMetadata 連想配列を生成する.
     *
     * target_country は BaseInfo の国 (ISO numeric) を alpha-2 へ変換して設定する
     * (解決できない場合は省略 = schema 上 optional)。updated_at はテスト容易性のため
     * 引数で受けられるようにし、未指定なら呼び出し時刻 (now) を使う。
     *
     * @return array<string, mixed> {id, target_country?, updated_at}
     */
    public function generateMetadata(string $feedId, ?\DateTimeImmutable $updatedAt = null): array
    {
        if ($feedId === '') {
            throw new AcpFeedException('Feed id must not be empty when generating metadata.');
        }

        $updatedAt ??= new \DateTimeImmutable();

        $metadata = [
            'id' => $feedId,
            'updated_at' => $updatedAt->format(\DateTimeInterface::RFC3339),
        ];

        $targetCountry = $this->resolveTargetCountry();
        if ($targetCountry !== null) {
            $metadata['target_country'] = $targetCountry;
        }

        return $metadata;
    }

    /**
     * BaseInfo の国 (ISO numeric id) を alpha-2 へ変換する. 解決不能なら null.
     */
    private function resolveTargetCountry(): ?string
    {
        $baseInfo = $this->baseInfoRepository->get();

        $country = $baseInfo->getCountry();
        if ($country === null) {
            return null;
        }

        return $this->addressMappingService->getAlpha2FromCountryId($country->getId());
    }
}

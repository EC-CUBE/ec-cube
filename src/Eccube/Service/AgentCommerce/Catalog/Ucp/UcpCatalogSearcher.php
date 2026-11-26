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

use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Repository\ProductRepository;
use Eccube\Service\AgentCommerce\Catalog\CatalogMapper;

/**
 * UCP Catalog search の応答本文を生成する.
 *
 * search エンドポイントと、キャッシュの事前生成 (管理画面・CLI) はいずれも本クラスで本文を作る。
 * 事前生成は search と同じキャッシュキーへ格納するため、両者で商品の範囲・次ページ情報・件数上限が
 * 食い違うと、事前生成した本文が search の応答として配られてしまう (次ページが失われる等)。
 */
class UcpCatalogSearcher
{
    /** search のデフォルトページサイズ. */
    public const DEFAULT_LIMIT = 10;

    /** search のページサイズ上限 (実装側でクランプ). */
    public const MAX_LIMIT = 100;

    /** cursor offset の上限 (大 offset による高コストなページングを抑止). */
    public const MAX_OFFSET = 10000;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CatalogMapper $catalogMapper,
        private readonly UcpCatalogResponseBuilder $responseBuilder,
    ) {
    }

    /**
     * ページサイズを search と同じ範囲 (1〜MAX_LIMIT) に収める.
     */
    public static function clampLimit(int $limit): int
    {
        return max(1, min($limit, self::MAX_LIMIT));
    }

    /**
     * 公開商品を検索し、search の応答本文 (JSON) を返す.
     *
     * 1 件多く取得して次ページの有無を判定し、取得範囲を確定してから商品を写像する
     * (写像で除外された商品の分を後続の商品で補充しない。補充すると次ページと重複する)。
     *
     * @throws \JsonException
     */
    public function searchBody(?string $query, int $limit, int $offset): string
    {
        $limit = self::clampLimit($limit);
        $offset = max(0, min($offset, self::MAX_OFFSET));

        $products = $this->findDisplayProducts($query, $offset, $limit + 1);
        $hasNext = count($products) > $limit;
        if ($hasNext) {
            $products = array_slice($products, 0, $limit);
        }

        $items = [];
        foreach ($products as $product) {
            $item = $this->catalogMapper->mapProduct($product);
            if ($item !== null) {
                $items[] = $item;
            }
        }

        $pagination = ['has_next_page' => $hasNext];
        if ($hasNext) {
            $pagination['cursor'] = self::encodeCursor($offset + $limit);
        }

        return json_encode(
            $this->responseBuilder->buildSearchResponse($items, $pagination),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );
    }

    /**
     * cursor (本実装独自の不透明 offset) を生成する.
     */
    public static function encodeCursor(int $offset): string
    {
        return base64_encode('offset:'.$offset);
    }

    /**
     * cursor を offset へ戻す. 不正な cursor は 0、上限を超える offset は MAX_OFFSET に丸める.
     */
    public static function decodeCursor(string $cursor): int
    {
        $decoded = base64_decode($cursor, true);
        if ($decoded === false || !str_starts_with($decoded, 'offset:')) {
            return 0;
        }

        $offset = max(0, (int) substr($decoded, strlen('offset:')));

        return min($offset, self::MAX_OFFSET);
    }

    /**
     * 公開商品をフリーテキスト (name LIKE) で検索し、id 昇順でページング取得する.
     *
     * @return Product[]
     */
    private function findDisplayProducts(?string $query, int $offset, int $limit): array
    {
        $qb = $this->productRepository->createQueryBuilder('p')
            ->where('p.Status = :status')
            ->setParameter('status', ProductStatus::DISPLAY_SHOW)
            ->orderBy('p.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($query !== null && trim($query) !== '') {
            $escaped = str_replace(['%', '_'], ['\\%', '\\_'], trim($query));
            $qb->andWhere('p.name LIKE :keyword')
                ->setParameter('keyword', '%'.$escaped.'%');
        }

        /** @var Product[] $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}

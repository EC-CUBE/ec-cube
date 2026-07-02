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

namespace Eccube\Controller\AgentCommerce;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\ProductRepository;
use Eccube\Service\AgentCommerce\Catalog\AgentCatalogItemDto;
use Eccube\Service\AgentCommerce\Catalog\CatalogMapper;
use Eccube\Service\AgentCommerce\Catalog\ProductReferenceResolverInterface;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogCache;
use Eccube\Service\AgentCommerce\Catalog\Ucp\UcpCatalogResponseBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * UCP Catalog capability (pull / REST transport) のエンドポイント.
 *
 * 一次仕様 (v2026-04-08) ではいずれも POST の RPC 形式 (GET ではない):
 *   - POST /catalog/search  (dev.ucp.shopping.catalog.search / search_catalog)
 *   - POST /catalog/lookup  (dev.ucp.shopping.catalog.lookup / lookup_catalog)
 *   - POST /catalog/product (dev.ucp.shopping.catalog.lookup / get_product)
 *
 * UCP Catalog は公開商品データのため常時公開する (フラグで無効化しない)。
 * Catalog query は read-only のため RFC 9421 署名は OPTIONAL であり本実装では検証しない
 * (discovery の signing_keys は公開鍵のみ別途広告)。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/catalog_search.json
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/shopping/catalog_lookup.json
 */
class UcpCatalogController extends AbstractController
{
    /**
     * search のデフォルトページサイズ.
     */
    private const DEFAULT_LIMIT = 10;

    /**
     * search のページサイズ上限 (実装側でクランプ).
     */
    private const MAX_LIMIT = 100;

    /**
     * lookup で 1 リクエストに受け付ける ids[] の上限 (公開エンドポイントの負荷耐性).
     */
    private const MAX_LOOKUP_IDS = 100;

    /**
     * cursor offset の上限 (大 offset による高コストなページングを抑止).
     */
    private const MAX_OFFSET = 10000;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductReferenceResolverInterface $referenceResolver,
        private readonly CatalogMapper $catalogMapper,
        private readonly UcpCatalogResponseBuilder $responseBuilder,
        private readonly UcpCatalogCache $cache,
    ) {
    }

    /**
     * POST /catalog/search — フリーテキスト / フィルタ + ページングで公開商品を返す.
     */
    #[Route(path: '/catalog/search', name: 'agent_ucp_catalog_search', methods: ['POST'])]
    public function search(Request $request): Response
    {
        // UCP Catalog は公開商品データのため常時公開 (discovery と同様)。
        // TODO: ucp_catalog_requires_auth=true の場合 ucp:catalog scope での OAuth2 認証を要求する
        //       (ec-cube/api42 導入後に AgentCommerceOAuth2Authenticator 経由で検証)。現状は public read。

        $rawBody = $request->getContent();
        $payload = $this->decodeBody($rawBody);
        $key = $this->cache->buildKey('search', $rawBody);

        $body = $this->cache->getOrCompute($key, function () use ($payload): string {
            $query = isset($payload['query']) && is_string($payload['query']) ? $payload['query'] : null;
            [$limit, $offset] = $this->resolvePagination($payload);

            // 1 件多く取得して次ページ有無を判定する
            $products = $this->findDisplayProducts($query, $offset, $limit + 1);
            $hasNext = count($products) > $limit;
            if ($hasNext) {
                $products = array_slice($products, 0, $limit);
            }

            $items = $this->mapProducts($products);

            $pagination = ['has_next_page' => $hasNext];
            if ($hasNext) {
                $pagination['cursor'] = $this->encodeCursor($offset + $limit);
            }

            return $this->encodeJson(
                $this->responseBuilder->buildSearchResponse($items, $pagination)
            );
        });

        return $this->respond($request, $body);
    }

    /**
     * POST /catalog/lookup — ids[] (product ID / variant ID / SKU) を解決して返す.
     */
    #[Route(path: '/catalog/lookup', name: 'agent_ucp_catalog_lookup', methods: ['POST'])]
    public function lookup(Request $request): Response
    {
        $rawBody = $request->getContent();
        $payload = $this->decodeBody($rawBody);
        $key = $this->cache->buildKey('lookup', $rawBody);

        $body = $this->cache->getOrCompute($key, function () use ($payload): string {
            $ids = isset($payload['ids']) && is_array($payload['ids']) ? $payload['ids'] : [];

            // 入力 id を文字列へ正規化し重複排除した上で件数上限へクランプする (解決処理/DB アクセスの増大を抑止)。
            $normalizedIds = [];
            foreach ($ids as $id) {
                if (!is_string($id) && !is_int($id)) {
                    continue;
                }
                $normalizedIds[(string) $id] = true;
            }
            $normalizedIds = array_slice(array_keys($normalizedIds), 0, self::MAX_LOOKUP_IDS);

            $products = [];
            $seen = [];
            foreach ($normalizedIds as $id) {
                $product = $this->resolveProductByIdentifier($id);
                if ($product === null) {
                    continue;
                }
                $productId = $product->getId();
                if ($productId === null || isset($seen[$productId])) {
                    continue;
                }
                $seen[$productId] = true;
                $products[] = $product;
            }

            $items = $this->mapProducts($products);

            // TODO: UCP lookup_response.variants[] は lookup_variant であり input_correlation[]
            // (`inputs`, minItems:1, {id, match?}) を MUST とする (catalog_lookup.json)。
            // 要求 id と解決した variant の相関 (exact/featured) を出力する必要がある。
            // 現状は未出力 (UcpCatalogSchemaContractTest::testLookupResponseMatchesUcpSchema は incomplete)。
            return $this->encodeJson($this->responseBuilder->buildLookupResponse($items));
        });

        return $this->respond($request, $body);
    }

    /**
     * POST /catalog/product — id (product ID / variant ID) で単一商品を返す.
     *
     * 必須の product が解決できない場合は 404 (Error) を返す。
     */
    #[Route(path: '/catalog/product', name: 'agent_ucp_catalog_product', methods: ['POST'])]
    public function product(Request $request): Response
    {
        $rawBody = $request->getContent();
        $payload = $this->decodeBody($rawBody);

        $id = isset($payload['id']) && (is_string($payload['id']) || is_int($payload['id']))
            ? (string) $payload['id']
            : null;
        if ($id === null || $id === '') {
            throw new NotFoundHttpException('product not found');
        }

        $key = $this->cache->buildKey('product', $rawBody);

        // 解決不能は 404 とするためキャッシュ前に存在判定する
        $product = $this->resolveProductByIdentifier($id);
        if ($product === null) {
            throw new NotFoundHttpException('product not found');
        }
        $item = $this->catalogMapper->mapProduct($product);
        if ($item === null) {
            throw new NotFoundHttpException('product not found');
        }

        $body = $this->cache->getOrCompute(
            $key,
            fn (): string => $this->encodeJson($this->responseBuilder->buildProductResponse($item)),
        );

        return $this->respond($request, $body);
    }

    /**
     * リクエストボディ (JSON) を連想配列へデコードする. 空 / 不正は空配列扱い.
     *
     * @return array<string, mixed>
     */
    private function decodeBody(string $rawBody): array
    {
        if (trim($rawBody) === '') {
            return [];
        }

        $decoded = json_decode($rawBody, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * pagination リクエストから [limit, offset] を解決する.
     *
     * cursor は本実装独自の不透明 offset (base64) として扱う。
     *
     * @param array<string, mixed> $payload
     *
     * @return array{0: int, 1: int}
     */
    private function resolvePagination(array $payload): array
    {
        $pagination = isset($payload['pagination']) && is_array($payload['pagination'])
            ? $payload['pagination']
            : [];

        $limit = self::DEFAULT_LIMIT;
        if (isset($pagination['limit']) && is_int($pagination['limit']) && $pagination['limit'] >= 1) {
            $limit = min($pagination['limit'], self::MAX_LIMIT);
        }

        $offset = 0;
        if (isset($pagination['cursor']) && is_string($pagination['cursor']) && $pagination['cursor'] !== '') {
            $offset = $this->decodeCursor($pagination['cursor']);
        }

        return [$limit, $offset];
    }

    private function encodeCursor(int $offset): string
    {
        return base64_encode('offset:'.$offset);
    }

    private function decodeCursor(string $cursor): int
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

    /**
     * 識別子 (product ID / variant ID / SKU) から Product を解決する.
     *
     * 数値は product ID を優先し、見つからなければ variant (ProductClass) ID として解決する。
     * 数値以外は SKU (ProductClass.code) として解決する。
     */
    private function resolveProductByIdentifier(string $identifier): ?Product
    {
        if ($identifier === '') {
            return null;
        }

        if (ctype_digit($identifier)) {
            $product = $this->productRepository->find((int) $identifier);
            if ($product !== null) {
                return $product;
            }

            $productClass = $this->referenceResolver->resolveByProductClassId((int) $identifier);
            if ($productClass !== null) {
                return $productClass->getProduct();
            }

            return null;
        }

        $productClass = $this->referenceResolver->resolveBySku($identifier);

        return $productClass?->getProduct();
    }

    /**
     * Product 配列を AgentCatalogItemDto 配列へ写す (非公開 / 出力不可は除外).
     *
     * @param Product[] $products
     *
     * @return AgentCatalogItemDto[]
     */
    private function mapProducts(array $products): array
    {
        $items = [];
        foreach ($products as $product) {
            $item = $this->catalogMapper->mapProduct($product);
            if ($item !== null) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * 連想配列を JSON 文字列へエンコードする.
     *
     * @param array<string, mixed> $data
     */
    private function encodeJson(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * JSON 本文を返す. Accept-Encoding に gzip があれば gzip 圧縮する.
     */
    private function respond(Request $request, string $body): Response
    {
        $acceptEncoding = (string) $request->headers->get('Accept-Encoding', '');
        if (str_contains($acceptEncoding, 'gzip')) {
            $compressed = gzencode($body, 6);
            if ($compressed !== false) {
                return new Response($compressed, JsonResponse::HTTP_OK, [
                    'Content-Type' => 'application/json',
                    'Content-Encoding' => 'gzip',
                    'Vary' => 'Accept-Encoding',
                ]);
            }
        }

        return new Response($body, JsonResponse::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}

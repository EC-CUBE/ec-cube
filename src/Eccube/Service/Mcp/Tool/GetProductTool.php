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

namespace Eccube\Service\Mcp\Tool;

use Eccube\Entity\Product;
use Eccube\Repository\ProductRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 商品詳細取得ツール (`get_product`)。
 *
 * 必要 scope: `mcp:product:read`。 商品 ID または商品コード (`ProductClass.code`) で検索し、
 * Api44 の allow_list に列挙された項目のみを返す (規格 / 画像 / カテゴリ等の関連も allow_list 経由)。
 */
final readonly class GetProductTool
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 商品 ID または商品コードから商品詳細を取得する。
     *
     * @param int|null    $id   商品 ID (id, code いずれか必須)
     * @param string|null $code 商品コード (ProductClass.code、 id, code いずれか必須)
     *
     * @return array<string, mixed> 見つからなければ空配列
     */
    #[McpTool(
        name: 'get_product',
        description: 'EC-CUBE の商品 ID または商品コードから商品詳細 (規格 / 画像 / カテゴリ等を含む) を取得する。 読み取り専用。 必要 scope: mcp:product:read。',
    )]
    public function get(?int $id = null, ?string $code = null): array
    {
        return $this->invoker->invoke(
            toolName: 'get_product',
            requiredScope: McpScope::ROLE_PRODUCT_READ,
            args: compact('id', 'code'),
            work: function () use ($id, $code): array {
                $product = $this->resolveProduct($id, $code);
                if (null === $product) {
                    return [
                        'data' => ['found' => false],
                        'summary' => ['found' => false],
                    ];
                }

                $data = $this->serializer->toArray($product);

                return [
                    'data' => $data,
                    'summary' => ['found' => true, 'product_id' => $product->getId()],
                ];
            },
        );
    }

    private function resolveProduct(?int $id, ?string $code): ?Product
    {
        if (null !== $id) {
            return $this->productRepository->find($id);
        }

        if (null !== $code && '' !== trim($code)) {
            return $this->productRepository->createQueryBuilder('p')
                ->innerJoin('p.ProductClasses', 'pc')
                ->andWhere('pc.code = :code')
                ->setParameter('code', $code)
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();
        }

        return null;
    }
}

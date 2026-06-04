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
use Eccube\Entity\ProductClass;
use Eccube\Repository\ProductRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 商品在庫取得ツール (`get_product_stock`)。
 *
 * 必要 scope: `mcp:product:read`。 商品単位 (規格なし) と商品規格単位 (`ProductClass`) の両方に対応。
 * 出力は ProductClass の allow_list (`stock`、 `stock_unlimited` 等) に従う。
 * `stock_unlimited = true` の規格は `stock` が null になる場合がある (在庫無制限の表現)。
 */
final readonly class GetProductStockTool
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 商品の在庫情報を取得する (規格あり / 規格なしの両対応)。
     *
     * @param int $productId 商品 ID
     *
     * @return array{summary:array<string, mixed>,items:list<array<string, mixed>>}
     */
    #[McpTool(
        name: 'get_product_stock',
        description: 'EC-CUBE の商品 (および商品規格) 単位の在庫数を取得する。 stock_unlimited フラグの規格は stock が null。 読み取り専用。 必要 scope: mcp:product:read。',
    )]
    public function get(int $productId): array
    {
        /** @var array{summary:array<string, mixed>,items:list<array<string, mixed>>} $result */
        $result = $this->invoker->invoke(
            toolName: 'get_product_stock',
            requiredScope: McpScope::ROLE_PRODUCT_READ,
            args: ['productId' => $productId],
            work: function () use ($productId): array {
                $product = $this->productRepository->find($productId);
                if (null === $product) {
                    return [
                        'data' => ['summary' => ['found' => false, 'total_classes' => 0], 'items' => []],
                        'summary' => ['found' => false],
                    ];
                }

                $classes = $this->visibleProductClasses($product);
                $items = $this->serializer->toArrayList($classes);

                $summary = $this->buildStockSummary($classes);

                return [
                    'data' => [
                        'summary' => $summary,
                        'items' => $items,
                    ],
                    'summary' => [
                        'found' => true,
                        'product_id' => $product->getId(),
                        'total_classes' => $summary['total_classes'],
                    ],
                ];
            },
        );

        return $result;
    }

    /**
     * @return list<ProductClass>
     */
    private function visibleProductClasses(Product $product): array
    {
        $visible = [];
        foreach ($product->getProductClasses() as $class) {
            if ($class->isVisible()) {
                $visible[] = $class;
            }
        }

        return $visible;
    }

    /**
     * @param list<ProductClass> $classes
     *
     * @return array{total_classes:int,total_stock:int|null,stock_unlimited:bool}
     */
    private function buildStockSummary(array $classes): array
    {
        $totalStock = 0;
        $stockUnlimited = false;
        foreach ($classes as $class) {
            if ($class->isStockUnlimited()) {
                $stockUnlimited = true;
                continue;
            }
            $stock = $class->getStock();
            if (null !== $stock) {
                $totalStock += (int) $stock;
            }
        }

        return [
            'total_classes' => \count($classes),
            'total_stock' => $stockUnlimited ? null : $totalStock,
            'stock_unlimited' => $stockUnlimited,
        ];
    }
}

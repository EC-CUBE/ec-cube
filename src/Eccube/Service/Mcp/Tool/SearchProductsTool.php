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

use Doctrine\ORM\Tools\Pagination\Paginator;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\Master\ProductStatusRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\McpSummaryFields;
use Eccube\Service\Mcp\ProductPriceStockSummarizer;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 商品検索ツール (`search_products`)。
 *
 * 必要 scope: `mcp:product:read`。 出力フィールドは Api44 の allow_list (`Eccube\Entity\Product`)
 * に列挙された項目のみ。 INNER JOIN による cartesian product を吸収するため Doctrine の
 * `Paginator` を使う (`fetchJoinCollection: true`)。
 */
final readonly class SearchProductsTool
{
    public function __construct(
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository,
        private ProductStatusRepository $productStatusRepository,
        private EntityArraySerializer $serializer,
        private ProductPriceStockSummarizer $priceStockSummarizer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 商品をキーワード / カテゴリ / 公開ステータス / 在庫数で検索する。
     *
     * @param string|null $keyword    ID または商品名 / 商品コードの部分一致 (任意)
     * @param int|null    $categoryId カテゴリ ID。 子カテゴリも対象 (任意)
     * @param int[]|null  $statusIds  商品ステータス ID の配列。 1=公開、 2=非公開、 3=廃止 (任意、 既定 [1])
     * @param int|null    $stockMin   在庫がこの値以上の表示規格を持つ商品のみ (任意)。 stockMax と併用時は同一規格が [stockMin, stockMax] に入る商品に限る
     * @param int|null    $stockMax   在庫がこの値以下の表示規格を持つ商品のみ (任意)。 stockMin と併用時は同一規格が [stockMin, stockMax] に入る商品に限る
     * @param int         $limit      取得件数。 1〜200 (既定 10)
     * @param int         $offset     スキップ件数 (既定 0)
     *
     * @return array{total:int,limit:int,offset:int,items:list<array<string, mixed>>}
     */
    #[McpTool(
        name: 'search_products',
        description: 'EC-CUBE の商品をキーワード / カテゴリ / 公開ステータス / 在庫数で検索し、商品一覧を返す。 読み取り専用。 必要 scope: mcp:product:read。',
    )]
    public function search(
        ?string $keyword = null,
        ?int $categoryId = null,
        ?array $statusIds = null,
        ?int $stockMin = null,
        ?int $stockMax = null,
        int $limit = 10,
        int $offset = 0,
    ): array {
        /** @var array{total:int,limit:int,offset:int,items:list<array<string, mixed>>} $result */
        $result = $this->invoker->invoke(
            toolName: 'search_products',
            args: compact('keyword', 'categoryId', 'statusIds', 'stockMin', 'stockMax', 'limit', 'offset'),
            work: function () use ($keyword, $categoryId, $statusIds, $stockMin, $stockMax, $limit, $offset): array {
                $limit = max(1, min(200, $limit));
                $offset = max(0, $offset);

                $searchData = $this->buildSearchData($keyword, $categoryId, $statusIds);

                // 明示指定した statusIds が 1 つも解決しないと、 admin 検索は status フィルタ落ちで
                // 全状態 (非公開・廃止含む) を返してしまう。 絞り込み意図を尊重し 0 件を返す。
                if (null !== $statusIds && !isset($searchData['status'])) {
                    return [
                        'data' => ['total' => 0, 'limit' => $limit, 'offset' => $offset, 'items' => []],
                        'summary' => ['total' => 0, 'returned' => 0],
                    ];
                }

                $qb = $this->productRepository->getQueryBuilderBySearchDataForAdmin($searchData);

                // 在庫での絞り込みは fetch-join した pc を直接制約すると、 Paginator(fetchJoinCollection)
                // が ProductClasses を「条件に合う規格だけ」部分ハイドレートし、 価格/在庫レンジ集計
                // (ProductPriceStockSummarizer) が条件外の規格を落としてレンジが縮む。 商品単位の EXISTS
                // 部分クエリで絞り込み、 pc の完全ハイドレート (=正しいレンジ) を保つ。
                //
                // min/max は 1 本の EXISTS にまとめ、 同一規格が両方を満たすこと (=「在庫が [stockMin, stockMax]
                // に入る規格を持つ商品」) を要求する。 別々の EXISTS だと min と max を別規格が満たしてもヒットし
                // レンジ交差になる。 visible = true は、 出力レンジ側 (ProductPriceStockSummarizer が非表示規格を
                // 除外して集計) と絞り込みの母集団を揃えるため (非表示規格だけが条件を満たす商品を除外する)。
                if (null !== $stockMin || null !== $stockMax) {
                    $dql = 'SELECT pcStock.id FROM '.ProductClass::class.' pcStock'
                        .' WHERE pcStock.Product = p AND pcStock.visible = true AND pcStock.stock_unlimited = false';
                    if (null !== $stockMin) {
                        $dql .= ' AND pcStock.stock >= :mcpStockMin';
                    }
                    if (null !== $stockMax) {
                        $dql .= ' AND pcStock.stock <= :mcpStockMax';
                    }
                    $qb->andWhere($qb->expr()->exists($dql));
                    if (null !== $stockMin) {
                        $qb->setParameter('mcpStockMin', $stockMin);
                    }
                    if (null !== $stockMax) {
                        $qb->setParameter('mcpStockMax', $stockMax);
                    }
                }

                $qb->setMaxResults($limit)->setFirstResult($offset);
                $paginator = new Paginator($qb, fetchJoinCollection: true);

                $total = $paginator->count();
                $items = [];
                foreach ($paginator as $product) {
                    /** @var Product $product */
                    // `+` は左辺優先。 サマリ (左) と price/stock (右) はキーが衝突しない前提。
                    // McpSummaryFields::PRODUCT に price/stock と同名キーを足さないこと (足すと集約値が消える)。
                    $items[] = $this->serializer->toSummary($product, McpSummaryFields::PRODUCT)
                        + $this->priceStockSummarizer->summarize($product);
                }

                return [
                    'data' => [
                        'total' => $total,
                        'limit' => $limit,
                        'offset' => $offset,
                        'items' => $items,
                    ],
                    'summary' => ['total' => $total, 'returned' => \count($items)],
                ];
            },
        );

        return $result;
    }

    /**
     * @param int[]|null $statusIds
     *
     * @return array<string, mixed>
     */
    private function buildSearchData(?string $keyword, ?int $categoryId, ?array $statusIds): array
    {
        $searchData = [];

        if (null !== $keyword && '' !== trim($keyword)) {
            $searchData['id'] = $keyword;
        }

        if (null !== $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            if (null !== $category) {
                $searchData['category_id'] = $category;
            }
        }

        $statusIds ??= [ProductStatus::DISPLAY_SHOW];
        $statuses = $this->productStatusRepository->findBy(['id' => $statusIds]);
        if ([] !== $statuses) {
            $searchData['status'] = $statuses;
        }

        return $searchData;
    }
}

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
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 注文検索ツール (`search_orders`)。
 *
 * 必要 scope: `mcp:order:read`。 出力フィールドは Api44 allow_list (`Eccube\Entity\Order`) の項目のみ。
 * PII (氏名・住所・電話・メール等) が含まれる点に注意 (allow_list で許可されている前提)。
 */
final readonly class SearchOrdersTool
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderStatusRepository $orderStatusRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 注文をキーワード / 注文番号 / ステータス / 期間 / 金額レンジ / 顧客 ID で検索する。
     *
     * @param string|null $keyword         注文者名 / 注文番号 / メール等の汎用検索 (任意)
     * @param string|null $orderNo         注文番号の部分一致 (任意)
     * @param int[]|null  $statusIds       OrderStatus ID の配列 (任意。 例: 1=新規受付、 5=対応中)
     * @param string|null $email           注文者メール部分一致 (任意)
     * @param int|null    $totalMin        支払合計の下限 (任意)
     * @param int|null    $totalMax        支払合計の上限 (任意)
     * @param string|null $orderDateFrom   注文日 (ISO 8601) の開始 (任意)
     * @param string|null $orderDateTo     注文日 (ISO 8601) の終了 (任意)
     * @param int|null    $customerId      Customer ID で絞り込み (任意)
     * @param int         $limit           取得件数 1〜200 (既定 20)
     * @param int         $offset          スキップ件数 (既定 0)
     *
     * @return array{total:int,limit:int,offset:int,items:list<array<string, mixed>>}
     */
    #[McpTool(
        name: 'search_orders',
        description: 'EC-CUBE の注文を キーワード / 注文番号 / ステータス / 期間 / 金額レンジ / 顧客 ID で検索する。 読み取り専用。 必要 scope: mcp:order:read。 PII を含み得る。',
    )]
    public function search(
        ?string $keyword = null,
        ?string $orderNo = null,
        ?array $statusIds = null,
        ?string $email = null,
        ?int $totalMin = null,
        ?int $totalMax = null,
        ?string $orderDateFrom = null,
        ?string $orderDateTo = null,
        ?int $customerId = null,
        int $limit = 20,
        int $offset = 0,
    ): array {
        /** @var array{total:int,limit:int,offset:int,items:list<array<string, mixed>>} $result */
        $result = $this->invoker->invoke(
            toolName: 'search_orders',
            args: compact('keyword', 'orderNo', 'statusIds', 'email', 'totalMin', 'totalMax', 'orderDateFrom', 'orderDateTo', 'customerId', 'limit', 'offset'),
            work: function () use ($keyword, $orderNo, $statusIds, $email, $totalMin, $totalMax, $orderDateFrom, $orderDateTo, $customerId, $limit, $offset): array {
                $limit = max(1, min(200, $limit));
                $offset = max(0, $offset);

                $searchData = $this->buildSearchData($keyword, $orderNo, $statusIds, $email, $totalMin, $totalMax, $orderDateFrom, $orderDateTo);
                $qb = $this->orderRepository->getQueryBuilderBySearchDataForAdmin($searchData);

                if (null !== $customerId) {
                    $qb->andWhere('o.Customer = :mcpCustomerId')
                        ->setParameter('mcpCustomerId', $customerId);
                }

                $qb->setMaxResults($limit)->setFirstResult($offset);
                $paginator = new Paginator($qb, fetchJoinCollection: true);

                $total = $paginator->count();
                $items = $this->serializer->toArrayList($paginator);

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
    private function buildSearchData(
        ?string $keyword,
        ?string $orderNo,
        ?array $statusIds,
        ?string $email,
        ?int $totalMin,
        ?int $totalMax,
        ?string $orderDateFrom,
        ?string $orderDateTo,
    ): array {
        $searchData = [];

        if (null !== $keyword && '' !== trim($keyword)) {
            $searchData['multi'] = $keyword;
        }
        if (null !== $orderNo && '' !== trim($orderNo)) {
            $searchData['order_no'] = $orderNo;
        }
        if (null !== $statusIds && [] !== $statusIds) {
            $statuses = $this->orderStatusRepository->findBy(['id' => $statusIds]);
            if ([] !== $statuses) {
                $searchData['status'] = $statuses;
            }
        }
        if (null !== $email && '' !== trim($email)) {
            $searchData['email'] = $email;
        }
        if (null !== $totalMin) {
            $searchData['payment_total_start'] = $totalMin;
        }
        if (null !== $totalMax) {
            $searchData['payment_total_end'] = $totalMax;
        }
        if (null !== $orderDateFrom && '' !== trim($orderDateFrom)) {
            $searchData['order_date_start'] = new \DateTime($orderDateFrom);
        }
        if (null !== $orderDateTo && '' !== trim($orderDateTo)) {
            $searchData['order_date_end'] = new \DateTime($orderDateTo);
        }

        return $searchData;
    }
}

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
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CustomerStatusRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\McpSummaryFields;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 会員検索ツール (`search_customers`)。
 *
 * 必要 scope: `mcp:customer:read`。 出力は Api44 allow_list (`Eccube\Entity\Customer`) の項目のみ。
 * 氏名・メール・電話・住所等の PII を含む点に注意 (scope 付与で運用統制)。
 *
 * `email` 専用キーは admin の検索仕様に存在しないため、 `keyword` (汎用) で兼ねる。
 */
final readonly class SearchCustomersTool
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private CustomerStatusRepository $customerStatusRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 会員を検索する。
     *
     * @param string|null $keyword         氏名 / メール / 顧客 ID 等の汎用検索 (任意)
     * @param string|null $phoneNumber     電話番号部分一致 (任意)
     * @param int[]|null  $statusIds       CustomerStatus ID 配列 (1=仮会員、 2=本会員、 3=退会、 任意)
     * @param string|null $createDateFrom  登録日 (ISO 8601) 開始 (任意)
     * @param string|null $createDateTo    登録日 (ISO 8601) 終了 (任意)
     * @param int|null    $buyTotalMin     購入合計の下限 (任意)
     * @param int|null    $buyTotalMax     購入合計の上限 (任意)
     * @param int|null    $buyTimesMin     購入回数の下限 (任意)
     * @param int|null    $buyTimesMax     購入回数の上限 (任意)
     * @param int         $limit           取得件数 1〜200 (既定 10)
     * @param int         $offset          スキップ件数 (既定 0)
     *
     * @return array{total:int,limit:int,offset:int,items:list<array<string, mixed>>}
     */
    #[McpTool(
        name: 'search_customers',
        description: 'EC-CUBE の会員を キーワード / 電話 / ステータス / 登録期間 / 購入合計 / 購入回数 で検索する。 読み取り専用。 必要 scope: mcp:customer:read。 氏名・メール・電話等の PII を含み得る。',
    )]
    public function search(
        ?string $keyword = null,
        ?string $phoneNumber = null,
        ?array $statusIds = null,
        ?string $createDateFrom = null,
        ?string $createDateTo = null,
        ?int $buyTotalMin = null,
        ?int $buyTotalMax = null,
        ?int $buyTimesMin = null,
        ?int $buyTimesMax = null,
        int $limit = 10,
        int $offset = 0,
    ): array {
        /** @var array{total:int,limit:int,offset:int,items:list<array<string, mixed>>} $result */
        $result = $this->invoker->invoke(
            toolName: 'search_customers',
            args: compact('keyword', 'phoneNumber', 'statusIds', 'createDateFrom', 'createDateTo', 'buyTotalMin', 'buyTotalMax', 'buyTimesMin', 'buyTimesMax', 'limit', 'offset'),
            work: function () use ($keyword, $phoneNumber, $statusIds, $createDateFrom, $createDateTo, $buyTotalMin, $buyTotalMax, $buyTimesMin, $buyTimesMax, $limit, $offset): array {
                $limit = max(1, min(200, $limit));
                $offset = max(0, $offset);

                $searchData = $this->buildSearchData($keyword, $phoneNumber, $statusIds, $createDateFrom, $createDateTo, $buyTotalMin, $buyTotalMax, $buyTimesMin, $buyTimesMax);
                $qb = $this->customerRepository->getQueryBuilderBySearchData($searchData);
                $qb->setMaxResults($limit)->setFirstResult($offset);

                $paginator = new Paginator($qb, fetchJoinCollection: true);

                $total = $paginator->count();
                $items = $this->serializer->toSummaryList($paginator, McpSummaryFields::CUSTOMER);

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
        ?string $phoneNumber,
        ?array $statusIds,
        ?string $createDateFrom,
        ?string $createDateTo,
        ?int $buyTotalMin,
        ?int $buyTotalMax,
        ?int $buyTimesMin,
        ?int $buyTimesMax,
    ): array {
        $searchData = [];

        if (null !== $keyword && '' !== trim($keyword)) {
            $searchData['multi'] = $keyword;
        }
        if (null !== $phoneNumber && '' !== trim($phoneNumber)) {
            $searchData['phone_number'] = $phoneNumber;
        }
        if (null !== $statusIds && [] !== $statusIds) {
            $statuses = $this->customerStatusRepository->findBy(['id' => $statusIds]);
            if ([] !== $statuses) {
                $searchData['customer_status'] = $statuses;
            }
        }
        if (null !== $createDateFrom && '' !== trim($createDateFrom)) {
            $searchData['create_date_start'] = $this->parseSearchDate($createDateFrom, 'createDateFrom');
        }
        if (null !== $createDateTo && '' !== trim($createDateTo)) {
            $searchData['create_date_end'] = $this->parseSearchDate($createDateTo, 'createDateTo');
        }
        if (null !== $buyTotalMin) {
            $searchData['buy_total_start'] = $buyTotalMin;
        }
        if (null !== $buyTotalMax) {
            $searchData['buy_total_end'] = $buyTotalMax;
        }
        if (null !== $buyTimesMin) {
            $searchData['buy_times_start'] = $buyTimesMin;
        }
        if (null !== $buyTimesMax) {
            $searchData['buy_times_end'] = $buyTimesMax;
        }

        return $searchData;
    }

    /**
     * MCP クライアント由来の日付文字列を `\DateTime` に変換する。
     * 不正な書式は `new \DateTime()` の不透明な例外ではなく、 引数名を示す `\InvalidArgumentException` に変換する。
     */
    private function parseSearchDate(string $value, string $field): \DateTime
    {
        try {
            return new \DateTime($value);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(sprintf('Invalid %s format: %s', $field, $value), 0, $e);
        }
    }
}

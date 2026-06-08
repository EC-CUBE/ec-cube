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
use Eccube\Repository\OrderRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\McpScope;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 会員の購入履歴取得ツール (`get_customer_orders`)。
 *
 * 必要 scope: `mcp:customer:read`。 指定会員の注文一覧を取得する。
 * 設計上、 顧客領域 scope で受注一覧を扱う点に注意 (注文明細は概要のみ、 `mcp:order:read` で個別 Order 詳細)。
 */
final readonly class GetCustomerOrdersTool
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private OrderRepository $orderRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 指定会員の注文一覧を取得する。
     *
     * @param int $customerId 会員 ID
     * @param int $limit      取得件数 1〜200 (既定 20)
     * @param int $offset     スキップ件数 (既定 0)
     *
     * @return array{customer_id:int|null,total:int,limit:int,offset:int,items:list<array<string, mixed>>}
     */
    #[McpTool(
        name: 'get_customer_orders',
        description: 'EC-CUBE の指定会員の購入履歴 (注文一覧) を取得する。 読み取り専用。 必要 scope: mcp:customer:read。',
    )]
    public function get(int $customerId, int $limit = 20, int $offset = 0): array
    {
        /** @var array{customer_id:int|null,total:int,limit:int,offset:int,items:list<array<string, mixed>>} $result */
        $result = $this->invoker->invoke(
            toolName: 'get_customer_orders',
            requiredScope: McpScope::ROLE_CUSTOMER_READ,
            args: ['customerId' => $customerId, 'limit' => $limit, 'offset' => $offset],
            work: function () use ($customerId, $limit, $offset): array {
                $limit = max(1, min(200, $limit));
                $offset = max(0, $offset);

                $customer = $this->customerRepository->find($customerId);
                if (null === $customer) {
                    return [
                        'data' => [
                            'customer_id' => null,
                            'total' => 0,
                            'limit' => $limit,
                            'offset' => $offset,
                            'items' => [],
                        ],
                        'summary' => ['found' => false],
                    ];
                }

                $qb = $this->orderRepository->getQueryBuilderByCustomer($customer);
                $qb->setMaxResults($limit)->setFirstResult($offset);
                $paginator = new Paginator($qb, fetchJoinCollection: true);

                $total = $paginator->count();
                $items = $this->serializer->toArrayList($paginator);

                return [
                    'data' => [
                        'customer_id' => $customer->getId(),
                        'total' => $total,
                        'limit' => $limit,
                        'offset' => $offset,
                        'items' => $items,
                    ],
                    'summary' => [
                        'found' => true,
                        'customer_id' => $customer->getId(),
                        'total' => $total,
                        'returned' => \count($items),
                    ],
                ];
            },
        );

        return $result;
    }
}

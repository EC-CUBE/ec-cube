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

use Eccube\Entity\Order;
use Eccube\Repository\OrderRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 注文詳細取得ツール (`get_order`)。
 *
 * 必要 scope: `mcp:order:read`。 注文 ID または注文番号で検索する。
 * 出力フィールドは Api44 allow_list (`Eccube\Entity\Order`) の項目のみ (PII 含み得る)。
 */
final readonly class GetOrderTool
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 注文 ID または注文番号から注文詳細を取得する。
     *
     * @param int|null    $id      注文 ID (id, orderNo いずれか必須)
     * @param string|null $orderNo 注文番号 (id, orderNo いずれか必須)
     *
     * @return array<string, mixed> 見つからなければ空配列
     */
    #[McpTool(
        name: 'get_order',
        description: 'EC-CUBE の注文 ID または注文番号から注文詳細 (明細 / 配送状況 / 支払状況等) を取得する。 読み取り専用。 必要 scope: mcp:order:read。 allow_list に氏名・住所等の PII が含まれ得る。',
    )]
    public function get(?int $id = null, ?string $orderNo = null): array
    {
        return $this->invoker->invoke(
            toolName: 'get_order',
            args: compact('id', 'orderNo'),
            work: function () use ($id, $orderNo): array {
                $order = $this->resolveOrder($id, $orderNo);
                if (null === $order) {
                    return [
                        'data' => ['found' => false],
                        'summary' => ['found' => false],
                    ];
                }

                return [
                    'data' => $this->serializer->toArray($order),
                    'summary' => ['found' => true, 'order_id' => $order->getId()],
                ];
            },
        );
    }

    private function resolveOrder(?int $id, ?string $orderNo): ?Order
    {
        if (null !== $id) {
            return $this->orderRepository->find($id);
        }

        if (null !== $orderNo && '' !== trim($orderNo)) {
            return $this->orderRepository->findOneBy(['order_no' => trim($orderNo)]);
        }

        return null;
    }
}

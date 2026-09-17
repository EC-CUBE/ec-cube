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

use Eccube\Repository\OrderRepository;
use Eccube\Service\Mcp\EntityArraySerializer;
use Eccube\Service\Mcp\ToolInvoker;
use Mcp\Capability\Attribute\McpTool;

/**
 * 配送情報取得ツール (`get_shipping`)。
 *
 * 必要 scope: `mcp:order:read`。 指定注文に紐づく配送 (Shipping、 複数あり得る) を返す。
 * 出力フィールドは Api44 allow_list (`Eccube\Entity\Shipping`) の項目のみ。
 * 出荷ステータス・配送日・追跡番号・配送先 (氏名・住所、 PII) が含まれる。
 */
final readonly class GetShippingTool
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityArraySerializer $serializer,
        private ToolInvoker $invoker,
    ) {
    }

    /**
     * 注文 ID に紐づく配送情報の一覧を取得する。
     *
     * @param int $orderId 注文 ID
     *
     * @return array{order_id:int|null,items:list<array<string, mixed>>}
     */
    #[McpTool(
        name: 'get_shipping',
        description: 'EC-CUBE の注文 ID に紐づく配送情報 (出荷ステータス / 配送日 / 追跡番号 / 配送先) の一覧を取得する。 読み取り専用。 必要 scope: mcp:order:read。 配送先住所等の PII が含まれ得る。',
    )]
    public function get(int $orderId): array
    {
        /** @var array{order_id:int|null,items:list<array<string, mixed>>} $result */
        $result = $this->invoker->invoke(
            toolName: 'get_shipping',
            args: ['orderId' => $orderId],
            work: function () use ($orderId): array {
                $order = $this->orderRepository->find($orderId);
                if (null === $order) {
                    return [
                        'data' => ['order_id' => null, 'items' => []],
                        'summary' => ['found' => false],
                    ];
                }

                $shippings = $order->getShippings()->toArray();
                $items = $this->serializer->toArrayList($shippings);

                return [
                    'data' => [
                        'order_id' => $order->getId(),
                        'items' => $items,
                    ],
                    'summary' => [
                        'found' => true,
                        'order_id' => $order->getId(),
                        'count' => \count($items),
                    ],
                ];
            },
        );

        return $result;
    }
}

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

namespace Eccube\Service\AgentCommerce\Payment;

use Eccube\Entity\Order;

/**
 * エージェントチェックアウトの決済ハンドラレジストリ.
 *
 * `agent_commerce.payment_handler` タグの付いたハンドラを集約し、Order の支払方法から適切な
 * ハンドラを解決する。具象ハンドラは決済プラグインがタグ付きサービスとして寄与する
 * (core には具象実装を持たない)。プロトコル固有の解決メソッド (UCP の handler_id 解決等) は
 * 各プロトコル PR (#6574 等) が本クラスへ追加する。
 */
class AgentCheckoutPaymentHandlerRegistry
{
    /**
     * @var list<AgentCheckoutPaymentHandlerInterface>
     */
    private readonly array $handlers;

    /**
     * @param iterable<AgentCheckoutPaymentHandlerInterface> $handlers タグ付きハンドラ群
     */
    public function __construct(iterable $handlers)
    {
        $this->handlers = is_array($handlers) ? array_values($handlers) : iterator_to_array($handlers, false);
    }

    /**
     * Order の支払方法を扱えるハンドラを返す (なければ null).
     */
    public function resolveForOrder(Order $order): ?AgentCheckoutPaymentHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($order)) {
                return $handler;
            }
        }

        return null;
    }
}

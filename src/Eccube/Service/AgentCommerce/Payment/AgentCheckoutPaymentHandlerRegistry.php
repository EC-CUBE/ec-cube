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
 * `agent_commerce.payment_handler` タグの付いたハンドラを集約し、Order の支払方法、
 * もしくは UCP の handler_id から適切なハンドラを解決する。具象ハンドラは決済プラグインが
 * タグ付きサービスとして寄与する (core には具象実装を持たない)。
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

    /**
     * UCP の handler_id に一致する UCP ハンドラを返す (なければ null).
     */
    public function resolveUcpByHandlerId(string $handlerId): ?UcpPaymentHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof UcpPaymentHandlerInterface && $handler->getHandlerId() === $handlerId) {
                return $handler;
            }
        }

        return null;
    }

    /**
     * 登録済みの UCP ハンドラを返す (discovery の payment_handlers 広告に使用).
     *
     * @return list<UcpPaymentHandlerInterface>
     */
    public function ucpHandlers(): array
    {
        return array_values(array_filter(
            $this->handlers,
            static fn (AgentCheckoutPaymentHandlerInterface $handler): bool => $handler instanceof UcpPaymentHandlerInterface
        ));
    }
}

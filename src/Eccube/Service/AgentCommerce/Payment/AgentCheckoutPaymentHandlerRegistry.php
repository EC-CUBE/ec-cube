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
 * `agent_commerce.payment_handler` タグの付いたハンドラを集約し、Order の支払方法、もしくは
 * UCP の handler_id から適切なハンドラを解決する。具象ハンドラは決済プラグインがタグ付きサービスとして
 * 寄与する (core には具象実装を持たない)。
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
     *
     * 同一 handler_id を複数のハンドラが宣言している場合は、プラグイン競合による非決定的な
     * 決済ハンドラ選択を避けるため、null を返さず明示的に例外を投げる。
     *
     * @throws \RuntimeException 同一 handler_id が複数登録されている場合
     */
    public function resolveUcpByHandlerId(string $handlerId): ?UcpPaymentHandlerInterface
    {
        $matched = [];
        foreach ($this->handlers as $handler) {
            if ($handler instanceof UcpPaymentHandlerInterface && $handler->getHandlerId() === $handlerId) {
                $matched[] = $handler;
            }
        }

        if (count($matched) > 1) {
            throw new \RuntimeException(sprintf('Multiple UCP payment handlers are registered for handler_id "%s". Plugin payment handlers must declare a unique handler_id.', $handlerId));
        }

        return $matched[0] ?? null;
    }

    /**
     * handler_id に一致するハンドラ (ACP/UCP 横断) を返す (なければ null).
     *
     * ACP ({@link AcpPaymentHandlerInterface}) / UCP ({@link UcpPaymentHandlerInterface}) は
     * いずれも `getHandlerId()` を宣言する。{@link AgentPaymentMethodResolverInterface} が
     * エージェントの選んだ handler_id から Payment を解決するために用いる。
     *
     * UCP 同様、同一 handler_id を複数のハンドラが宣言している場合はプラグイン競合による
     * 非決定的な選択を避けるため明示的に例外を投げる (ACP 側にも一意性を強制する)。
     *
     * @throws \RuntimeException 同一 handler_id が複数登録されている場合
     */
    public function resolveByHandlerId(string $handlerId): ?AgentCheckoutPaymentHandlerInterface
    {
        $matched = [];
        foreach ($this->handlers as $handler) {
            if (($handler instanceof AcpPaymentHandlerInterface || $handler instanceof UcpPaymentHandlerInterface)
                && $handler->getHandlerId() === $handlerId) {
                $matched[] = $handler;
            }
        }

        if (count($matched) > 1) {
            throw new \RuntimeException(sprintf('Multiple agent payment handlers are registered for handler_id "%s". Plugin payment handlers must declare a unique handler_id.', $handlerId));
        }

        return $matched[0] ?? null;
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

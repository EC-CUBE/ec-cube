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

namespace Eccube\Service\AgentCommerce\Discovery;

/**
 * payment_handlers の既定実装. tagged service として寄与された個々のレジストリを
 * reverse-domain キーでマージして返す. 寄与が無ければ空配列 (= 空オブジェクト {}) を返す.
 *
 * 決済ハンドラプラグイン側は PaymentHandlerRegistryInterface を実装したサービスを
 * 'eccube.agent_commerce.payment_handler_registry' タグ付きで登録することで寄与できる.
 */
class EmptyPaymentHandlerRegistry implements PaymentHandlerRegistryInterface
{
    /**
     * @param iterable<PaymentHandlerRegistryInterface> $registries tagged service 群 (自身は含めない)
     */
    public function __construct(private readonly iterable $registries = [])
    {
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, list<array<string, mixed>>>
     */
    public function collect(): array
    {
        $handlers = [];
        foreach ($this->registries as $registry) {
            foreach ($registry->collect() as $key => $value) {
                $handlers[$key] = $value;
            }
        }

        return $handlers;
    }
}

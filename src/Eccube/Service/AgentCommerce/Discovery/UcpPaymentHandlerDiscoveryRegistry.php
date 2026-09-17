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

use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;

/**
 * 登録済みの UCP 決済ハンドラから UCP discovery profile の payment_handlers を自動生成する.
 *
 * 決済ハンドラプラグインは `agent_commerce.payment_handler` タグで {@link \Eccube\Service\AgentCommerce\Payment\UcpPaymentHandlerInterface}
 * を登録するだけで、checkout の解決 ({@link AgentCheckoutPaymentHandlerRegistry::resolveUcpByHandlerId()}) に加えて
 * discovery (`/.well-known/ucp` の `payment_handlers`) にも自動的に広告される (ゼロ設定)。
 *
 * UCP profile の payment_handlers は **reverse-domain キー** のレジストリで、値は
 * payment_handler オブジェクトの **配列** ({@see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/ucp.json
 * の `payment_handlers`、各エントリは `id`(必須)/`version`(必須) を持つ)。本実装は最小エントリ
 * (`id` = handler_id / `version` = UCP プロトコルバージョン) を生成する。
 *
 * **拡張点**: `available_instruments` / `config` / `spec` 等のリッチな descriptor が必要な場合は、
 * 決済ハンドラプラグインが {@link PaymentHandlerRegistryInterface} を実装したサービスを
 * `eccube.agent_commerce.payment_handler_registry` タグで寄与すればよい
 * ({@link EmptyPaymentHandlerRegistry} が同一 reverse-domain キーでマージし、後勝ちで上書きできる)。
 */
class UcpPaymentHandlerDiscoveryRegistry implements PaymentHandlerRegistryInterface
{
    public function __construct(
        private readonly AgentCheckoutPaymentHandlerRegistry $handlerRegistry,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $handlers = [];
        foreach ($this->handlerRegistry->ucpHandlers() as $handler) {
            $handlerId = $handler->getHandlerId();
            $handlers[$handlerId] = [
                [
                    'id' => $handlerId,
                    'version' => UcpProfileBuilder::UCP_VERSION,
                ],
            ];
        }

        return $handlers;
    }
}

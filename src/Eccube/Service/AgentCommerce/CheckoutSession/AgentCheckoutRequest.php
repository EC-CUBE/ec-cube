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

namespace Eccube\Service\AgentCommerce\CheckoutSession;

/**
 * チェックアウト見積要求 (プロトコル非依存の中立表現).
 *
 * 明細と購入者/配送先住所を保持する。`AgentCheckoutPurchaseFlowAdapter` がこれを
 * EC-CUBE の `Cart` → `Order` へ変換し、shopping flow で税・送料・在庫を再計算する。
 */
final readonly class AgentCheckoutRequest
{
    /**
     * @param array<int, AgentCheckoutLineItem> $lineItems
     */
    public function __construct(
        public array $lineItems,
        public ?AgentCheckoutAddress $buyer = null,
        public string $currencyCode = 'JPY',
        public ?string $protocol = null,
        public ?string $agentId = null,
    ) {
    }
}

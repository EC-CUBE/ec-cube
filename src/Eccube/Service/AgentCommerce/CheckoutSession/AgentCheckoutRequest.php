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
     * @param int|null                          $protocolId プロトコル種別マスタ (`AgentProtocol::ACP` 等) の id
     */
    public function __construct(
        public array $lineItems,
        public ?AgentCheckoutAddress $buyer = null,
        public string $currencyCode = 'JPY',
        public ?int $protocolId = null,
        public ?string $agentId = null,
    ) {
    }

    /**
     * 購入者/配送先住所だけを差し替えた複製を返す (正規化後の住所で見積るため).
     */
    public function withBuyer(?AgentCheckoutAddress $buyer): self
    {
        return new self($this->lineItems, $buyer, $this->currencyCode, $this->protocolId, $this->agentId);
    }
}

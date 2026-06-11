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

namespace Eccube\Service\AgentCommerce\Fulfillment;

/**
 * 配送方法に紐づく支払方法の選択肢 (中立表現).
 *
 * 代引手数料等は `chargeMinor` に minor unit (整数) で保持する。
 */
final readonly class FulfillmentPaymentOption
{
    public function __construct(
        public int $paymentId,
        public string $method,
        public int $chargeMinor,
        public string $currencyCode,
    ) {
    }
}

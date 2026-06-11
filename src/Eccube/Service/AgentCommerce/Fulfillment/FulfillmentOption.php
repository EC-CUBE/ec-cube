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
 * 配送方法の選択肢 (中立表現).
 *
 * EC-CUBE の `Delivery` 1 件に対応し、配送先 (`Pref`) に対する送料 (`shippingFeeMinor`)、
 * 配送日数 (`estimatedDeliveryDays`)、利用可能な支払方法 (`paymentOptions`) を保持する。
 * 金額はすべて minor unit (整数) で表現する。
 */
final class FulfillmentOption
{
    /**
     * @param array<int, FulfillmentPaymentOption> $paymentOptions
     */
    public function __construct(
        public readonly int $deliveryId,
        public readonly string $name,
        public readonly int $shippingFeeMinor,
        public readonly string $currencyCode,
        public readonly ?int $estimatedDeliveryDays,
        public readonly array $paymentOptions,
    ) {
    }
}

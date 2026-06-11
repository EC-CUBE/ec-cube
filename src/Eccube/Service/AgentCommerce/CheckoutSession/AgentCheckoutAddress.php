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
 * 購入者/配送先の住所 (プロトコル非依存の中立表現).
 *
 * `prefId` は EC-CUBE の `mtb_pref.id` (1-47)。プロトコル固有 Mapper が
 * 共通基盤の `AddressMappingService` を用いて region 名等から解決する。
 */
final class AgentCheckoutAddress
{
    public function __construct(
        public readonly ?string $name01 = null,
        public readonly ?string $name02 = null,
        public readonly ?string $kana01 = null,
        public readonly ?string $kana02 = null,
        public readonly ?string $companyName = null,
        public readonly ?string $postalCode = null,
        public readonly ?int $prefId = null,
        public readonly ?string $addr01 = null,
        public readonly ?string $addr02 = null,
        public readonly ?string $email = null,
        public readonly ?string $phoneNumber = null,
    ) {
    }
}

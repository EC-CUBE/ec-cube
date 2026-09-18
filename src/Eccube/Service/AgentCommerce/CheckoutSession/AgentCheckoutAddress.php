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
final readonly class AgentCheckoutAddress
{
    public function __construct(
        public ?string $name01 = null,
        public ?string $name02 = null,
        public ?string $kana01 = null,
        public ?string $kana02 = null,
        public ?string $companyName = null,
        public ?string $postalCode = null,
        public ?int $prefId = null,
        public ?string $addr01 = null,
        public ?string $addr02 = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
    ) {
    }
}

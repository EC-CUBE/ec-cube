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
 * チェックアウトの明細 1 行 (プロトコル非依存の中立表現).
 *
 * プロトコル固有 Mapper (#6776 ACP / #6574 UCP) が、各プロトコルの variant 識別子を
 * EC-CUBE の `ProductClass.id` へ解決した上で本 DTO を組み立てる。
 */
final class AgentCheckoutLineItem
{
    public function __construct(
        public readonly int $productClassId,
        public readonly int $quantity,
    ) {
    }
}

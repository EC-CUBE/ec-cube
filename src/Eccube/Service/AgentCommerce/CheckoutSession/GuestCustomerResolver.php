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

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Customer;

/**
 * 標準実装: 常にゲスト購入 (会員解決なし) として null を返す.
 *
 * 会員 ID 連携 ([EC-CUBE/eccube-api4#189]) が landing するまでの既定挙動。
 */
class GuestCustomerResolver implements CustomerResolverInterface
{
    public function resolve(CheckoutSession $session): ?Customer
    {
        return null;
    }
}

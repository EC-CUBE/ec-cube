<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service;

use Eccube\Entity\Order;

/**
 * @deprecated since 3.0.0, to be removed in 3.1
 */
class OrderService
{
    /**
     * @var ShoppingService
     */
    protected $shoppingService;

    /**
     * 販売種別を取得
     *
     * @param Order $Order
     *
     * @return \Eccube\Entity\Master\SaleType[]
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getSaleTypes(Order $Order)
    {
        return $Order->getSaleTypes();
    }
}

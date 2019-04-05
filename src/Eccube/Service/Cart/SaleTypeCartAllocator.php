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

namespace Eccube\Service\Cart;

use Eccube\Entity\CartItem;

/**
 * 販売種別ごとにカートを振り分けるCartItemAllocator
 */
class SaleTypeCartAllocator implements CartItemAllocator
{
    /**
     * 商品の振り分け先となるカートの識別子を決定します。
     *
     * @param CartItem $Item カート商品
     *
     * @return string
     */
    public function allocate(CartItem $Item)
    {
        $ProductClass = $Item->getProductClass();
        if ($ProductClass && $ProductClass->getSaleType()) {
            return (string) $ProductClass->getSaleType()->getId();
        }
        throw new \InvalidArgumentException('ProductClass/SaleType not found');
    }
}

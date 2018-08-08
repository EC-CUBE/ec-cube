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

namespace Eccube\Service\Cart;

use Eccube\Entity\CartItem;

/**
 * 商品をカートに振り分けるインターフェイス
 */
interface CartItemAllocator
{
    /**
     * 商品の振り分け先となるカートの識別子を決定します。
     *
     * @param CartItem $Item カート商品
     *
     * @return string
     */
    public function allocate(CartItem $Item);
}

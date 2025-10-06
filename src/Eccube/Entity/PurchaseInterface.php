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

namespace Eccube\Entity;

interface PurchaseInterface
{
    /**
     * 合計金額を設定します。
     *
     * @param int|float|string $total
     *
     * @return ItemHolderInterface
     */
    public function setTotal($total): ItemHolderInterface;

    /**
     * 合計金額を返す。
     *
     * @return string
     */
    public function getTotal(): string;

    /**
     * @return \Eccube\Service\PurchaseFlow\ItemCollection<int,ItemInterface>
     */
    public function getItems(): \Eccube\Service\PurchaseFlow\ItemCollection;
}

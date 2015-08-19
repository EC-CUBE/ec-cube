<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Service;

use Eccube\Application;
use Eccube\Entity\Order;

class OrderService
{
    /** @var \Eccube\Application */
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 合計数量を取得
     *
     * @param Order $Order
     * @return int
     */
    public function getTotalQuantity(Order $Order)
    {
        $totalQuantity = 0;
        foreach ($Order->getOrderDetails() as $OrderDetail) {
            $totalQuantity += $OrderDetail->getQuantity();
        }

        return $totalQuantity;
    }

    /**
     * 小計を取得
     *
     * @param Order $Order
     * @return int
     */
    public function getSubTotal(Order $Order)
    {
        $subTotal = 0;
        foreach ($Order->getOrderDetails() as $OrderDetail) {
            // 小計
            $subTotal += $OrderDetail->getPriceIncTax() * $OrderDetail->getQuantity();
        }

        return $subTotal;
    }

    /**
     * 消費税のみの小計を取得
     *
     * @param Order $Order
     * @return int
     */
    public function getTotalTax(Order $Order)
    {
        $tax = 0;
        foreach ($Order->getOrderDetails() as $OrderDetail) {
            // 消費税のみの小計
            $tax += ($OrderDetail->getPriceIncTax() - $OrderDetail->getPrice()) * $OrderDetail->getQuantity();
        }

        return $tax;
    }

    /**
     * 商品種別を取得
     *
     * @param Order $Order
     * @return array
     */
    public function getProductTypes(Order $Order)
    {

        $productTypes = array();
        foreach ($Order->getOrderDetails() as $OrderDetail) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $OrderDetail->getProductClass();
            $productTypes[] = $ProductClass->getProductType();
        }
        return array_unique($productTypes);

    }

}

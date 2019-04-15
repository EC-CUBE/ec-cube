<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Eccube\Entity\Cart;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;

/**
 * @deprecated since 3.0.0, to be removed in 3.1
 */
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
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getTotalQuantity(Order $Order)
    {
        return $Order->calculateTotalQuantity();
    }

    /**
     * 小計を取得
     *
     * @param Order $Order
     * @return int
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getSubTotal(Order $Order)
    {
        return $Order->calculateSubTotal();
    }

    /**
     * 消費税のみの小計を取得
     *
     * @param Order $Order
     * @return int
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getTotalTax(Order $Order)
    {
        return $Order->calculateTotalTax();
    }

    /**
     * 商品種別を取得
     *
     * @param Order $Order
     * @return array
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getProductTypes(Order $Order)
    {
        return $Order->getProductTypes();
    }

    /**
     * 下位互換用関数
     *
     * @return Order
     *
     * @see ShoppingService::newOrder()
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function newOrder()
    {
        return $this->app['eccube.service.shopping']->newOrder();
    }

    /**
     * 下位互換用関数
     *
     * @param $cartItems
     * @param Customer|null $Customer
     * @param $preOrderId
     * @return Order
     *
     * @see ShoppingService::createOrder()
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function registerPreOrderFromCartItems($cartItems, Customer $Customer = null, $preOrderId)
    {
        return $this->app['eccube.service.shopping']->createOrder($Customer);
    }

    /**
     * 下位互換用関数
     *
     * @param Order $Order
     * @param Cart $Cart
     * @return Order
     *
     * @see ShoppingService::getAmount()
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getAmount(Order $Order, Cart $Cart)
    {
        return $this->app['eccube.service.shopping']->getAmount($Order);
    }

    /**
     * 下位互換用関数
     *
     * @param $em トランザクション制御されているEntityManager
     * @param Order $Order 受注情報
     * @return bool true : 成功、false : 失敗
     *
     * @see ShoppingService::isOrderProduct()
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function isOrderProduct($em, Order $Order)
    {
        return $this->app['eccube.service.shopping']->isOrderProduct($em, $Order);
    }

    /**
     * 下位互換用関数
     *
     * @param $em トランザクション制御されているEntityManager
     * @param Order $Order 受注情報
     * @param $formData フォームデータ
     *
     * @see ShoppingService::setOrderUpdate()
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function setOrderUpdate($em, Order $Order, $formData)
    {
        $this->app['eccube.service.shopping']->setOrderUpdate($Order, $formData);
    }

    /**
     * 下位互換用関数
     *
     * @param $em トランザクション制御されているEntityManager
     * @param Order $Order 受注情報
     *
     * @see ShoppingService::setStockUpdate()
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function setStockUpdate($em, Order $Order)
    {
        $this->app['eccube.service.shopping']->setStockUpdate($em, $Order);
    }

    /**
     * 下位互換用関数
     *
     * @param $em トランザクション制御されているEntityManager
     * @param Order $Order 受注情報
     * @param Customer $user ログインユーザ
     *
     * @see ShoppingService::setCustomerUpdate()
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function setCustomerUpdate($em, Order $Order, Customer $user)
    {
        $this->app['eccube.service.shopping']->setCustomerUpdate($Order, $user);
    }


}

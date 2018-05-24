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

use Eccube\Entity\Cart;
use Eccube\Entity\Customer;
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
     * @return array
     *
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getSaleTypes(Order $Order)
    {
        return $Order->getSaleTypes();
    }

    /**
     * 下位互換用関数
     *
     * @return Order
     *
     * @see ShoppingService::newOrder()
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function newOrder()
    {
        return $this->shoppingService->newOrder();
    }

    /**
     * 下位互換用関数
     *
     * @param $cartItems
     * @param Customer|null $Customer
     * @param $preOrderId
     *
     * @return Order
     *
     * @see ShoppingService::createOrder()
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function registerPreOrderFromCartItems($cartItems, Customer $Customer = null, $preOrderId)
    {
        return $this->shoppingService->createOrder($Customer);
    }

    /**
     * 下位互換用関数
     *
     * @param Order $Order
     * @param Cart $Cart
     *
     * @return Order
     *
     * @see ShoppingService::getAmount()
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function getAmount(Order $Order, Cart $Cart)
    {
        return $this->shoppingService->getAmount($Order);
    }

    /**
     * 下位互換用関数
     *
     * @param $em トランザクション制御されているEntityManager
     * @param Order $Order 受注情報
     * @param $formData フォームデータ
     *
     * @see ShoppingService::setOrderUpdate()
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function setOrderUpdate($em, Order $Order, $formData)
    {
        $this->shoppingService->setOrderUpdate($Order, $formData);
    }

    /**
     * 下位互換用関数
     *
     * @param $em トランザクション制御されているEntityManager
     * @param Order $Order 受注情報
     * @param Customer $user ログインユーザ
     *
     * @see ShoppingService::setCustomerUpdate()
     * @deprecated since 3.0.0, to be removed in 3.1
     */
    public function setCustomerUpdate($em, Order $Order, Customer $user)
    {
        $this->shoppingService->setCustomerUpdate($Order, $user);
    }
}

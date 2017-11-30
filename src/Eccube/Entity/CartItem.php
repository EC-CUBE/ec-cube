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


namespace Eccube\Entity;

class CartItem extends \Eccube\Entity\AbstractEntity implements ItemInterface
{
    use PointRateTrait;

    private $price;
    private $quantity;
    private $ProductClass;
    private $product_class_id;

    /**
     * @deprecated
     */
    private $class_name;

    /**
     * @deprecated
     */
    private $class_id;

    /**
     * @deprecated
     */
    private $object;

    public function __construct()
    {
    }

    public function __sleep()
    {
        return array('product_class_id', 'price', 'quantity');
    }

    /**
     * @param  string   $class_name
     * @return CartItem
     * @deprecated
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;

        return $this;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * @param  string   $class_id
     * @return CartItem
     * @deprecated
     */
    public function setClassId($class_id)
    {
        $this->class_id = $class_id;

        return $this;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getClassId()
    {
        return $this->class_id;
    }

    /**
     * @param  integer  $price
     * @return CartItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param  integer  $quantity
     * @return CartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return integer
     */
    public function getTotalPrice()
    {
        return $this->getPrice() * $this->getQuantity();
    }

    /**
     * @param  object   $object
     * @return CartItem
     * @deprecated
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return object
     * @deprecated
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * 商品明細かどうか.
     *
     * @return boolean 商品明細の場合 true
     */
    public function isProduct()
    {
        return true;
    }

    /**
     * 送料明細かどうか.
     *
     * @return boolean 送料明細の場合 true
     */
    public function isDeliveryFee()
    {
        return false;
    }

    /**
     * 手数料明細かどうか.
     *
     * @return boolean 手数料明細の場合 true
     */
    public function isCharge()
    {
        return false;
    }

    /**
     * 値引き明細かどうか.
     *
     * @return boolean 値引き明細の場合 true
     */
    public function isDiscount()
    {
        return false;
    }

    /**
     * 税額明細かどうか.
     *
     * @return boolean 税額明細の場合 true
     */
    public function isTax()
    {
        return false;
    }

    public function getOrderItemType()
    {
        // TODO OrderItemType::PRODUCT
        $ItemType = new \Eccube\Entity\Master\OrderItemType();
        return $ItemType;
    }

    /**
     * @param ProductClass $ProductClass
     * @return $this
     */
    public function setProductClass(ProductClass $ProductClass)
    {
        $this->ProductClass = $ProductClass;

        $this->product_class_id = is_object($ProductClass) ?
            $ProductClass->getId() :
            null;

        return $this;
    }

    /**
     * @return ProductClass
     */
    public function getProductClass()
    {
        return $this->ProductClass;
    }

    /**
     * @return int|null
     */
    public function getProductClassId()
    {
        return $this->product_class_id;
    }

    public function getPriceIncTax()
    {
        // TODO ItemInterfaceに追加, Cart::priceは税込み金額が入っているので,フィールドを分ける必要がある
        return $this->price;
    }
}

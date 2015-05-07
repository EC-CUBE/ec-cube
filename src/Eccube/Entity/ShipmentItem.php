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

/**
 * ShipmentItem
 */
class ShipmentItem extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $shipping_id;

    /**
     * @var integer
     */
    private $product_class_id;

    /**
     * @var integer
     */
    private $order_id;

    /**
     * @var string
     */
    private $product_name;

    /**
     * @var string
     */
    private $product_code;

    /**
     * @var string
     */
    private $classcategory_name1;

    /**
     * @var string
     */
    private $classcategory_name2;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var \Eccube\Entity\Shipping
     */
    private $Shipping;

    /**
     * @var \Eccube\Entity\ProductClass
     */
    private $ProductClass;

    /**
     * Set shipping_id
     *
     * @param  integer      $shippingId
     * @return ShipmentItem
     */
    public function setShippingId($shippingId)
    {
        $this->shipping_id = $shippingId;

        return $this;
    }

    /**
     * Get shipping_id
     *
     * @return integer
     */
    public function getShippingId()
    {
        return $this->shipping_id;
    }

    /**
     * Set product_class_id
     *
     * @param  integer      $productClassId
     * @return ShipmentItem
     */
    public function setProductClassId($productClassId)
    {
        $this->product_class_id = $productClassId;

        return $this;
    }

    /**
     * Get product_class_id
     *
     * @return integer
     */
    public function getProductClassId()
    {
        return $this->product_class_id;
    }

    /**
     * Set order_id
     *
     * @param  integer      $orderId
     * @return ShipmentItem
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get order_id
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set product_name
     *
     * @param  string       $productName
     * @return ShipmentItem
     */
    public function setProductName($productName)
    {
        $this->product_name = $productName;

        return $this;
    }

    /**
     * Get product_name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * Set product_code
     *
     * @param  string       $productCode
     * @return ShipmentItem
     */
    public function setProductCode($productCode)
    {
        $this->product_code = $productCode;

        return $this;
    }

    /**
     * Get product_code
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->product_code;
    }

    /**
     * Set classcategory_name1
     *
     * @param  string       $classcategoryName1
     * @return ShipmentItem
     */
    public function setClasscategoryName1($classcategoryName1)
    {
        $this->classcategory_name1 = $classcategoryName1;

        return $this;
    }

    /**
     * Get classcategory_name1
     *
     * @return string
     */
    public function getClasscategoryName1()
    {
        return $this->classcategory_name1;
    }

    /**
     * Set classcategory_name2
     *
     * @param  string       $classcategoryName2
     * @return ShipmentItem
     */
    public function setClasscategoryName2($classcategoryName2)
    {
        $this->classcategory_name2 = $classcategoryName2;

        return $this;
    }

    /**
     * Get classcategory_name2
     *
     * @return string
     */
    public function getClasscategoryName2()
    {
        return $this->classcategory_name2;
    }

    /**
     * Set price
     *
     * @param  string       $price
     * @return ShipmentItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantity
     *
     * @param  string       $quantity
     * @return ShipmentItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set Shipping
     *
     * @param  \Eccube\Entity\Shipping $shipping
     * @return ShipmentItem
     */
    public function setShipping(\Eccube\Entity\Shipping $shipping)
    {
        $this->Shipping = $shipping;

        return $this;
    }

    /**
     * Get Shipping
     *
     * @return \Eccube\Entity\Shipping
     */
    public function getShipping()
    {
        return $this->Shipping;
    }

    /**
     * Set ProductClass
     *
     * @param  \Eccube\Entity\ProductClass $productClass
     * @return ShipmentItem
     */
    public function setProductClass(\Eccube\Entity\ProductClass $productClass)
    {
        $this->ProductClass = $productClass;

        return $this;
    }

    /**
     * Get ProductClass
     *
     * @return \Eccube\Entity\ProductClass
     */
    public function getProductClass()
    {
        return $this->ProductClass;
    }
}

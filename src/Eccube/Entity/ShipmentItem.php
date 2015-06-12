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
    private $id;

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
    private $class_category_name1;

    /**
     * @var string
     */
    private $class_category_name2;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var \Eccube\Entity\Order
     */
    private $Order;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\ProductClass
     */
    private $ProductClass;

    /**
     * @var \Eccube\Entity\Shipping
     */
    private $Shipping;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set product_name
     *
     * @param string $productName
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
     * @param string $productCode
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
     * Set class_category_name1
     *
     * @param string $classCategoryName1
     * @return ShipmentItem
     */
    public function setClassCategoryName1($classCategoryName1)
    {
        $this->class_category_name1 = $classCategoryName1;

        return $this;
    }

    /**
     * Get class_category_name1
     *
     * @return string 
     */
    public function getClassCategoryName1()
    {
        return $this->class_category_name1;
    }

    /**
     * Set class_category_name2
     *
     * @param string $classCategoryName2
     * @return ShipmentItem
     */
    public function setClassCategoryName2($classCategoryName2)
    {
        $this->class_category_name2 = $classCategoryName2;

        return $this;
    }

    /**
     * Get class_category_name2
     *
     * @return string 
     */
    public function getClassCategoryName2()
    {
        return $this->class_category_name2;
    }

    /**
     * Set price
     *
     * @param string $price
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
     * @param string $quantity
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
     * Set Order
     *
     * @param \Eccube\Entity\Order $order
     * @return ShipmentItem
     */
    public function setOrder(\Eccube\Entity\Order $order)
    {
        $this->Order = $order;

        return $this;
    }

    /**
     * Get Order
     *
     * @return \Eccube\Entity\Order 
     */
    public function getOrder()
    {
        return $this->Order;
    }

    /**
     * Set Product
     *
     * @param \Eccube\Entity\Product $product
     * @return ShipmentItem
     */
    public function setProduct(\Eccube\Entity\Product $product)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get Product
     *
     * @return \Eccube\Entity\Product 
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * Set ProductClass
     *
     * @param \Eccube\Entity\ProductClass $productClass
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

    /**
     * Set Shipping
     *
     * @param \Eccube\Entity\Shipping $shipping
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
     * @var string
     */
    private $class_name1;

    /**
     * @var string
     */
    private $class_name2;


    /**
     * Set class_name1
     *
     * @param string $className1
     * @return ShipmentItem
     */
    public function setClassName1($className1)
    {
        $this->class_name1 = $className1;

        return $this;
    }

    /**
     * Get class_name1
     *
     * @return string 
     */
    public function getClassName1()
    {
        return $this->class_name1;
    }

    /**
     * Set class_name2
     *
     * @param string $className2
     * @return ShipmentItem
     */
    public function setClassName2($className2)
    {
        $this->class_name2 = $className2;

        return $this;
    }

    /**
     * Get class_name2
     *
     * @return string 
     */
    public function getClassName2()
    {
        return $this->class_name2;
    }
}

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
 * OrderDetail
 */
class OrderDetail extends \Eccube\Entity\AbstractEntity
{
    private $price_inc_tax = null;

    public function isPriceChange()
    {
        if (!$this->getProductClass()) {
            return true;
        } elseif ($this->getProductClass()->getPrice02IncTax() === $this->getPriceIncTax()) {
            return false;
        } else {
            return true;
        }
    }

    public function isEnable()
    {
        if ($this->getProductClass() && $this->getProductClass()->isEnable()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param \Eccube\Entity\BaseInfo
     * @return bool
     */
    public function isEffective(\Eccube\Entity\BaseInfo $BaseInfo)
    {
        $downloable = clone $this->getOrder()->getPaymentDate();
        if ($BaseInfo->getDownloadableDays()) {
            $downloable->add(new \DateInterval("P" . $BaseInfo->getDownloadableDays() . "D"));
        }

        if ($BaseInfo->getDownloadableDaysUnlimited() === 1 && $this->getOrder()->getPaymentDate()) {
            return true;
        } elseif (new \DateTime() <= $downloable) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isDownloadable()
    {
        // 販売価格が 0 円
        if ($this->getPrice() === 0) {
            return true;
        }
        // ダウンロード期限内かつ, 入金日あり
        elseif ($this->isEffective()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set price IncTax
     *
     * @param  string       $price_inc_tax
     * @return ProductClass
     */
    public function setPriceIncTax($price_inc_tax)
    {
        $this->price_inc_tax = $price_inc_tax;

        return $this;
    }

    /**
     * Get price IncTax
     *
     * @return string
     */
    public function getPriceIncTax()
    {
        return $this->price_inc_tax;
    }

    /**
     * @return integer
     */
    public function getTotalPrice()
    {
        return $this->getPriceIncTax() * $this->getQuantity();
    }

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
     * @var string
     */
    private $tax_rate;

    /**
     * @var integer
     */
    private $tax_rule;

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
     * @param  string      $productName
     * @return OrderDetail
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
     * @param  string      $productCode
     * @return OrderDetail
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
     * @param  string      $classCategoryName1
     * @return OrderDetail
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
     * @param  string      $classCategoryName2
     * @return OrderDetail
     */
    public function setClassCategoryName2($classCategoryName2)
    {
        $this->class_category_name2 = $classCategoryName2;

        return $this;
    }

    /**
     * Get classcategory_name2
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
     * @param  string      $price
     * @return OrderDetail
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
     * @param  string      $quantity
     * @return OrderDetail
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
     * Set tax_rate
     *
     * @param  string      $taxRate
     * @return OrderDetail
     */
    public function setTaxRate($taxRate)
    {
        $this->tax_rate = $taxRate;

        return $this;
    }

    /**
     * Get tax_rate
     *
     * @return string
     */
    public function getTaxRate()
    {
        return $this->tax_rate;
    }

    /**
     * Set tax_rule
     *
     * @param  integer     $taxRule
     * @return OrderDetail
     */
    public function setTaxRule($taxRule)
    {
        $this->tax_rule = $taxRule;

        return $this;
    }

    /**
     * Get tax_rule
     *
     * @return integer
     */
    public function getTaxRule()
    {
        return $this->tax_rule;
    }

    /**
     * Set Order
     *
     * @param  \Eccube\Entity\Order $order
     * @return OrderDetail
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
     * @param  \Eccube\Entity\Product $product
     * @return OrderDetail
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
     * @param  \Eccube\Entity\ProductClass $productClass
     * @return OrderDetail
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
     * @return OrderDetail
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
     * @return OrderDetail
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

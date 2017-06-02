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

use Eccube\Util\EntityUtil;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Doctrine\ORM\Mapping as ORM;

/**
 * ShipmentItem
 *
 * @ORM\Table(name="dtb_shipment_item")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\ShipmentItemRepository")
 */
class ShipmentItem extends \Eccube\Entity\AbstractEntity
{
    private $price_inc_tax = null;

    /**
     * Set price IncTax
     *
     * @param  string       $price_inc_tax
     * @return ShipmentItem
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
        $TaxDisplayType = $this->getTaxDisplayType();
        if (is_object($TaxDisplayType)) {
            switch ($TaxDisplayType->getId()) {
                // 税込価格
                case TaxDisplayType::INCLUDED:
                    $this->setPriceIncTax($this->getPrice());
                    break;
                    // 税別価格の場合は税額を加算する
                case TaxDisplayType::EXCLUDED:
                    // TODO 課税規則を考慮する
                    $this->setPriceIncTax($this->getPrice() + $this->getPrice() * $this->getTaxRate() / 100);
                    break;
            }
        }

        return $this->getPriceIncTax() * $this->getQuantity();
    }

    /**
     * @return integer
     */
    public function getOrderItemTypeId()
    {
        if (is_object($this->getOrderItemType())) {
            return $this->getOrderItemType()->getId();
        }
        return null;
    }
    /**
     * 商品明細かどうか.
     *
     * @return boolean 商品明細の場合 true
     */
    public function isProduct()
    {
        return ($this->getOrderItemTypeId() === OrderItemType::PRODUCT);
    }

    /**
     * 送料明細かどうか.
     *
     * @return boolean 送料明細の場合 true
     */
    public function isDeliveryFee()
    {
        return ($this->getOrderItemTypeId() === OrderItemType::DELIVERY_FEE);
    }

    /**
     * 手数料明細かどうか.
     *
     * @return boolean 手数料明細の場合 true
     */
    public function isCharge()
    {
        return ($this->getOrderItemTypeId() === OrderItemType::CHARGE);
    }

    /**
     * 値引き明細かどうか.
     *
     * @return boolean 値引き明細の場合 true
     */
    public function isDiscount()
    {
        return ($this->getOrderItemTypeId() === OrderItemType::DISCOUNT);
    }

    /**
     * 税額明細かどうか.
     *
     * @return boolean 税額明細の場合 true
     */
    public function isTax()
    {
        return ($this->getOrderItemTypeId() === OrderItemType::TAX);
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", length=255)
     */
    private $product_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_code", type="string", length=255, nullable=true)
     */
    private $product_code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="class_name1", type="string", length=255, nullable=true)
     */
    private $class_name1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="class_name2", type="string", length=255, nullable=true)
     */
    private $class_name2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="class_category_name1", type="string", length=255, nullable=true)
     */
    private $class_category_name1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="class_category_name2", type="string", length=255, nullable=true)
     */
    private $class_category_name2;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $price = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $quantity = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_rate", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $tax_rate = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tax_rule", type="smallint", nullable=true, options={"unsigned":true})
     */
    private $tax_rule;

    /**
     * @var \Eccube\Entity\Order
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Order", inversedBy="ShipmentItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_id", referencedColumnName="order_id")
     * })
     */
    private $Order;

    /**
     * @var \Eccube\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     * })
     */
    private $Product;

    /**
     * @var \Eccube\Entity\ProductClass
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\ProductClass")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_class_id", referencedColumnName="product_class_id")
     * })
     */
    private $ProductClass;

    /**
     * @var \Eccube\Entity\Shipping
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Shipping", inversedBy="ShipmentItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shipping_id", referencedColumnName="shipping_id")
     * })
     */
    private $Shipping;

    /**
     * @var \Eccube\Entity\Master\TaxType
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\TaxType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tax_type_id", referencedColumnName="id")
     * })
     */
    private $TaxType;

    /**
     * @var \Eccube\Entity\Master\TaxDisplayType
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\TaxDisplayType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tax_display_type_id", referencedColumnName="id")
     * })
     */
    private $TaxDisplayType;

    /**
     * @var \Eccube\Entity\Master\OrderItemType
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\OrderItemType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_item_type_id", referencedColumnName="id")
     * })
     */
    private $OrderItemType;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set productName.
     *
     * @param string $productName
     *
     * @return ShipmentItem
     */
    public function setProductName($productName)
    {
        $this->product_name = $productName;

        return $this;
    }

    /**
     * Get productName.
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * Set productCode.
     *
     * @param string|null $productCode
     *
     * @return ShipmentItem
     */
    public function setProductCode($productCode = null)
    {
        $this->product_code = $productCode;

        return $this;
    }

    /**
     * Get productCode.
     *
     * @return string|null
     */
    public function getProductCode()
    {
        return $this->product_code;
    }

    /**
     * Set className1.
     *
     * @param string|null $className1
     *
     * @return ShipmentItem
     */
    public function setClassName1($className1 = null)
    {
        $this->class_name1 = $className1;

        return $this;
    }

    /**
     * Get className1.
     *
     * @return string|null
     */
    public function getClassName1()
    {
        return $this->class_name1;
    }

    /**
     * Set className2.
     *
     * @param string|null $className2
     *
     * @return ShipmentItem
     */
    public function setClassName2($className2 = null)
    {
        $this->class_name2 = $className2;

        return $this;
    }

    /**
     * Get className2.
     *
     * @return string|null
     */
    public function getClassName2()
    {
        return $this->class_name2;
    }

    /**
     * Set classCategoryName1.
     *
     * @param string|null $classCategoryName1
     *
     * @return ShipmentItem
     */
    public function setClassCategoryName1($classCategoryName1 = null)
    {
        $this->class_category_name1 = $classCategoryName1;

        return $this;
    }

    /**
     * Get classCategoryName1.
     *
     * @return string|null
     */
    public function getClassCategoryName1()
    {
        return $this->class_category_name1;
    }

    /**
     * Set classCategoryName2.
     *
     * @param string|null $classCategoryName2
     *
     * @return ShipmentItem
     */
    public function setClassCategoryName2($classCategoryName2 = null)
    {
        $this->class_category_name2 = $classCategoryName2;

        return $this;
    }

    /**
     * Get classCategoryName2.
     *
     * @return string|null
     */
    public function getClassCategoryName2()
    {
        return $this->class_category_name2;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return ShipmentItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantity.
     *
     * @param string $quantity
     *
     * @return ShipmentItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set taxRate.
     *
     * @param string $taxRate
     *
     * @return ShipmentItem
     */
    public function setTaxRate($taxRate)
    {
        $this->tax_rate = $taxRate;

        return $this;
    }

    /**
     * Get taxRate.
     *
     * @return string
     */
    public function getTaxRate()
    {
        return $this->tax_rate;
    }

    /**
     * Set taxRule.
     *
     * @param int|null $taxRule
     *
     * @return ShipmentItem
     */
    public function setTaxRule($taxRule = null)
    {
        $this->tax_rule = $taxRule;

        return $this;
    }

    /**
     * Get taxRule.
     *
     * @return int|null
     */
    public function getTaxRule()
    {
        return $this->tax_rule;
    }

    /**
     * Set order.
     *
     * @param \Eccube\Entity\Order|null $order
     *
     * @return ShipmentItem
     */
    public function setOrder(\Eccube\Entity\Order $order = null)
    {
        $this->Order = $order;

        return $this;
    }

    /**
     * Get order.
     *
     * @return \Eccube\Entity\Order|null
     */
    public function getOrder()
    {
        return $this->Order;
    }

    public function getOrderId()
    {
        if (is_object($this->getOrder())) {
            return $this->getOrder()->getId();
        }
        return null;
    }

    /**
     * Set product.
     *
     * @param \Eccube\Entity\Product|null $product
     *
     * @return ShipmentItem
     */
    public function setProduct(\Eccube\Entity\Product $product = null)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get product.
     *
     * @return \Eccube\Entity\Product|null
     */
    public function getProduct()
    {
        if (EntityUtil::isEmpty($this->Product)) {
            return null;
        }
        return $this->Product;
    }

    /**
     * Set productClass.
     *
     * @param \Eccube\Entity\ProductClass|null $productClass
     *
     * @return ShipmentItem
     */
    public function setProductClass(\Eccube\Entity\ProductClass $productClass = null)
    {
        $this->ProductClass = $productClass;

        return $this;
    }

    /**
     * Get productClass.
     *
     * @return \Eccube\Entity\ProductClass|null
     */
    public function getProductClass()
    {
        return $this->ProductClass;
    }

    /**
     * Set shipping.
     *
     * @param \Eccube\Entity\Shipping|null $shipping
     *
     * @return ShipmentItem
     */
    public function setShipping(\Eccube\Entity\Shipping $shipping = null)
    {
        $this->Shipping = $shipping;

        return $this;
    }

    /**
     * Get shipping.
     *
     * @return \Eccube\Entity\Shipping|null
     */
    public function getShipping()
    {
        return $this->Shipping;
    }

    /**
     * Set taxType
     *
     * @param \Eccube\Entity\Master\TaxType $taxType
     *
     * @return ShipmentItem
     */
    public function setTaxType(\Eccube\Entity\Master\TaxType $taxType = null)
    {
        $this->TaxType = $taxType;

        return $this;
    }

    /**
     * Get taxType
     *
     * @return \Eccube\Entity\Master\TaxType
     */
    public function getTaxType()
    {
        return $this->TaxType;
    }

    /**
     * Set taxDisplayType
     *
     * @param \Eccube\Entity\Master\TaxDisplayType $taxDisplayType
     *
     * @return ShipmentItem
     */
    public function setTaxDisplayType(\Eccube\Entity\Master\TaxDisplayType $taxDisplayType = null)
    {
        $this->TaxDisplayType = $taxDisplayType;

        return $this;
    }

    /**
     * Get taxDisplayType
     *
     * @return \Eccube\Entity\Master\TaxDisplayType
     */
    public function getTaxDisplayType()
    {
        return $this->TaxDisplayType;
    }

    /**
     * Set orderItemType
     *
     * @param \Eccube\Entity\Master\OrderItemType $orderItemType
     *
     * @return ShipmentItem
     */
    public function setOrderItemType(\Eccube\Entity\Master\OrderItemType $orderItemType = null)
    {
        $this->OrderItemType = $orderItemType;

        return $this;
    }

    /**
     * Get orderItemType
     *
     * @return \Eccube\Entity\Master\OrderItemType
     */
    public function getOrderItemType()
    {
        return $this->OrderItemType;
    }
}

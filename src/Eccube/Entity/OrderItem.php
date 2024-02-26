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

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\TaxDisplayType;

if (!class_exists('\Eccube\Entity\OrderItem')) {
    /**
     * OrderItem
     *
     * @ORM\Table(name="dtb_order_item")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\OrderItemRepository")
     */
    class OrderItem extends \Eccube\Entity\AbstractEntity implements ItemInterface
    {
        use PointRateTrait;

        /**
         * Get price IncTax
         *
         * @return string
         */
        public function getPriceIncTax()
        {
            // 税表示区分が税込の場合は, priceに税込金額が入っている.
            if ($this->TaxDisplayType && $this->TaxDisplayType->getId() == TaxDisplayType::INCLUDED) {
                return $this->price;
            }

            return $this->price + $this->tax;
        }

        /**
         * @return integer
         */
        public function getTotalPrice()
        {
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
            return $this->getOrderItemTypeId() === OrderItemType::PRODUCT;
        }

        /**
         * 送料明細かどうか.
         *
         * @return boolean 送料明細の場合 true
         */
        public function isDeliveryFee()
        {
            return $this->getOrderItemTypeId() === OrderItemType::DELIVERY_FEE;
        }

        /**
         * 手数料明細かどうか.
         *
         * @return boolean 手数料明細の場合 true
         */
        public function isCharge()
        {
            return $this->getOrderItemTypeId() === OrderItemType::CHARGE;
        }

        /**
         * 値引き明細かどうか.
         *
         * @return boolean 値引き明細の場合 true
         */
        public function isDiscount()
        {
            return $this->getOrderItemTypeId() === OrderItemType::DISCOUNT;
        }

        /**
         * 税額明細かどうか.
         *
         * @return boolean 税額明細の場合 true
         */
        public function isTax()
        {
            return $this->getOrderItemTypeId() === OrderItemType::TAX;
        }

        /**
         * ポイント明細かどうか.
         *
         * @return boolean ポイント明細の場合 true
         */
        public function isPoint()
        {
            return $this->getOrderItemTypeId() === OrderItemType::POINT;
        }

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
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
         * @ORM\Column(name="price", type="decimal", precision=12, scale=2, options={"default":0})
         */
        private $price = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="quantity", type="decimal", precision=10, scale=0, options={"default":0})
         */
        private $quantity = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="tax", type="decimal", precision=10, scale=0, options={"default":0})
         */
        private $tax = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="tax_rate", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
         */
        private $tax_rate = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="tax_adjust", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
         */
        private $tax_adjust = 0;

        /**
         * @var int|null
         *
         * @ORM\Column(name="tax_rule_id", type="smallint", nullable=true, options={"unsigned":true})
         */
        private $tax_rule_id;

        /**
         * @var string|null
         *
         * @ORM\Column(name="currency_code", type="string", nullable=true)
         */
        private $currency_code;

        /**
         * @var string|null
         *
         * @ORM\Column(name="processor_name", type="string", nullable=true)
         */
        private $processor_name;

        /**
         * @var \Eccube\Entity\Order
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Order", inversedBy="OrderItems")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
         * })
         */
        private $Order;

        /**
         * @var \Eccube\Entity\Product
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
         * })
         */
        private $Product;

        /**
         * @var \Eccube\Entity\ProductClass
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ProductClass")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_class_id", referencedColumnName="id")
         * })
         */
        private $ProductClass;

        /**
         * @var \Eccube\Entity\Shipping
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Shipping", inversedBy="OrderItems")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="shipping_id", referencedColumnName="id")
         * })
         */
        private $Shipping;

        /**
         * @var \Eccube\Entity\Master\RoundingType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\RoundingType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="rounding_type_id", referencedColumnName="id")
         * })
         */
        private $RoundingType;

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
         * @return OrderItem
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
         * @return OrderItem
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
         * @return OrderItem
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
         * @return OrderItem
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
         * @return OrderItem
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
         * @return OrderItem
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
         * @return OrderItem
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
         * @return OrderItem
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
         * @return string
         */
        public function getTax()
        {
            return $this->tax;
        }

        /**
         * @param string $tax
         *
         * @return $this
         */
        public function setTax($tax)
        {
            $this->tax = $tax;

            return $this;
        }

        /**
         * Set taxRate.
         *
         * @param string $taxRate
         *
         * @return OrderItem
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
         * Set taxAdjust.
         *
         * @param string $tax_adjust
         *
         * @return OrderItem
         */
        public function setTaxAdjust($tax_adjust)
        {
            $this->tax_adjust = $tax_adjust;

            return $this;
        }

        /**
         * Get taxAdjust.
         *
         * @return string
         */
        public function getTaxAdjust()
        {
            return $this->tax_adjust;
        }

        /**
         * Set taxRuleId.
         *
         * @deprecated 税率設定は受注作成時に決定するため廃止予定
         *
         * @param int|null $taxRuleId
         *
         * @return OrderItem
         */
        public function setTaxRuleId($taxRuleId = null)
        {
            $this->tax_rule_id = $taxRuleId;

            return $this;
        }

        /**
         * Get taxRuleId.
         *
         * @deprecated 税率設定は受注作成時に決定するため廃止予定
         *
         * @return int|null
         */
        public function getTaxRuleId()
        {
            return $this->tax_rule_id;
        }

        /**
         * Get currencyCode.
         *
         * @return string
         */
        public function getCurrencyCode()
        {
            return $this->currency_code;
        }

        /**
         * Set currencyCode.
         *
         * @param string|null $currencyCode
         *
         * @return OrderItem
         */
        public function setCurrencyCode($currencyCode = null)
        {
            $this->currency_code = $currencyCode;

            return $this;
        }

        /**
         * Get processorName.
         *
         * @return string
         */
        public function getProcessorName()
        {
            return $this->processor_name;
        }

        /**
         * Set processorName.
         *
         * @param string|null $processorName
         *
         * @return $this
         */
        public function setProcessorName($processorName = null)
        {
            $this->processor_name = $processorName;

            return $this;
        }

        /**
         * Set order.
         *
         * @param \Eccube\Entity\Order|null $order
         *
         * @return OrderItem
         */
        public function setOrder(Order $order = null)
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
         * @return OrderItem
         */
        public function setProduct(Product $product = null)
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
            return $this->Product;
        }

        /**
         * Set productClass.
         *
         * @param \Eccube\Entity\ProductClass|null $productClass
         *
         * @return OrderItem
         */
        public function setProductClass(ProductClass $productClass = null)
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
         * @return OrderItem
         */
        public function setShipping(Shipping $shipping = null)
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
         * @return RoundingType
         */
        public function getRoundingType()
        {
            return $this->RoundingType;
        }

        /**
         * @param RoundingType $RoundingType
         */
        public function setRoundingType(RoundingType $RoundingType = null)
        {
            $this->RoundingType = $RoundingType;

            return $this;
        }

        /**
         * Set taxType
         *
         * @param \Eccube\Entity\Master\TaxType $taxType
         *
         * @return OrderItem
         */
        public function setTaxType(Master\TaxType $taxType = null)
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
         * @return OrderItem
         */
        public function setTaxDisplayType(TaxDisplayType $taxDisplayType = null)
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
         * @return OrderItem
         */
        public function setOrderItemType(OrderItemType $orderItemType = null)
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
}

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
use Eccube\Entity\Master\TaxType;
use Eccube\Repository\OrderItemRepository;

if (!class_exists(OrderItem::class)) {
    /**
     * OrderItem
     */
    #[ORM\Table(name: 'dtb_order_item')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: OrderItemRepository::class)]
    class OrderItem extends AbstractEntity implements ItemInterface
    {
        use PointRateTrait;

        /**
         * Get price IncTax
         *
         * @return string
         */
        public function getPriceIncTax(): string
        {
            // 税表示区分が税込の場合は, priceに税込金額が入っている.
            if ($this->TaxDisplayType && $this->TaxDisplayType->getId() == TaxDisplayType::INCLUDED) {
                return $this->price;
            }

            return bcadd($this->price, $this->tax, 2);
        }

        /**
         * @return string
         */
        public function getTotalPrice(): string
        {
            return bcmul($this->getPriceIncTax(), $this->getQuantity(), 2);
        }

        /**
         * @return int|null
         */
        public function getOrderItemTypeId(): ?int
        {
            if (is_object($this->getOrderItemType())) {
                return $this->getOrderItemType()->getId();
            }

            return null;
        }

        /**
         * 商品明細かどうか.
         *
         * @return bool 商品明細の場合 true
         */
        #[\Override]
        public function isProduct(): bool
        {
            return $this->getOrderItemTypeId() === OrderItemType::PRODUCT;
        }

        /**
         * 送料明細かどうか.
         *
         * @return bool 送料明細の場合 true
         */
        #[\Override]
        public function isDeliveryFee(): bool
        {
            return $this->getOrderItemTypeId() === OrderItemType::DELIVERY_FEE;
        }

        /**
         * 手数料明細かどうか.
         *
         * @return bool 手数料明細の場合 true
         */
        #[\Override]
        public function isCharge(): bool
        {
            return $this->getOrderItemTypeId() === OrderItemType::CHARGE;
        }

        /**
         * 値引き明細かどうか.
         *
         * @return bool 値引き明細の場合 true
         */
        #[\Override]
        public function isDiscount(): bool
        {
            return $this->getOrderItemTypeId() === OrderItemType::DISCOUNT;
        }

        /**
         * 税額明細かどうか.
         *
         * @return bool 税額明細の場合 true
         */
        #[\Override]
        public function isTax(): bool
        {
            return $this->getOrderItemTypeId() === OrderItemType::TAX;
        }

        /**
         * ポイント明細かどうか.
         *
         * @return bool ポイント明細の場合 true
         */
        #[\Override]
        public function isPoint(): bool
        {
            return $this->getOrderItemTypeId() === OrderItemType::POINT;
        }

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line property.unusedType, property.onlyRead */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'product_name', type: 'string', length: 255)]
        private $product_name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'product_code', type: 'string', length: 255, nullable: true)]
        private $product_code;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'class_name1', type: 'string', length: 255, nullable: true)]
        private $class_name1;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'class_name2', type: 'string', length: 255, nullable: true)]
        private $class_name2;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'class_category_name1', type: 'string', length: 255, nullable: true)]
        private $class_category_name1;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'class_category_name2', type: 'string', length: 255, nullable: true)]
        private $class_category_name2;

        /**
         * @var string
         */
        #[ORM\Column(name: 'price', type: 'decimal', precision: 12, scale: 2, options: ['default' => 0])]
        private $price = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'quantity', type: 'decimal', precision: 10, scale: 0, options: ['default' => 0])]
        private $quantity = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'tax', type: 'decimal', precision: 10, scale: 0, options: ['default' => 0])]
        private $tax = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'tax_rate', type: 'decimal', precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 0])]
        private $tax_rate = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'tax_adjust', type: 'decimal', precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 0])]
        private $tax_adjust = '0';

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'tax_rule_id', type: 'smallint', nullable: true, options: ['unsigned' => true])]
        private $tax_rule_id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'currency_code', type: 'string', nullable: true)]
        private $currency_code;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'processor_name', type: 'string', nullable: true)]
        private $processor_name;

        /**
         * @var Order|null
         */
        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'OrderItems')]
        #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
        private $Order;

        /**
         * @var Product|null
         */
        #[ORM\ManyToOne(targetEntity: Product::class)]
        #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
        private $Product;

        /**
         * @var ProductClass|null
         */
        #[ORM\ManyToOne(targetEntity: ProductClass::class)]
        #[ORM\JoinColumn(name: 'product_class_id', referencedColumnName: 'id')]
        private $ProductClass;

        /**
         * @var Shipping|null
         */
        #[ORM\ManyToOne(targetEntity: Shipping::class, inversedBy: 'OrderItems')]
        #[ORM\JoinColumn(name: 'shipping_id', referencedColumnName: 'id')]
        private $Shipping;

        /**
         * @var RoundingType|null
         */
        #[ORM\ManyToOne(targetEntity: RoundingType::class)]
        #[ORM\JoinColumn(name: 'rounding_type_id', referencedColumnName: 'id')]
        private $RoundingType;

        /**
         * @var TaxType|null
         */
        #[ORM\ManyToOne(targetEntity: TaxType::class)]
        #[ORM\JoinColumn(name: 'tax_type_id', referencedColumnName: 'id')]
        private $TaxType;

        /**
         * @var TaxDisplayType|null
         */
        #[ORM\ManyToOne(targetEntity: TaxDisplayType::class)]
        #[ORM\JoinColumn(name: 'tax_display_type_id', referencedColumnName: 'id')]
        private $TaxDisplayType;

        /**
         * @var OrderItemType|null
         */
        #[ORM\ManyToOne(targetEntity: OrderItemType::class)]
        #[ORM\JoinColumn(name: 'order_item_type_id', referencedColumnName: 'id')]
        private $OrderItemType;

        /**
         * Get id.
         *
         * @return int|null
         */
        public function getId(): ?int
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
        public function setProductName($productName): OrderItem
        {
            $this->product_name = $productName;

            return $this;
        }

        /**
         * Get productName.
         *
         * @return string
         */
        public function getProductName(): string
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
        public function setProductCode($productCode = null): OrderItem
        {
            $this->product_code = $productCode;

            return $this;
        }

        /**
         * Get productCode.
         *
         * @return string|null
         */
        public function getProductCode(): ?string
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
        public function setClassName1($className1 = null): OrderItem
        {
            $this->class_name1 = $className1;

            return $this;
        }

        /**
         * Get className1.
         *
         * @return string|null
         */
        public function getClassName1(): ?string
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
        public function setClassName2($className2 = null): OrderItem
        {
            $this->class_name2 = $className2;

            return $this;
        }

        /**
         * Get className2.
         *
         * @return string|null
         */
        public function getClassName2(): ?string
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
        public function setClassCategoryName1($classCategoryName1 = null): OrderItem
        {
            $this->class_category_name1 = $classCategoryName1;

            return $this;
        }

        /**
         * Get classCategoryName1.
         *
         * @return string|null
         */
        public function getClassCategoryName1(): ?string
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
        public function setClassCategoryName2($classCategoryName2 = null): OrderItem
        {
            $this->class_category_name2 = $classCategoryName2;

            return $this;
        }

        /**
         * Get classCategoryName2.
         *
         * @return string|null
         */
        public function getClassCategoryName2(): ?string
        {
            return $this->class_category_name2;
        }

        /**
         * Set price.
         *
         * @param string $price
         *
         * @return $this
         */
        public function setPrice($price): static
        {
            $this->price = $price;

            return $this;
        }

        /**
         * Get price.
         *
         * @return string|null
         */
        #[\Override]
        public function getPrice(): ?string
        {
            return $this->price;
        }

        /**
         * Set quantity.
         *
         * @param string $quantity
         *
         * @return $this
         */
        #[\Override]
        public function setQuantity($quantity): static
        {
            $this->quantity = $quantity;

            return $this;
        }

        /**
         * Get quantity.
         *
         * @return string
         */
        #[\Override]
        public function getQuantity(): string
        {
            return $this->quantity;
        }

        /**
         * @return string
         */
        public function getTax(): string
        {
            return $this->tax;
        }

        /**
         * @param string $tax
         *
         * @return $this
         */
        public function setTax($tax): static
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
        public function setTaxRate($taxRate): OrderItem
        {
            $this->tax_rate = $taxRate;

            return $this;
        }

        /**
         * Get taxRate.
         *
         * @return string
         */
        public function getTaxRate(): string
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
        public function setTaxAdjust($tax_adjust): OrderItem
        {
            $this->tax_adjust = $tax_adjust;

            return $this;
        }

        /**
         * Get taxAdjust.
         *
         * @return string
         */
        public function getTaxAdjust(): string
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
        public function setTaxRuleId($taxRuleId = null): OrderItem
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
        public function getTaxRuleId(): ?int
        {
            return $this->tax_rule_id;
        }

        /**
         * Get currencyCode.
         *
         * @return string
         */
        public function getCurrencyCode(): string
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
        public function setCurrencyCode($currencyCode = null): OrderItem
        {
            $this->currency_code = $currencyCode;

            return $this;
        }

        /**
         * Get processorName.
         *
         * @return string|null
         */
        public function getProcessorName(): ?string
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
        public function setProcessorName($processorName = null): static
        {
            $this->processor_name = $processorName;

            return $this;
        }

        /**
         * Set order.
         *
         * @param Order|null $order
         *
         * @return OrderItem
         */
        public function setOrder(?Order $order = null): OrderItem
        {
            $this->Order = $order;

            return $this;
        }

        /**
         * Get order.
         *
         * @return Order|null
         */
        public function getOrder(): ?Order
        {
            return $this->Order;
        }

        /**
         * @return int|null
         */
        public function getOrderId(): ?int
        {
            if (is_object($this->getOrder())) {
                return $this->getOrder()->getId();
            }

            return null;
        }

        /**
         * Set product.
         *
         * @param Product|null $product
         *
         * @return OrderItem
         */
        public function setProduct(?Product $product = null): OrderItem
        {
            $this->Product = $product;

            return $this;
        }

        /**
         * Get product.
         *
         * @return Product|null
         */
        public function getProduct(): ?Product
        {
            return $this->Product;
        }

        /**
         * Set productClass.
         *
         * @param ProductClass|null $productClass
         *
         * @return OrderItem
         */
        public function setProductClass(?ProductClass $productClass = null): OrderItem
        {
            $this->ProductClass = $productClass;

            return $this;
        }

        /**
         * Get productClass.
         *
         * @return ProductClass|null
         */
        #[\Override]
        public function getProductClass(): ?ProductClass
        {
            return $this->ProductClass;
        }

        /**
         * Set shipping.
         *
         * @param Shipping|null $shipping
         *
         * @return OrderItem
         */
        public function setShipping(?Shipping $shipping = null): OrderItem
        {
            $this->Shipping = $shipping;

            return $this;
        }

        /**
         * Get shipping.
         *
         * @return Shipping|null
         */
        public function getShipping(): ?Shipping
        {
            return $this->Shipping;
        }

        /**
         * @return RoundingType|null
         */
        public function getRoundingType(): ?RoundingType
        {
            return $this->RoundingType;
        }

        /**
         * @param RoundingType|null $RoundingType
         *
         * @return $this
         */
        public function setRoundingType(?RoundingType $RoundingType = null): static
        {
            $this->RoundingType = $RoundingType;

            return $this;
        }

        /**
         * Set taxType
         *
         * @param TaxType $taxType
         *
         * @return OrderItem
         */
        public function setTaxType(?TaxType $taxType = null): OrderItem
        {
            $this->TaxType = $taxType;

            return $this;
        }

        /**
         * Get taxType
         *
         * @return TaxType|null
         */
        public function getTaxType(): ?TaxType
        {
            return $this->TaxType;
        }

        /**
         * Set taxDisplayType
         *
         * @param TaxDisplayType $taxDisplayType
         *
         * @return OrderItem
         */
        public function setTaxDisplayType(?TaxDisplayType $taxDisplayType = null): OrderItem
        {
            $this->TaxDisplayType = $taxDisplayType;

            return $this;
        }

        /**
         * Get taxDisplayType
         *
         * @return TaxDisplayType|null
         */
        public function getTaxDisplayType(): ?TaxDisplayType
        {
            return $this->TaxDisplayType;
        }

        /**
         * Set orderItemType
         *
         * @param OrderItemType $orderItemType
         *
         * @return OrderItem
         */
        public function setOrderItemType(?OrderItemType $orderItemType = null): OrderItem
        {
            $this->OrderItemType = $orderItemType;

            return $this;
        }

        /**
         * Get orderItemType
         *
         * @return OrderItemType|null
         */
        #[\Override]
        public function getOrderItemType(): ?OrderItemType
        {
            return $this->OrderItemType;
        }
    }
}

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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Repository\OrderItemRepository;

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
     */
    public function getPriceIncTax(): string
    {
        // 税表示区分が税込の場合は, priceに税込金額が入っている.
        if ($this->TaxDisplayType && $this->TaxDisplayType->getId() == TaxDisplayType::INCLUDED) {
            return $this->price;
        }

        return bcadd((string) $this->price, (string) $this->tax, 2);
    }

    public function getTotalPrice(): string
    {
        return bcmul($this->getPriceIncTax(), $this->getQuantity(), 2);
    }

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

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'product_name', type: Types::STRING, length: 255)]
    private ?string $product_name = null;

    #[ORM\Column(name: 'product_code', type: Types::STRING, length: 255, nullable: true)]
    private ?string $product_code = null;

    #[ORM\Column(name: 'class_name1', type: Types::STRING, length: 255, nullable: true)]
    private ?string $class_name1 = null;

    #[ORM\Column(name: 'class_name2', type: Types::STRING, length: 255, nullable: true)]
    private ?string $class_name2 = null;

    #[ORM\Column(name: 'class_category_name1', type: Types::STRING, length: 255, nullable: true)]
    private ?string $class_category_name1 = null;

    #[ORM\Column(name: 'class_category_name2', type: Types::STRING, length: 255, nullable: true)]
    private ?string $class_category_name2 = null;

    #[ORM\Column(name: 'price', type: Types::DECIMAL, precision: 12, scale: 2, options: ['default' => 0])]
    private ?string $price = '0';

    #[ORM\Column(name: 'quantity', type: Types::DECIMAL, precision: 10, scale: 0, options: ['default' => 0])]
    private ?string $quantity = '0';

    #[ORM\Column(name: 'tax', type: Types::DECIMAL, precision: 10, scale: 0, options: ['default' => 0])]
    private ?string $tax = '0';

    #[ORM\Column(name: 'tax_rate', type: Types::DECIMAL, precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 0])]
    private ?string $tax_rate = '0';

    #[ORM\Column(name: 'tax_adjust', type: Types::DECIMAL, precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 0])]
    private ?string $tax_adjust = '0';

    #[ORM\Column(name: 'tax_rule_id', type: Types::SMALLINT, nullable: true, options: ['unsigned' => true])]
    private ?int $tax_rule_id = null;

    #[ORM\Column(name: 'currency_code', type: Types::STRING, nullable: true)]
    private ?string $currency_code = null;

    #[ORM\Column(name: 'processor_name', type: Types::STRING, nullable: true)]
    private ?string $processor_name = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'OrderItems')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    private ?Order $Order = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private ?Product $Product = null;

    #[ORM\ManyToOne(targetEntity: ProductClass::class)]
    #[ORM\JoinColumn(name: 'product_class_id', referencedColumnName: 'id')]
    private ?ProductClass $ProductClass = null;

    #[ORM\ManyToOne(targetEntity: Shipping::class, inversedBy: 'OrderItems')]
    #[ORM\JoinColumn(name: 'shipping_id', referencedColumnName: 'id')]
    private ?Shipping $Shipping = null;

    #[ORM\ManyToOne(targetEntity: RoundingType::class)]
    #[ORM\JoinColumn(name: 'rounding_type_id', referencedColumnName: 'id')]
    private ?RoundingType $RoundingType = null;

    #[ORM\ManyToOne(targetEntity: TaxType::class)]
    #[ORM\JoinColumn(name: 'tax_type_id', referencedColumnName: 'id')]
    private ?TaxType $TaxType = null;

    #[ORM\ManyToOne(targetEntity: TaxDisplayType::class)]
    #[ORM\JoinColumn(name: 'tax_display_type_id', referencedColumnName: 'id')]
    private ?TaxDisplayType $TaxDisplayType = null;

    #[ORM\ManyToOne(targetEntity: OrderItemType::class)]
    #[ORM\JoinColumn(name: 'order_item_type_id', referencedColumnName: 'id')]
    private ?OrderItemType $OrderItemType = null;

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set productName.
     */
    public function setProductName(string $productName): OrderItem
    {
        $this->product_name = $productName;

        return $this;
    }

    /**
     * Get productName.
     */
    public function getProductName(): string
    {
        return $this->product_name;
    }

    /**
     * Set productCode.
     */
    public function setProductCode(?string $productCode = null): OrderItem
    {
        $this->product_code = $productCode;

        return $this;
    }

    /**
     * Get productCode.
     */
    public function getProductCode(): ?string
    {
        return $this->product_code;
    }

    /**
     * Set className1.
     */
    public function setClassName1(?string $className1 = null): OrderItem
    {
        $this->class_name1 = $className1;

        return $this;
    }

    /**
     * Get className1.
     */
    public function getClassName1(): ?string
    {
        return $this->class_name1;
    }

    /**
     * Set className2.
     */
    public function setClassName2(?string $className2 = null): OrderItem
    {
        $this->class_name2 = $className2;

        return $this;
    }

    /**
     * Get className2.
     */
    public function getClassName2(): ?string
    {
        return $this->class_name2;
    }

    /**
     * Set classCategoryName1.
     */
    public function setClassCategoryName1(?string $classCategoryName1 = null): OrderItem
    {
        $this->class_category_name1 = $classCategoryName1;

        return $this;
    }

    /**
     * Get classCategoryName1.
     */
    public function getClassCategoryName1(): ?string
    {
        return $this->class_category_name1;
    }

    /**
     * Set classCategoryName2.
     */
    public function setClassCategoryName2(?string $classCategoryName2 = null): OrderItem
    {
        $this->class_category_name2 = $classCategoryName2;

        return $this;
    }

    /**
     * Get classCategoryName2.
     */
    public function getClassCategoryName2(): ?string
    {
        return $this->class_category_name2;
    }

    /**
     * Set price.
     *
     * @return $this
     */
    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
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
     */
    #[\Override]
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function getTax(): string
    {
        return $this->tax;
    }

    /**
     * @return $this
     */
    public function setTax(string $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Set taxRate.
     */
    public function setTaxRate(string $taxRate): OrderItem
    {
        $this->tax_rate = $taxRate;

        return $this;
    }

    /**
     * Get taxRate.
     */
    public function getTaxRate(): string
    {
        return $this->tax_rate;
    }

    /**
     * Set taxAdjust.
     */
    public function setTaxAdjust(string $tax_adjust): OrderItem
    {
        $this->tax_adjust = $tax_adjust;

        return $this;
    }

    /**
     * Get taxAdjust.
     */
    public function getTaxAdjust(): string
    {
        return $this->tax_adjust;
    }

    /**
     * Set taxRuleId.
     *
     * @deprecated 税率設定は受注作成時に決定するため廃止予定
     */
    public function setTaxRuleId(?int $taxRuleId = null): OrderItem
    {
        $this->tax_rule_id = $taxRuleId;

        return $this;
    }

    /**
     * Get taxRuleId.
     *
     * @deprecated 税率設定は受注作成時に決定するため廃止予定
     */
    public function getTaxRuleId(): ?int
    {
        return $this->tax_rule_id;
    }

    /**
     * Get currencyCode.
     */
    public function getCurrencyCode(): string
    {
        return $this->currency_code;
    }

    /**
     * Set currencyCode.
     */
    public function setCurrencyCode(?string $currencyCode = null): OrderItem
    {
        $this->currency_code = $currencyCode;

        return $this;
    }

    /**
     * Get processorName.
     */
    public function getProcessorName(): ?string
    {
        return $this->processor_name;
    }

    /**
     * Set processorName.
     *
     * @return $this
     */
    public function setProcessorName(?string $processorName = null): static
    {
        $this->processor_name = $processorName;

        return $this;
    }

    /**
     * Set order.
     */
    public function setOrder(?Order $order = null): OrderItem
    {
        $this->Order = $order;

        return $this;
    }

    /**
     * Get order.
     */
    public function getOrder(): ?Order
    {
        return $this->Order;
    }

    public function getOrderId(): ?int
    {
        if (is_object($this->getOrder())) {
            return $this->getOrder()->getId();
        }

        return null;
    }

    /**
     * Set product.
     */
    public function setProduct(?Product $product = null): OrderItem
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get product.
     */
    public function getProduct(): ?Product
    {
        return $this->Product;
    }

    /**
     * Set productClass.
     */
    public function setProductClass(?ProductClass $productClass = null): OrderItem
    {
        $this->ProductClass = $productClass;

        return $this;
    }

    /**
     * Get productClass.
     */
    #[\Override]
    public function getProductClass(): ?ProductClass
    {
        return $this->ProductClass;
    }

    /**
     * Set shipping.
     */
    public function setShipping(?Shipping $shipping = null): OrderItem
    {
        $this->Shipping = $shipping;

        return $this;
    }

    /**
     * Get shipping.
     */
    public function getShipping(): ?Shipping
    {
        return $this->Shipping;
    }

    public function getRoundingType(): ?RoundingType
    {
        return $this->RoundingType;
    }

    /**
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
     */
    public function setTaxType(?TaxType $taxType = null): OrderItem
    {
        $this->TaxType = $taxType;

        return $this;
    }

    /**
     * Get taxType
     */
    public function getTaxType(): ?TaxType
    {
        return $this->TaxType;
    }

    /**
     * Set taxDisplayType
     *
     * @param TaxDisplayType $taxDisplayType
     */
    public function setTaxDisplayType(?TaxDisplayType $taxDisplayType = null): OrderItem
    {
        $this->TaxDisplayType = $taxDisplayType;

        return $this;
    }

    /**
     * Get taxDisplayType
     */
    public function getTaxDisplayType(): ?TaxDisplayType
    {
        return $this->TaxDisplayType;
    }

    /**
     * Set orderItemType
     *
     * @param OrderItemType $orderItemType
     */
    public function setOrderItemType(?OrderItemType $orderItemType = null): OrderItem
    {
        $this->OrderItemType = $orderItemType;

        return $this;
    }

    /**
     * Get orderItemType
     */
    #[\Override]
    public function getOrderItemType(): ?OrderItemType
    {
        return $this->OrderItemType;
    }
}

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
use Eccube\Entity\Master\SaleType;
use Eccube\Repository\ProductClassRepository;

if (!class_exists(ProductClass::class)) {
    /**
     * ProductClass
     */
    #[ORM\Table(name: 'dtb_product_class')]
    #[ORM\Index(name: 'dtb_product_class_price02_idx', columns: ['price02'])]
    #[ORM\Index(columns: ['stock', 'stock_unlimited'], name: 'dtb_product_class_stock_stock_unlimited_idx')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: ProductClassRepository::class)]
    class ProductClass extends AbstractEntity
    {
        private ?string $price01_inc_tax = null;
        private ?string $price02_inc_tax = null;
        private ?string $tax_rate = '';

        /**
         * 商品規格名を含めた商品名を返す.
         */
        public function formattedProductName(): string
        {
            $productName = $this->getProduct()->getName();
            if ($this->hasClassCategory1()) {
                $productName .= ' - '.$this->getClassCategory1()->getName();
            }
            if ($this->hasClassCategory2()) {
                $productName .= ' - '.$this->getClassCategory2()->getName();
            }

            return $productName;
        }

        /**
         * Is Enable
         *
         * @deprecated
         */
        public function isEnable(): bool
        {
            return $this->getProduct()->isEnable();
        }

        /**
         * Set price01 IncTax
         */
        public function setPrice01IncTax(?string $price01_inc_tax): ProductClass
        {
            $this->price01_inc_tax = $price01_inc_tax;

            return $this;
        }

        /**
         * Get price01 IncTax
         */
        public function getPrice01IncTax(): string
        {
            return $this->price01_inc_tax;
        }

        /**
         * Set price02 IncTax
         */
        public function setPrice02IncTax(?string $price02_inc_tax): ProductClass
        {
            $this->price02_inc_tax = $price02_inc_tax;

            return $this;
        }

        /**
         * Get price02 IncTax
         */
        public function getPrice02IncTax(): string
        {
            return $this->price02_inc_tax;
        }

        /**
         * Get StockFind
         */
        public function getStockFind(): bool
        {
            if ($this->getStock() > 0 || $this->isStockUnlimited()) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Set tax_rate
         */
        public function setTaxRate(?string $tax_rate): ProductClass
        {
            $this->tax_rate = $tax_rate;

            return $this;
        }

        /**
         * Get tax_rate
         */
        public function getTaxRate(): ?string
        {
            return $this->tax_rate;
        }

        /**
         * Has ClassCategory1
         */
        public function hasClassCategory1(): bool
        {
            return isset($this->ClassCategory1);
        }

        /**
         * Has ClassCategory1
         */
        public function hasClassCategory2(): bool
        {
            return isset($this->ClassCategory2);
        }

        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private ?int $id = null;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'product_code', type: 'string', length: 255, nullable: true)]
        private $code;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'stock', type: 'decimal', precision: 10, scale: 0, nullable: true)]
        private $stock;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'stock_unlimited', type: 'boolean', options: ['default' => false])]
        private $stock_unlimited = false;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'sale_limit', type: 'decimal', precision: 10, scale: 0, nullable: true, options: ['unsigned' => true])]
        private $sale_limit;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'price01', type: 'decimal', precision: 12, scale: 2, nullable: true)]
        private $price01;

        /**
         * @var string
         */
        #[ORM\Column(name: 'price02', type: 'decimal', precision: 12, scale: 2)]
        private $price02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'delivery_fee', type: 'decimal', precision: 12, scale: 2, nullable: true, options: ['unsigned' => true])]
        private $delivery_fee;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'visible', type: 'boolean', options: ['default' => true])]
        private $visible;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'currency_code', type: 'string', nullable: true)]
        private $currency_code;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'point_rate', type: 'decimal', precision: 10, scale: 0, options: ['unsigned' => true], nullable: true)]
        private $point_rate;

        /**
         * @var ProductStock|null
         */
        #[ORM\OneToOne(targetEntity: ProductStock::class, mappedBy: 'ProductClass', cascade: ['persist', 'remove'])]
        private $ProductStock;

        /**
         * @var TaxRule|null
         */
        #[ORM\OneToOne(targetEntity: TaxRule::class, mappedBy: 'ProductClass', cascade: ['persist', 'remove'])]
        private $TaxRule;

        /**
         * @var Product|null
         */
        #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'ProductClasses')]
        #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
        private $Product;

        /**
         * @var SaleType|null
         */
        #[ORM\ManyToOne(targetEntity: SaleType::class)]
        #[ORM\JoinColumn(name: 'sale_type_id', referencedColumnName: 'id')]
        private $SaleType;

        /**
         * @var ClassCategory|null
         */
        #[ORM\ManyToOne(targetEntity: ClassCategory::class)]
        #[ORM\JoinColumn(name: 'class_category_id1', referencedColumnName: 'id', nullable: true)]
        private $ClassCategory1;

        /**
         * @var ClassCategory|null
         */
        #[ORM\ManyToOne(targetEntity: ClassCategory::class)]
        #[ORM\JoinColumn(name: 'class_category_id2', referencedColumnName: 'id', nullable: true)]
        private $ClassCategory2;

        /**
         * @var DeliveryDuration|null
         */
        #[ORM\ManyToOne(targetEntity: DeliveryDuration::class)]
        #[ORM\JoinColumn(name: 'delivery_duration_id', referencedColumnName: 'id')]
        private $DeliveryDuration;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        public function __clone()
        {
            $this->id = null;
        }

        /**
         * Get id.
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set code.
         */
        public function setCode(?string $code = null): ProductClass
        {
            $this->code = $code;

            return $this;
        }

        /**
         * Get code.
         */
        public function getCode(): ?string
        {
            return $this->code;
        }

        /**
         * Set stock.
         */
        public function setStock(?string $stock = null): ProductClass
        {
            $this->stock = $stock;

            return $this;
        }

        /**
         * Get stock.
         */
        public function getStock(): ?string
        {
            return $this->stock;
        }

        /**
         * Set stockUnlimited.
         */
        public function setStockUnlimited(bool $stockUnlimited): ProductClass
        {
            $this->stock_unlimited = $stockUnlimited;

            return $this;
        }

        /**
         * Get stockUnlimited.
         */
        public function isStockUnlimited(): bool
        {
            return $this->stock_unlimited;
        }

        /**
         * Set saleLimit.
         */
        public function setSaleLimit(?string $saleLimit = null): ProductClass
        {
            $this->sale_limit = $saleLimit;

            return $this;
        }

        /**
         * Get saleLimit.
         */
        public function getSaleLimit(): ?string
        {
            return $this->sale_limit;
        }

        /**
         * Set price01.
         */
        public function setPrice01(?string $price01 = null): ProductClass
        {
            $this->price01 = $price01;

            return $this;
        }

        /**
         * Get price01.
         */
        public function getPrice01(): ?string
        {
            return $this->price01;
        }

        /**
         * Set price02.
         */
        public function setPrice02(?string $price02): ProductClass
        {
            $this->price02 = $price02;

            return $this;
        }

        /**
         * Get price02.
         */
        public function getPrice02(): ?string
        {
            return $this->price02;
        }

        /**
         * Set deliveryFee.
         */
        public function setDeliveryFee(?string $deliveryFee = null): ProductClass
        {
            $this->delivery_fee = $deliveryFee;

            return $this;
        }

        /**
         * Get deliveryFee.
         */
        public function getDeliveryFee(): ?string
        {
            return $this->delivery_fee;
        }

        public function isVisible(): bool
        {
            return $this->visible;
        }

        public function setVisible(bool $visible): ProductClass
        {
            $this->visible = $visible;

            return $this;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): ProductClass
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         */
        public function setUpdateDate(\DateTime $updateDate): ProductClass
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
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
         *
         * @return $this
         */
        public function setCurrencyCode(?string $currencyCode = null): static
        {
            $this->currency_code = $currencyCode;

            return $this;
        }

        /**
         * Set productStock.
         */
        public function setProductStock(?ProductStock $productStock = null): ProductClass
        {
            $this->ProductStock = $productStock;

            return $this;
        }

        /**
         * Get productStock.
         */
        public function getProductStock(): ?ProductStock
        {
            return $this->ProductStock;
        }

        /**
         * Set taxRule.
         */
        public function setTaxRule(?TaxRule $taxRule = null): ProductClass
        {
            $this->TaxRule = $taxRule;

            return $this;
        }

        /**
         * Get taxRule.
         */
        public function getTaxRule(): ?TaxRule
        {
            return $this->TaxRule;
        }

        /**
         * Set product.
         */
        public function setProduct(?Product $product = null): ProductClass
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
         * Set saleType.
         */
        public function setSaleType(?SaleType $saleType = null): ProductClass
        {
            $this->SaleType = $saleType;

            return $this;
        }

        /**
         * Get saleType.
         */
        public function getSaleType(): ?SaleType
        {
            return $this->SaleType;
        }

        /**
         * Set classCategory1.
         */
        public function setClassCategory1(?ClassCategory $classCategory1 = null): ProductClass
        {
            $this->ClassCategory1 = $classCategory1;

            return $this;
        }

        /**
         * Get classCategory1.
         */
        public function getClassCategory1(): ?ClassCategory
        {
            return $this->ClassCategory1;
        }

        /**
         * Set classCategory2.
         */
        public function setClassCategory2(?ClassCategory $classCategory2 = null): ProductClass
        {
            $this->ClassCategory2 = $classCategory2;

            return $this;
        }

        /**
         * Get classCategory2.
         */
        public function getClassCategory2(): ?ClassCategory
        {
            return $this->ClassCategory2;
        }

        /**
         * Set deliveryDuration.
         */
        public function setDeliveryDuration(?DeliveryDuration $deliveryDuration = null): ProductClass
        {
            $this->DeliveryDuration = $deliveryDuration;

            return $this;
        }

        /**
         * Get deliveryDuration.
         */
        public function getDeliveryDuration(): ?DeliveryDuration
        {
            return $this->DeliveryDuration;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): ProductClass
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }

        /**
         * Set pointRate
         */
        public function setPointRate(?string $pointRate): ProductClass
        {
            $this->point_rate = $pointRate;

            return $this;
        }

        /**
         * Get pointRate
         */
        public function getPointRate(): ?string
        {
            return $this->point_rate;
        }
    }
}

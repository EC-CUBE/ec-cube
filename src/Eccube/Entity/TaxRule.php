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
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Master\RoundingType;
use Eccube\Repository\TaxRuleRepository;

if (!class_exists(TaxRule::class)) {
    /**
     * TaxRule
     */
    #[ORM\Table(name: 'dtb_tax_rule')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: TaxRuleRepository::class)]
    class TaxRule extends AbstractEntity
    {
        /**
         * @var int
         */
        public const DEFAULT_TAX_RULE_ID = 1;

        /**
         * @var int
         */
        private $sort_no;

        /**
         * is default
         *
         * @return bool
         */
        public function isDefaultTaxRule(): bool
        {
            return self::DEFAULT_TAX_RULE_ID === $this->getId();
        }

        /**
         * Set sortNo
         *
         * @param  int $sortNo
         *
         * @return TaxRule
         */
        public function setSortNo($sortNo): TaxRule
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo
         *
         * @return int
         */
        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

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
         * @var \DateTime
         */
        #[ORM\Column(name: 'apply_date', type: 'datetimetz')]
        private $apply_date;

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
         * @var ProductClass|null
         */
        #[ORM\OneToOne(targetEntity: ProductClass::class, inversedBy: 'TaxRule')]
        #[ORM\JoinColumn(name: 'product_class_id', referencedColumnName: 'id')]
        private $ProductClass;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        /**
         * @var Country|null
         */
        #[ORM\ManyToOne(targetEntity: Country::class)]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private $Country;

        /**
         * @var Pref|null
         */
        #[ORM\ManyToOne(targetEntity: Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private $Pref;

        /**
         * @var Product|null
         */
        #[ORM\ManyToOne(targetEntity: Product::class)]
        #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
        private $Product;

        /**
         * @var RoundingType|null
         */
        #[ORM\ManyToOne(targetEntity: RoundingType::class)]
        #[ORM\JoinColumn(name: 'rounding_type_id', referencedColumnName: 'id')]
        private $RoundingType;

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
         * Set taxRate.
         *
         * @param string $taxRate
         *
         * @return TaxRule
         */
        public function setTaxRate($taxRate): TaxRule
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
         * @param string $taxAdjust
         *
         * @return TaxRule
         */
        public function setTaxAdjust($taxAdjust): TaxRule
        {
            $this->tax_adjust = $taxAdjust;

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
         * Set applyDate.
         *
         * @param \DateTime $applyDate
         *
         * @return TaxRule
         */
        public function setApplyDate($applyDate): TaxRule
        {
            $this->apply_date = $applyDate;

            return $this;
        }

        /**
         * Get applyDate.
         *
         * @return \DateTime|null
         */
        public function getApplyDate(): ?\DateTime
        {
            return $this->apply_date;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return TaxRule
         */
        public function setCreateDate($createDate): TaxRule
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime|null
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return TaxRule
         */
        public function setUpdateDate($updateDate): TaxRule
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime|null
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * Set productClass.
         *
         * @param ProductClass|null $productClass
         *
         * @return TaxRule
         */
        public function setProductClass(?ProductClass $productClass = null): TaxRule
        {
            $this->ProductClass = $productClass;

            return $this;
        }

        /**
         * Get productClass.
         *
         * @return ProductClass|null
         */
        public function getProductClass(): ?ProductClass
        {
            return $this->ProductClass;
        }

        /**
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return TaxRule
         */
        public function setCreator(?Member $creator = null): TaxRule
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return Member|null
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }

        /**
         * Set country.
         *
         * @param Country|null $country
         *
         * @return TaxRule
         */
        public function setCountry(?Country $country = null): TaxRule
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return Country|null
         */
        public function getCountry(): ?Country
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @param Pref|null $pref
         *
         * @return TaxRule
         */
        public function setPref(?Pref $pref = null): TaxRule
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return Pref|null
         */
        public function getPref(): ?Pref
        {
            return $this->Pref;
        }

        /**
         * Set product.
         *
         * @param Product|null $product
         *
         * @return TaxRule
         */
        public function setProduct(?Product $product = null): TaxRule
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
         * Set roundingType.
         *
         * @return TaxRule
         */
        public function setRoundingType(?RoundingType $RoundingType = null): TaxRule
        {
            $this->RoundingType = $RoundingType;

            return $this;
        }

        /**
         * Get roundingType.
         *
         * @return RoundingType|null
         */
        public function getRoundingType(): ?RoundingType
        {
            return $this->RoundingType;
        }

        /**
         * 自分自身と Target を比較し, ソートのための数値を返す.
         *
         * 以下の順で比較し、
         *
         * 同一であれば 0
         * 自分の方が大きければ正の整数
         * 小さければ負の整数を返す.
         *
         * 1. 商品別税率が有効
         * 2. apply_date
         * 3. sort_no
         *
         * このメソッドは usort() 関数などで使用する.
         *
         * @param TaxRule $Target 比較対象の TaxRule
         *
         * @return int
         */
        public function compareTo(TaxRule $Target): int
        {
            if ($this->isProductTaxRule() && !$Target->isProductTaxRule()) {
                return -1;
            } elseif (!$this->isProductTaxRule() && $Target->isProductTaxRule()) {
                return 1;
            } else {
                if ($this->getApplyDate()->format('YmdHis') == $Target->getApplyDate()->format('YmdHis')) {
                    if ($this->getSortNo() == $Target->getSortNo()) {
                        return 0;
                    }
                    if ($this->getSortNo() > $Target->getSortNo()) {
                        return -1;
                    } else {
                        return 1;
                    }
                } else {
                    if ($this->getApplyDate()->format('YmdHis') > $Target->getApplyDate()->format('YmdHis')) {
                        return -1;
                    } else {
                        return 1;
                    }
                }
            }
        }

        /**
         * 商品別税率設定が適用されているかどうか.
         *
         * @return bool 商品別税率が適用されている場合 true
         */
        public function isProductTaxRule(): bool
        {
            return $this->getProductClass() !== null || $this->getProduct() !== null;
        }
    }
}

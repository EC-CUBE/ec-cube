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

        private int $sort_no = 0;

        /**
         * is default
         */
        public function isDefaultTaxRule(): bool
        {
            return self::DEFAULT_TAX_RULE_ID === $this->getId();
        }

        /**
         * Set sortNo
         */
        public function setSortNo(int $sortNo): TaxRule
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo
         */
        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'tax_rate', type: Types::DECIMAL, precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 0])]
        private ?string $tax_rate = '0';

        #[ORM\Column(name: 'tax_adjust', type: Types::DECIMAL, precision: 10, scale: 0, options: ['unsigned' => true, 'default' => 0])]
        private string $tax_adjust = '0';

        #[ORM\Column(name: 'apply_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $apply_date = null;

        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $create_date = null;

        #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $update_date = null;

        #[ORM\OneToOne(targetEntity: ProductClass::class, inversedBy: 'TaxRule')]
        #[ORM\JoinColumn(name: 'product_class_id', referencedColumnName: 'id')]
        private ?ProductClass $ProductClass = null;

        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private ?Member $Creator = null;

        #[ORM\ManyToOne(targetEntity: Country::class)]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private ?Country $Country = null;

        #[ORM\ManyToOne(targetEntity: Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private ?Pref $Pref = null;

        #[ORM\ManyToOne(targetEntity: Product::class)]
        #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
        private ?Product $Product = null;

        #[ORM\ManyToOne(targetEntity: RoundingType::class)]
        #[ORM\JoinColumn(name: 'rounding_type_id', referencedColumnName: 'id')]
        private ?RoundingType $RoundingType = null;

        /**
         * Get id.
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set taxRate.
         */
        public function setTaxRate(?string $taxRate): TaxRule
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
        public function setTaxAdjust(string $taxAdjust): TaxRule
        {
            $this->tax_adjust = $taxAdjust;

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
         * Set applyDate.
         */
        public function setApplyDate(\DateTime $applyDate): TaxRule
        {
            $this->apply_date = $applyDate;

            return $this;
        }

        /**
         * Get applyDate.
         */
        public function getApplyDate(): ?\DateTime
        {
            return $this->apply_date;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): TaxRule
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
        public function setUpdateDate(\DateTime $updateDate): TaxRule
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
         * Set productClass.
         */
        public function setProductClass(?ProductClass $productClass = null): TaxRule
        {
            $this->ProductClass = $productClass;

            return $this;
        }

        /**
         * Get productClass.
         */
        public function getProductClass(): ?ProductClass
        {
            return $this->ProductClass;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): TaxRule
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
         * Set country.
         */
        public function setCountry(?Country $country = null): TaxRule
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         */
        public function getCountry(): ?Country
        {
            return $this->Country;
        }

        /**
         * Set pref.
         */
        public function setPref(?Pref $pref = null): TaxRule
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         */
        public function getPref(): ?Pref
        {
            return $this->Pref;
        }

        /**
         * Set product.
         */
        public function setProduct(?Product $product = null): TaxRule
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
         * Set roundingType.
         */
        public function setRoundingType(?RoundingType $RoundingType = null): TaxRule
        {
            $this->RoundingType = $RoundingType;

            return $this;
        }

        /**
         * Get roundingType.
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
         */
        public function compareTo(TaxRule $Target): int
        {
            if ($this->isProductTaxRule() && !$Target->isProductTaxRule()) {
                return -1;
            } elseif (!$this->isProductTaxRule() && $Target->isProductTaxRule()) {
                return 1;
            }
            if ($this->getApplyDate()->format('YmdHis') == $Target->getApplyDate()->format('YmdHis')) {
                if ($this->getSortNo() == $Target->getSortNo()) {
                    return 0;
                }
                if ($this->getSortNo() > $Target->getSortNo()) {
                    return -1;
                }

                return 1;
            }
            if ($this->getApplyDate()->format('YmdHis') > $Target->getApplyDate()->format('YmdHis')) {
                return -1;
            }

            return 1;
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

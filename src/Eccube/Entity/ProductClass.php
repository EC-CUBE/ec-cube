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

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductClass
 *
 * @ORM\Table(name="dtb_product_class", indexes={@ORM\Index(name="dtb_product_class_price02_idx", columns={"price02"}), @ORM\Index(name="dtb_product_class_stock_stock_unlimited_idx", columns={"stock", "stock_unlimited"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\ProductClassRepository")
 */
class ProductClass extends \Eccube\Entity\AbstractEntity
{
    private $price01_inc_tax = null;
    private $price02_inc_tax = null;
    private $add = false;
    private $tax_rate = false;

    /**
     * Is Enable
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->getProduct()->isEnable();
    }

    /**
     * Set price01 IncTax
     *
     * @param  string       $price01_inc_tax
     * @return ProductClass
     */
    public function setPrice01IncTax($price01_inc_tax)
    {
        $this->price01_inc_tax = $price01_inc_tax;

        return $this;
    }

    /**
     * Get price01 IncTax
     *
     * @return string
     */
    public function getPrice01IncTax()
    {
        return $this->price01_inc_tax;
    }

    /**
     * Set price02 IncTax
     *
     * @param  string       $price02
     * @return ProductClass
     */
    public function setPrice02IncTax($price02_inc_tax)
    {
        $this->price02_inc_tax = $price02_inc_tax;

        return $this;
    }

    /**
     * Get price02 IncTax
     *
     * @return string
     */
    public function getPrice02IncTax()
    {
        return $this->price02_inc_tax;
    }

    /**
     * Get StockFind
     *
     * @return bool
     */
    public function getStockFind()
    {
        if ($this->getStock() > 0 || $this->getStockUnlimited() == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set add
     *
     * @param  bool $add
     * @return ProductClass
     */
    public function setAdd($add)
    {
        $this->add = $add;

        return $this;
    }

    /**
     * Get add
     *
     * @return bool
     */
    public function getAdd()
    {
        return $this->add;
    }


    /**
     * Set tax_rate
     *
     * @param  string $tax_rate
     * @return ProductClass
     */
    public function setTaxRate($tax_rate)
    {
        $this->tax_rate = $tax_rate;

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
     * Has ClassCategory1
     *
     * @return boolean
     */
    public function hasClassCategory1()
    {
        return isset($this->ClassCategory1);
    }

    /**
     * Has ClassCategory1
     *
     * @return boolean
     */
    public function hasClassCategory2()
    {
        return isset($this->ClassCategory2);
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="stock", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $stock;

    /**
     * @var int
     *
     * @ORM\Column(name="stock_unlimited", type="smallint", options={"unsigned":true,"default":0})
     */
    private $stock_unlimited = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sale_limit", type="decimal", precision=10, scale=0, nullable=true, options={"unsigned":true})
     */
    private $sale_limit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price01", type="decimal", precision=12, scale=2, nullable=true, options={"unsigned":true})
     */
    private $price01;

    /**
     * @var string
     *
     * @ORM\Column(name="price02", type="decimal", precision=12, scale=2, options={"unsigned":true})
     */
    private $price02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="delivery_fee", type="decimal", precision=12, scale=2, nullable=true, options={"unsigned":true})
     */
    private $delivery_fee;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", options={"default":true})
     */
    private $visible;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="currency_code", type="string", nullable=true)
     */
    private $currency_code;

    /**
     * @var string
     *
     * @ORM\Column(name="point_rate", type="decimal", precision=10, scale=0, options={"unsigned":true}, nullable=true)
     */
    private $point_rate;

    /**
     * @var \Eccube\Entity\ProductStock
     *
     * @ORM\OneToOne(targetEntity="Eccube\Entity\ProductStock", mappedBy="ProductClass", cascade={"persist","remove"})
     */
    private $ProductStock;

    /**
     * @var \Eccube\Entity\TaxRule
     *
     * @ORM\OneToOne(targetEntity="Eccube\Entity\TaxRule", mappedBy="ProductClass", cascade={"persist","remove"})
     */
    private $TaxRule;

    /**
     * @var \Eccube\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product", inversedBy="ProductClasses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $Product;

    /**
     * @var \Eccube\Entity\Master\ProductType
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\ProductType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_type_id", referencedColumnName="id")
     * })
     */
    private $ProductType;

    /**
     * @var \Eccube\Entity\ClassCategory
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="class_category_id1", referencedColumnName="id", nullable=true)
     * })
     */
    private $ClassCategory1;

    /**
     * @var \Eccube\Entity\ClassCategory
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\ClassCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="class_category_id2", referencedColumnName="id", nullable=true)
     * })
     */
    private $ClassCategory2;

    /**
     * @var \Eccube\Entity\DeliveryDate
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\DeliveryDate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="delivery_date_id", referencedColumnName="id")
     * })
     */
    private $DeliveryDate;

    /**
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     */
    private $Creator;

    public function __clone()
    {
        $this->id = null;
    }

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
     * Set code.
     *
     * @param string|null $code
     *
     * @return ProductClass
     */
    public function setCode($code = null)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set stock.
     *
     * @param string|null $stock
     *
     * @return ProductClass
     */
    public function setStock($stock = null)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock.
     *
     * @return string|null
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set stockUnlimited.
     *
     * @param int $stockUnlimited
     *
     * @return ProductClass
     */
    public function setStockUnlimited($stockUnlimited)
    {
        $this->stock_unlimited = $stockUnlimited;

        return $this;
    }

    /**
     * Get stockUnlimited.
     *
     * @return int
     */
    public function getStockUnlimited()
    {
        return $this->stock_unlimited;
    }

    /**
     * Set saleLimit.
     *
     * @param string|null $saleLimit
     *
     * @return ProductClass
     */
    public function setSaleLimit($saleLimit = null)
    {
        $this->sale_limit = $saleLimit;

        return $this;
    }

    /**
     * Get saleLimit.
     *
     * @return string|null
     */
    public function getSaleLimit()
    {
        return $this->sale_limit;
    }

    /**
     * Set price01.
     *
     * @param string|null $price01
     *
     * @return ProductClass
     */
    public function setPrice01($price01 = null)
    {
        $this->price01 = $price01;

        return $this;
    }

    /**
     * Get price01.
     *
     * @return string|null
     */
    public function getPrice01()
    {
        return $this->price01;
    }

    /**
     * Set price02.
     *
     * @param string $price02
     *
     * @return ProductClass
     */
    public function setPrice02($price02)
    {
        $this->price02 = $price02;

        return $this;
    }

    /**
     * Get price02.
     *
     * @return string
     */
    public function getPrice02()
    {
        return $this->price02;
    }

    /**
     * Set deliveryFee.
     *
     * @param string|null $deliveryFee
     *
     * @return ProductClass
     */
    public function setDeliveryFee($deliveryFee = null)
    {
        $this->delivery_fee = $deliveryFee;

        return $this;
    }

    /**
     * Get deliveryFee.
     *
     * @return string|null
     */
    public function getDeliveryFee()
    {
        return $this->delivery_fee;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     * @return ProductClass
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return ProductClass
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     *
     * @return ProductClass
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
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
     * @return $this
     */
    public function setCurrencyCode($currencyCode = null)
    {
        $this->currency_code = $currencyCode;

        return $this;
    }

    /**
     * Set productStock.
     *
     * @param \Eccube\Entity\ProductStock|null $productStock
     *
     * @return ProductClass
     */
    public function setProductStock(\Eccube\Entity\ProductStock $productStock = null)
    {
        $this->ProductStock = $productStock;

        return $this;
    }

    /**
     * Get productStock.
     *
     * @return \Eccube\Entity\ProductStock|null
     */
    public function getProductStock()
    {
        return $this->ProductStock;
    }

    /**
     * Set taxRule.
     *
     * @param \Eccube\Entity\TaxRule|null $taxRule
     *
     * @return ProductClass
     */
    public function setTaxRule(\Eccube\Entity\TaxRule $taxRule = null)
    {
        $this->TaxRule = $taxRule;

        return $this;
    }

    /**
     * Get taxRule.
     *
     * @return \Eccube\Entity\TaxRule|null
     */
    public function getTaxRule()
    {
        return $this->TaxRule;
    }

    /**
     * Set product.
     *
     * @param \Eccube\Entity\Product|null $product
     *
     * @return ProductClass
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
        return $this->Product;
    }

    /**
     * Set productType.
     *
     * @param \Eccube\Entity\Master\ProductType|null $productType
     *
     * @return ProductClass
     */
    public function setProductType(\Eccube\Entity\Master\ProductType $productType = null)
    {
        $this->ProductType = $productType;

        return $this;
    }

    /**
     * Get productType.
     *
     * @return \Eccube\Entity\Master\ProductType|null
     */
    public function getProductType()
    {
        return $this->ProductType;
    }

    /**
     * Set classCategory1.
     *
     * @param \Eccube\Entity\ClassCategory|null $classCategory1
     *
     * @return ProductClass
     */
    public function setClassCategory1(\Eccube\Entity\ClassCategory $classCategory1 = null)
    {
        $this->ClassCategory1 = $classCategory1;

        return $this;
    }

    /**
     * Get classCategory1.
     *
     * @return \Eccube\Entity\ClassCategory|null
     */
    public function getClassCategory1()
    {
        return $this->ClassCategory1;
    }

    /**
     * Set classCategory2.
     *
     * @param \Eccube\Entity\ClassCategory|null $classCategory2
     *
     * @return ProductClass
     */
    public function setClassCategory2(\Eccube\Entity\ClassCategory $classCategory2 = null)
    {
        $this->ClassCategory2 = $classCategory2;

        return $this;
    }

    /**
     * Get classCategory2.
     *
     * @return \Eccube\Entity\ClassCategory|null
     */
    public function getClassCategory2()
    {
        return $this->ClassCategory2;
    }

    /**
     * Set deliveryDate.
     *
     * @param \Eccube\Entity\DeliveryDate|null $deliveryDate
     *
     * @return ProductClass
     */
    public function setDeliveryDate(\Eccube\Entity\DeliveryDate $deliveryDate = null)
    {
        $this->DeliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * Get deliveryDate.
     *
     * @return \Eccube\Entity\DeliveryDate|null
     */
    public function getDeliveryDate()
    {
        return $this->DeliveryDate;
    }

    /**
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     * @return ProductClass
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     *
     * @return \Eccube\Entity\Member|null
     */
    public function getCreator()
    {
        return $this->Creator;
    }

    /**
     * Set pointRate
     *
     * @param string $pointRate
     *
     * @return ProductClass
     */
    public function setPointRate($pointRate)
    {
        $this->point_rate = $pointRate;

        return $this;
    }

    /**
     * Get pointRate
     *
     * @return string
     */
    public function getPointRate()
    {
        return $this->point_rate;
    }
}

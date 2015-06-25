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
 * Product
 */
class Product extends \Eccube\Entity\AbstractEntity
{
    private $_calc = false;
    private $stockFinds = array();
    private $stocks = array();
    private $stockUnlimiteds = array();
    private $price01 = array();
    private $price02 = array();
    private $price01IncTaxs = array();
    private $price02IncTaxs = array();
    private $codes = array();
    private $pointRates = array();
    private $points = array();
    private $classCategories1 = array();
    private $classCategories2 = array();
    private $className1;
    private $className2;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    public function _calc()
    {
        if (!$this->_calc) {
            $i = 0;
            foreach ($this->getProductClasses() as $ProductClass) {
                /* @var $ProductClass \Eccube\Entity\ProductClass */
                // del_flg
                if ($ProductClass->getDelFlg() === 1) {
                    continue;
                }

                // stock_find
                $this->stockFinds[] = $ProductClass->getStockFind();

                // stock
                $this->stocks[] = $ProductClass->getStock();

                // stock_unlimited
                $this->stockUnlimiteds[] = $ProductClass->getStockUnlimited();

                // price01
                $this->price01[] = $ProductClass->getPrice01();

                // price02
                $this->price02[] = $ProductClass->getPrice02();

                // price01IncTax
                $this->price01IncTaxs[] = $ProductClass->getPrice01IncTax();

                // price02IncTax
                $this->price02IncTaxs[] = $ProductClass->getPrice02IncTax();

                // product_code
                $this->codes[] = $ProductClass->getCode();

                // point
                $this->points[] = $ProductClass->getPoint();

                // point_rate
                $this->pointRates[] = $ProductClass->getPointRate();

                if ($i === 0) {
                    if ($ProductClass->getClassCategory1() && $ProductClass->getClassCategory1()->getId()) {
                        $this->className1 = $ProductClass->getClassCategory1()->getClassName()->getName();
                    }
                    if ($ProductClass->getClassCategory2() && $ProductClass->getClassCategory2()->getId()) {
                        $this->className2 = $ProductClass->getClassCategory2()->getClassName()->getName();
                    }
                }
                if ($ProductClass->getClassCategory1()) {
                    $classCategoryId1 = $ProductClass->getClassCategory1()->getId();
                    if (!empty($classCategoryId1)) {
                        $this->classCategories1[$ProductClass->getClassCategory1()->getId()] = $ProductClass->getClassCategory1()->getName();
                        if ($ProductClass->getClassCategory2()) {
                            $this->classCategories2[$ProductClass->getClassCategory1()->getId()][$ProductClass->getClassCategory2()->getId()] = $ProductClass->getClassCategory2()->getName();
                        }
                    }
                }
                $i++;
            }
            $this->_calc = true;
        }
    }

    /**
     * Is Enable
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->getStatus() === 1 ? true : false;
    }

    /**
     * Get ClassName1
     *
     * @return bool
     */
    public function getClassName1()
    {
        $this->_calc();

        return $this->className1;
    }

    /**
     * Get ClassName1
     *
     * @return bool
     */
    public function getClassName2()
    {
        $this->_calc();

        return $this->className2;
    }

    /**
     * Get getClassCategories1
     *
     * @return bool
     */
    public function getClassCategories1()
    {
        $this->_calc();

        return $this->classCategories1;
    }

    /**
     * Get getClassCategories2
     *
     * @return bool
     */
    public function getClassCategories2($class_category1)
    {
        $this->_calc();

        return isset($this->classCategories2[$class_category1]) ? $this->classCategories2[$class_category1] : array();
    }

    /**
     * Get StockFind
     *
     * @return bool
     */
    public function getStockFind()
    {
        $this->_calc();

        return max($this->stockFinds);
    }

    /**
     * Get Stock min
     *
     * @return integer
     */
    public function getStockMin()
    {
        $this->_calc();

        return min($this->stocks);
    }

    /**
     * Get Stock max
     *
     * @return integer
     */
    public function getStockMax()
    {
        $this->_calc();

        return max($this->stocks);
    }

    /**
     * Get StockUnlimited min
     *
     * @return integer
     */
    public function getStockUnlimitedMin()
    {
        $this->_calc();

        return min($this->stockUnlimiteds);
    }

    /**
     * Get StockUnlimited max
     *
     * @return integer
     */
    public function getStockUnlimitedMax()
    {
        $this->_calc();

        return max($this->stockUnlimiteds);
    }

    /**
     * Get Price01 min
     *
     * @return integer
     */
    public function getPrice01Min()
    {
        $this->_calc();

        return min($this->price01);
    }

    /**
     * Get Price01 max
     *
     * @return integer
     */
    public function getPrice01Max()
    {
        $this->_calc();

        return max($this->price01);
    }

    /**
     * Get Price02 min
     *
     * @return integer
     */
    public function getPrice02Min()
    {
        $this->_calc();

        return min($this->price02);
    }

    /**
     * Get Price02 max
     *
     * @return integer
     */
    public function getPrice02Max()
    {
        $this->_calc();

        return max($this->price02);
    }

    /**
     * Get Price01IncTax min
     *
     * @return integer
     */
    public function getPrice01IncTaxMin()
    {
        $this->_calc();

        return min($this->price01IncTaxs);
    }

    /**
     * Get Price01IncTax max
     *
     * @return integer
     */
    public function getPrice01IncTaxMax()
    {
        $this->_calc();

        return max($this->price01IncTaxs);
    }

    /**
     * Get Price02IncTax min
     *
     * @return integer
     */
    public function getPrice02IncTaxMin()
    {
        $this->_calc();

        return min($this->price02IncTaxs);
    }

    /**
     * Get Price02IncTax max
     *
     * @return integer
     */
    public function getPrice02IncTaxMax()
    {
        $this->_calc();

        return max($this->price02IncTaxs);
    }

    /**
     * Get Point min
     *
     * @return integer
     */
    public function getPointMin()
    {
        return min($this->points);
    }

    /**
     * Get Point max
     *
     * @return integer
     */
    public function getPointMax()
    {
        return max($this->points);
    }

    /**
     * Get Product_code min
     *
     * @return integer
     */
    public function getCodeMin()
    {
        $this->_calc();

        return min($this->codes);
    }

    /**
     * Get Product_code max
     *
     * @return integer
     */
    public function getCodeMax()
    {
        $this->_calc();

        return max($this->codes);
    }

    /**
     * Get getPointRate
     *
     * @return integer
     */
    public function getPointRate()
    {
        $this->_calc();

        return max($this->pointRates);
    }

    /**
     * Get ClassCategories
     *
     * @return array
     */
    public function getClassCategories()
    {
        $this->_calc();

        $class_categories = array(
            '__unselected' => array(
                '__unselected' => array(
                    'name'              => '選択してください',
                    'product_class_id'  => '',
                ),
            ),
        );
        foreach ($this->getProductClasses() as $ProductClass) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ClassCategory1 = $ProductClass->getClassCategory1();
            $ClassCategory2 = $ProductClass->getClassCategory2();

            $class_category_id1 = $ClassCategory1 ? (string) $ClassCategory1->getId() : '__unselected2';
            $class_category_id2 = $ClassCategory2 ? (string) $ClassCategory2->getId() : '';
            $class_category_name1 = $ClassCategory1 ? $ClassCategory1->getName() . ($ProductClass->getStockFind() ? '' : ' (品切れ中)') : '';
            $class_category_name2 = $ClassCategory2 ? $ClassCategory2->getName() . ($ProductClass->getStockFind() ? '' : ' (品切れ中)') : '';

            $class_categories[$class_category_id1]['#'] = array(
                'classcategory_id2' => '',
                'name'              => '選択してください',
                'product_class_id'  => '',
            );
            $class_categories[$class_category_id1]['#'.$class_category_id2] = array(
                'classcategory_id2' => $class_category_id2,
                'name'              => $class_category_name2,
                'stock_find'        => $ProductClass->getStockFind(),
                'price01'           => number_format($ProductClass->getPrice01IncTax()),
                'price02'           => number_format($ProductClass->getPrice02IncTax()),
                'point'             => number_format($ProductClass->getPoint()),
                'product_class_id'  => (string) $ProductClass->getId(),
                'product_code'      => $ProductClass->getCode(),
                'product_type'      => (string) $ProductClass->getProductType()->getId(),
            );
        }

        return $class_categories;
    }

    public function getMainListImage() {
        $ProductImages = $this->getProductImage();
        return empty($ProductImages) ? null : $ProductImages[0];
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $note;

    /**
     * @var string
     */
    private $description_list;

    /**
     * @var string
     */
    private $description_detail;

    /**
     * @var string
     */
    private $free_area;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductCategories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductClasses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductStatuses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $RecommendProducts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $RecommendedProducts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Reviews;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $BestProducts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $CustomerFavoriteProducts;

    /**
     * @var \Eccube\Entity\Maker
     */
    private $Maker;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;

    /**
     * @var \Eccube\Entity\DeliveryDate
     */
    private $DeliveryDate;

    /**
     * @var \Eccube\Entity\Master\Disp
     */
    private $Status;

    /**
     * @var \Eccube\Entity\Master\ProductStatusColor
     */
    private $ProductStatusColor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ProductCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProductClasses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProductStatuses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->RecommendProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->RecommendedProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Reviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->BestProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->CustomerFavoriteProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProductImage = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ProductTag = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param  string  $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set note
     *
     * @param  string  $note
     * @return Product
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set description_list
     *
     * @param string $descriptionList
     * @return Product
     */
    public function setDescriptionList($descriptionList)
    {
        $this->description_list = $descriptionList;

        return $this;
    }

    /**
     * Get description_list
     *
     * @return string 
     */
    public function getDescriptionList()
    {
        return $this->description_list;
    }

    /**
     * Set description_detail
     *
     * @param string $descriptionDetail
     * @return Product
     */
    public function setDescriptionDetail($descriptionDetail)
    {
        $this->description_detail = $descriptionDetail;

        return $this;
    }

    /**
     * Get description_detail
     *
     * @return string 
     */
    public function getDescriptionDetail()
    {
        return $this->description_detail;
    }

    /**
     * Set free_area
     *
     * @param string $freeArea
     * @return Product
     */
    public function setFreeArea($freeArea)
    {
        $this->free_area = $freeArea;

        return $this;
    }

    /**
     * Get free_area
     *
     * @return string 
     */
    public function getFreeArea()
    {
        return $this->free_area;
    }


    /**
     * Set del_flg
     *
     * @param  integer $delFlg
     * @return Product
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Product
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param  \DateTime $updateDate
     * @return Product
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Add ProductCategories
     *
     * @param  \Eccube\Entity\ProductCategory $productCategories
     * @return Product
     */
    public function addProductCategory(\Eccube\Entity\ProductCategory $productCategories)
    {
        $this->ProductCategories[] = $productCategories;

        return $this;
    }

    /**
     * Remove ProductCategories
     *
     * @param \Eccube\Entity\ProductCategory $productCategories
     */
    public function removeProductCategory(\Eccube\Entity\ProductCategory $productCategories)
    {
        $this->ProductCategories->removeElement($productCategories);
    }

    /**
     * Get ProductCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductCategories()
    {
        return $this->ProductCategories;
    }

    /**
     * Add ProductClasses
     *
     * @param  \Eccube\Entity\ProductClass $productClasses
     * @return Product
     */
    public function addProductClass(\Eccube\Entity\ProductClass $productClasses)
    {
        $this->ProductClasses[] = $productClasses;

        return $this;
    }

    /**
     * Remove ProductClasses
     *
     * @param \Eccube\Entity\ProductClass $productClasses
     */
    public function removeProductClass(\Eccube\Entity\ProductClass $productClasses)
    {
        $this->ProductClasses->removeElement($productClasses);
    }

    /**
     * Get ProductClasses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductClasses()
    {
        return $this->ProductClasses;
    }

    public function hasProductClass()
    {
        foreach ($this->ProductClasses as $ProductClass) {
            if (!is_null($ProductClass->getClassCategory1())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add ProductStatuses
     *
     * @param  \Eccube\Entity\ProductStatus $productStatuses
     * @return Product
     */
    public function addProductStatus(\Eccube\Entity\ProductStatus $productStatuses)
    {
        $this->ProductStatuses[] = $productStatuses;

        return $this;
    }

    /**
     * Remove ProductStatuses
     *
     * @param \Eccube\Entity\ProductStatus $productStatuses
     */
    public function removeProductStatus(\Eccube\Entity\ProductStatus $productStatuses)
    {
        $this->ProductStatuses->removeElement($productStatuses);
    }

    /**
     * Get ProductStatuses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductStatuses()
    {
        return $this->ProductStatuses;
    }

    /**
     * Add RecommendProducts
     *
     * @param  \Eccube\Entity\RecommendProduct $recommendProducts
     * @return Product
     */
    public function addRecommendProduct(\Eccube\Entity\RecommendProduct $recommendProducts)
    {
        $this->RecommendProducts[] = $recommendProducts;

        return $this;
    }

    /**
     * Remove RecommendProducts
     *
     * @param \Eccube\Entity\RecommendProduct $recommendProducts
     */
    public function removeRecommendProduct(\Eccube\Entity\RecommendProduct $recommendProducts)
    {
        $this->RecommendProducts->removeElement($recommendProducts);
    }

    /**
     * Get RecommendProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecommendProducts()
    {
        return $this->RecommendProducts;
    }

    /**
     * Add RecommendedProducts
     *
     * @param  \Eccube\Entity\RecommendProduct $recommendedProducts
     * @return Product
     */
    public function addRecommendedProduct(\Eccube\Entity\RecommendProduct $recommendedProducts)
    {
        $this->RecommendedProducts[] = $recommendedProducts;

        return $this;
    }

    /**
     * Remove RecommendedProducts
     *
     * @param \Eccube\Entity\RecommendProduct $recommendedProducts
     */
    public function removeRecommendedProduct(\Eccube\Entity\RecommendProduct $recommendedProducts)
    {
        $this->RecommendedProducts->removeElement($recommendedProducts);
    }

    /**
     * Get RecommendedProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecommendedProducts()
    {
        return $this->RecommendedProducts;
    }

    /**
     * Add Reviews
     *
     * @param  \Eccube\Entity\Review $reviews
     * @return Product
     */
    public function addReview(\Eccube\Entity\Review $reviews)
    {
        $this->Reviews[] = $reviews;

        return $this;
    }

    /**
     * Remove Reviews
     *
     * @param \Eccube\Entity\Review $reviews
     */
    public function removeReview(\Eccube\Entity\Review $reviews)
    {
        $this->Reviews->removeElement($reviews);
    }

    /**
     * Get Reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->Reviews;
    }

    /**
     * Add BestProducts
     *
     * @param  \Eccube\Entity\BestProduct $bestProducts
     * @return Product
     */
    public function addBestProduct(\Eccube\Entity\BestProduct $bestProducts)
    {
        $this->BestProducts[] = $bestProducts;

        return $this;
    }

    /**
     * Remove BestProducts
     *
     * @param \Eccube\Entity\BestProduct $bestProducts
     */
    public function removeBestProduct(\Eccube\Entity\BestProduct $bestProducts)
    {
        $this->BestProducts->removeElement($bestProducts);
    }

    /**
     * Get BestProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBestProducts()
    {
        return $this->BestProducts;
    }

    /**
     * Add CustomerFavoriteProducts
     *
     * @param  \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProducts
     * @return Product
     */
    public function addCustomerFavoriteProduct(\Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProducts)
    {
        $this->CustomerFavoriteProducts[] = $customerFavoriteProducts;

        return $this;
    }

    /**
     * Remove CustomerFavoriteProducts
     *
     * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProducts
     */
    public function removeCustomerFavoriteProduct(\Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProducts)
    {
        $this->CustomerFavoriteProducts->removeElement($customerFavoriteProducts);
    }

    /**
     * Get CustomerFavoriteProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomerFavoriteProducts()
    {
        return $this->CustomerFavoriteProducts;
    }

    /**
     * Set Maker
     *
     * @param  \Eccube\Entity\Maker $maker
     * @return Product
     */
    public function setMaker(\Eccube\Entity\Maker $maker = null)
    {
        $this->Maker = $maker;

        return $this;
    }

    /**
     * Get Maker
     *
     * @return \Eccube\Entity\Maker
     */
    public function getMaker()
    {
        return $this->Maker;
    }

    /**
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $creator
     * @return Product
     */
    public function setCreator(\Eccube\Entity\Member $creator)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get Creator
     *
     * @return \Eccube\Entity\Member
     */
    public function getCreator()
    {
        return $this->Creator;
    }

    /**
     * Set DeliveryDate
     *
     * @param  \Eccube\Entity\DeliveryDate $deliveryDate
     * @return Product
     */
    public function setDeliveryDate(\Eccube\Entity\DeliveryDate $deliveryDate = null)
    {
        $this->DeliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * Get DeliveryDate
     *
     * @return \Eccube\Entity\DeliveryDate
     */
    public function getDeliveryDate()
    {
        return $this->DeliveryDate;
    }

    /**
     * Set Status
     *
     * @param  \Eccube\Entity\Master\Disp $status
     * @return Product
     */
    public function setStatus(\Eccube\Entity\Master\Disp $status = null)
    {
        $this->Status = $status;

        return $this;
    }

    /**
     * Get Status
     *
     * @return \Eccube\Entity\Master\Disp
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * Set ProductStatusColor
     *
     * @param  \Eccube\Entity\Master\ProductStatusColor $productStatusColor
     * @return Product
     */
    public function setProductStatusColor(\Eccube\Entity\Master\ProductStatusColor $productStatusColor = null)
    {
        $this->ProductStatusColor = $productStatusColor;

        return $this;
    }

    /**
     * Get ProductStatusColor
     *
     * @return \Eccube\Entity\Master\ProductStatusColor
     */
    public function getProductStatusColor()
    {
        return $this->ProductStatusColor;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductImage;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductTag;


    /**
     * Add ProductImage
     *
     * @param \Eccube\Entity\ProductImage $productImage
     * @return Product
     */
    public function addProductImage(\Eccube\Entity\ProductImage $productImage)
    {
        $this->ProductImage[] = $productImage;

        return $this;
    }

    /**
     * Remove ProductImage
     *
     * @param \Eccube\Entity\ProductImage $productImage
     */
    public function removeProductImage(\Eccube\Entity\ProductImage $productImage)
    {
        $this->ProductImage->removeElement($productImage);
    }

    /**
     * Get ProductImage
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductImage()
    {
        return $this->ProductImage;
    }

    public function getMainFileName()
    {
        if (count($this->ProductImage) > 0) {
            return $this->ProductImage[0];
        } else {
            return null;
        }
    }

    /**
     * Add ProductTag
     *
     * @param \Eccube\Entity\ProductTag $productTag
     * @return Product
     */
    public function addProductTag(\Eccube\Entity\ProductTag $productTag)
    {
        $this->ProductTag[] = $productTag;

        return $this;
    }

    /**
     * Remove ProductTag
     *
     * @param \Eccube\Entity\ProductTag $productTag
     */
    public function removeProductTag(\Eccube\Entity\ProductTag $productTag)
    {
        $this->ProductTag->removeElement($productTag);
    }

    /**
     * Get ProductTag
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductTag()
    {
        return $this->ProductTag;
    }
}

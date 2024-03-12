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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\Product')) {
    /**
     * Product
     *
     * @ORM\Table(name="dtb_product")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\ProductRepository")
     */
    class Product extends \Eccube\Entity\AbstractEntity
    {
        private $_calc = false;
        private $stockFinds = [];
        private $stocks = [];
        private $stockUnlimiteds = [];
        private $price01 = [];
        private $price02 = [];
        private $price01IncTaxs = [];
        private $price02IncTaxs = [];
        private $codes = [];
        private $classCategories1 = [];
        private $classCategories2 = [];
        private $className1;
        private $className2;

        /**
         * @return string
         */
        public function __toString()
        {
            return (string) $this->getName();
        }

        public function _calc()
        {
            if (!$this->_calc) {
                $i = 0;
                foreach ($this->getProductClasses() as $ProductClass) {
                    /* @var $ProductClass \Eccube\Entity\ProductClass */
                    // stock_find
                    if ($ProductClass->isVisible() == false) {
                        continue;
                    }
                    $ClassCategory1 = $ProductClass->getClassCategory1();
                    $ClassCategory2 = $ProductClass->getClassCategory2();
                    if ($ClassCategory1 && !$ClassCategory1->isVisible()) {
                        continue;
                    }
                    if ($ClassCategory2 && !$ClassCategory2->isVisible()) {
                        continue;
                    }

                    // stock_find
                    $this->stockFinds[] = $ProductClass->getStockFind();

                    // stock
                    $this->stocks[] = $ProductClass->getStock();

                    // stock_unlimited
                    $this->stockUnlimiteds[] = $ProductClass->isStockUnlimited();

                    // price01
                    if (!is_null($ProductClass->getPrice01())) {
                        $this->price01[] = $ProductClass->getPrice01();
                        // price01IncTax
                        $this->price01IncTaxs[] = $ProductClass->getPrice01IncTax();
                    }

                    // price02
                    $this->price02[] = $ProductClass->getPrice02();

                    // price02IncTax
                    $this->price02IncTaxs[] = $ProductClass->getPrice02IncTax();

                    // product_code
                    $this->codes[] = $ProductClass->getCode();

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
                            if ($ProductClass->getClassCategory2()) {
                                $this->classCategories1[$ProductClass->getClassCategory1()->getId()] = $ProductClass->getClassCategory1()->getName();
                                $this->classCategories2[$ProductClass->getClassCategory1()->getId()][$ProductClass->getClassCategory2()->getId()] = $ProductClass->getClassCategory2()->getName();
                            } else {
                                $this->classCategories1[$ProductClass->getClassCategory1()->getId()] = $ProductClass->getClassCategory1()->getName().($ProductClass->getStockFind() ? '' : trans('front.product.out_of_stock_label'));
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
         *
         * @deprecated
         */
        public function isEnable()
        {
            return $this->getStatus()->getId() === \Eccube\Entity\Master\ProductStatus::DISPLAY_SHOW ? true : false;
        }

        /**
         * Get ClassName1
         *
         * @return string
         */
        public function getClassName1()
        {
            $this->_calc();

            return $this->className1;
        }

        /**
         * Get ClassName2
         *
         * @return string
         */
        public function getClassName2()
        {
            $this->_calc();

            return $this->className2;
        }

        /**
         * Get getClassCategories1
         *
         * @return array
         */
        public function getClassCategories1()
        {
            $this->_calc();

            return $this->classCategories1;
        }

        public function getClassCategories1AsFlip()
        {
            return array_flip($this->getClassCategories1());
        }

        /**
         * Get getClassCategories2
         *
         * @return array
         */
        public function getClassCategories2($class_category1)
        {
            $this->_calc();

            return isset($this->classCategories2[$class_category1]) ? $this->classCategories2[$class_category1] : [];
        }

        public function getClassCategories2AsFlip($class_category1)
        {
            return array_flip($this->getClassCategories2($class_category1));
        }

        /**
         * Get StockFind
         *
         * @return bool
         */
        public function getStockFind()
        {
            $this->_calc();

            return count($this->stockFinds)
                ? max($this->stockFinds)
                : null;
        }

        /**
         * Get Stock min
         *
         * @return integer
         */
        public function getStockMin()
        {
            $this->_calc();

            return count($this->stocks)
                ? min($this->stocks)
                : null;
        }

        /**
         * Get Stock max
         *
         * @return integer
         */
        public function getStockMax()
        {
            $this->_calc();

            return count($this->stocks)
                ? max($this->stocks)
                : null;
        }

        /**
         * Get StockUnlimited min
         *
         * @return integer
         */
        public function getStockUnlimitedMin()
        {
            $this->_calc();

            return count($this->stockUnlimiteds)
                ? min($this->stockUnlimiteds)
                : null;
        }

        /**
         * Get StockUnlimited max
         *
         * @return integer
         */
        public function getStockUnlimitedMax()
        {
            $this->_calc();

            return count($this->stockUnlimiteds)
                ? max($this->stockUnlimiteds)
                : null;
        }

        /**
         * Get Price01 min
         *
         * @return integer
         */
        public function getPrice01Min()
        {
            $this->_calc();

            if (count($this->price01) == 0) {
                return null;
            }

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

            if (count($this->price01) == 0) {
                return null;
            }

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

            return count($this->price02)
                ? min($this->price02)
                : null;
        }

        /**
         * Get Price02 max
         *
         * @return integer
         */
        public function getPrice02Max()
        {
            $this->_calc();

            return count($this->price02)
                ? max($this->price02)
                : null;
        }

        /**
         * Get Price01IncTax min
         *
         * @return integer
         */
        public function getPrice01IncTaxMin()
        {
            $this->_calc();

            return count($this->price01IncTaxs)
                ? min($this->price01IncTaxs)
                : null;
        }

        /**
         * Get Price01IncTax max
         *
         * @return integer
         */
        public function getPrice01IncTaxMax()
        {
            $this->_calc();

            return count($this->price01IncTaxs)
                ? max($this->price01IncTaxs)
                : null;
        }

        /**
         * Get Price02IncTax min
         *
         * @return integer
         */
        public function getPrice02IncTaxMin()
        {
            $this->_calc();

            return count($this->price02IncTaxs)
                ? min($this->price02IncTaxs)
                : null;
        }

        /**
         * Get Price02IncTax max
         *
         * @return integer
         */
        public function getPrice02IncTaxMax()
        {
            $this->_calc();

            return count($this->price02IncTaxs)
                ? max($this->price02IncTaxs)
                : null;
        }

        /**
         * Get Product_code min
         *
         * @return integer
         */
        public function getCodeMin()
        {
            $this->_calc();

            $codes = [];
            foreach ($this->codes as $code) {
                if (!is_null($code)) {
                    $codes[] = $code;
                }
            }

            return count($codes) ? min($codes) : null;
        }

        /**
         * Get Product_code max
         *
         * @return integer
         */
        public function getCodeMax()
        {
            $this->_calc();

            $codes = [];
            foreach ($this->codes as $code) {
                if (!is_null($code)) {
                    $codes[] = $code;
                }
            }

            return count($codes) ? max($codes) : null;
        }

        public function getMainListImage()
        {
            $ProductImages = $this->getProductImage();

            return $ProductImages->isEmpty() ? null : $ProductImages[0];
        }

        public function getMainFileName()
        {
            if (count($this->ProductImage) > 0) {
                return $this->ProductImage[0];
            } else {
                return null;
            }
        }

        public function hasProductClass()
        {
            foreach ($this->ProductClasses as $ProductClass) {
                if (!$ProductClass->isVisible()) {
                    continue;
                }
                if (!is_null($ProductClass->getClassCategory1())) {
                    return true;
                }
            }

            return false;
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
         * @ORM\Column(name="name", type="string", length=255)
         */
        private $name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="note", type="text", nullable=true)
         */
        private $note;

        /**
         * @var string|null
         *
         * @ORM\Column(name="description_list", type="text", nullable=true)
         */
        private $description_list;

        /**
         * @var string|null
         *
         * @ORM\Column(name="description_detail", type="text", nullable=true)
         */
        private $description_detail;

        /**
         * @var string|null
         *
         * @ORM\Column(name="search_word", type="text", nullable=true)
         */
        private $search_word;

        /**
         * @var string|null
         *
         * @ORM\Column(name="free_area", type="text", nullable=true)
         */
        private $free_area;

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
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductCategory", mappedBy="Product", cascade={"persist","remove"})
         */
        private $ProductCategories;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductClass", mappedBy="Product", cascade={"persist","remove"})
         */
        private $ProductClasses;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductImage", mappedBy="Product", cascade={"remove"})
         * @ORM\OrderBy({
         *     "sort_no"="ASC"
         * })
         */
        private $ProductImage;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductTag", mappedBy="Product", cascade={"remove"})
         */
        private $ProductTag;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\CustomerFavoriteProduct", mappedBy="Product")
         */
        private $CustomerFavoriteProducts;

        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
         * })
         */
        private $Creator;

        /**
         * @var \Eccube\Entity\Master\ProductStatus
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\ProductStatus")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_status_id", referencedColumnName="id")
         * })
         */
        private $Status;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->ProductCategories = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductClasses = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductImage = new \Doctrine\Common\Collections\ArrayCollection();
            $this->ProductTag = new \Doctrine\Common\Collections\ArrayCollection();
            $this->CustomerFavoriteProducts = new \Doctrine\Common\Collections\ArrayCollection();
        }

        public function __clone()
        {
            $this->id = null;
        }

        public function copy()
        {
            // コピー対象外
            $this->CustomerFavoriteProducts = new ArrayCollection();

            $Categories = $this->getProductCategories();
            $this->ProductCategories = new ArrayCollection();
            foreach ($Categories as $Category) {
                $CopyCategory = clone $Category;
                $this->addProductCategory($CopyCategory);
                $CopyCategory->setProduct($this);
            }

            $Classes = $this->getProductClasses();
            $this->ProductClasses = new ArrayCollection();
            foreach ($Classes as $Class) {
                $CopyClass = clone $Class;
                $this->addProductClass($CopyClass);
                $CopyClass->setProduct($this);
            }

            $Images = $this->getProductImage();
            $this->ProductImage = new ArrayCollection();
            foreach ($Images as $Image) {
                $CloneImage = clone $Image;
                $this->addProductImage($CloneImage);
                $CloneImage->setProduct($this);
            }

            $Tags = $this->getProductTag();
            $this->ProductTag = new ArrayCollection();
            foreach ($Tags as $Tag) {
                $CloneTag = clone $Tag;
                $this->addProductTag($CloneTag);
                $CloneTag->setProduct($this);
            }

            return $this;
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
         * Set name.
         *
         * @param string $name
         *
         * @return Product
         */
        public function setName($name)
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Set note.
         *
         * @param string|null $note
         *
         * @return Product
         */
        public function setNote($note = null)
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         *
         * @return string|null
         */
        public function getNote()
        {
            return $this->note;
        }

        /**
         * Set descriptionList.
         *
         * @param string|null $descriptionList
         *
         * @return Product
         */
        public function setDescriptionList($descriptionList = null)
        {
            $this->description_list = $descriptionList;

            return $this;
        }

        /**
         * Get descriptionList.
         *
         * @return string|null
         */
        public function getDescriptionList()
        {
            return $this->description_list;
        }

        /**
         * Set descriptionDetail.
         *
         * @param string|null $descriptionDetail
         *
         * @return Product
         */
        public function setDescriptionDetail($descriptionDetail = null)
        {
            $this->description_detail = $descriptionDetail;

            return $this;
        }

        /**
         * Get descriptionDetail.
         *
         * @return string|null
         */
        public function getDescriptionDetail()
        {
            return $this->description_detail;
        }

        /**
         * Set searchWord.
         *
         * @param string|null $searchWord
         *
         * @return Product
         */
        public function setSearchWord($searchWord = null)
        {
            $this->search_word = $searchWord;

            return $this;
        }

        /**
         * Get searchWord.
         *
         * @return string|null
         */
        public function getSearchWord()
        {
            return $this->search_word;
        }

        /**
         * Set freeArea.
         *
         * @param string|null $freeArea
         *
         * @return Product
         */
        public function setFreeArea($freeArea = null)
        {
            $this->free_area = $freeArea;

            return $this;
        }

        /**
         * Get freeArea.
         *
         * @return string|null
         */
        public function getFreeArea()
        {
            return $this->free_area;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Product
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
         * @return Product
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
         * Add productCategory.
         *
         * @param \Eccube\Entity\ProductCategory $productCategory
         *
         * @return Product
         */
        public function addProductCategory(ProductCategory $productCategory)
        {
            $this->ProductCategories[] = $productCategory;

            return $this;
        }

        /**
         * Remove productCategory.
         *
         * @param \Eccube\Entity\ProductCategory $productCategory
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductCategory(ProductCategory $productCategory)
        {
            return $this->ProductCategories->removeElement($productCategory);
        }

        /**
         * Get productCategories.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductCategories()
        {
            return $this->ProductCategories;
        }

        /**
         * Add productClass.
         *
         * @param \Eccube\Entity\ProductClass $productClass
         *
         * @return Product
         */
        public function addProductClass(ProductClass $productClass)
        {
            $this->ProductClasses[] = $productClass;

            return $this;
        }

        /**
         * Remove productClass.
         *
         * @param \Eccube\Entity\ProductClass $productClass
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductClass(ProductClass $productClass)
        {
            return $this->ProductClasses->removeElement($productClass);
        }

        /**
         * Get productClasses.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductClasses()
        {
            return $this->ProductClasses;
        }

        /**
         * Add productImage.
         *
         * @param \Eccube\Entity\ProductImage $productImage
         *
         * @return Product
         */
        public function addProductImage(ProductImage $productImage)
        {
            $this->ProductImage[] = $productImage;

            return $this;
        }

        /**
         * Remove productImage.
         *
         * @param \Eccube\Entity\ProductImage $productImage
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductImage(ProductImage $productImage)
        {
            return $this->ProductImage->removeElement($productImage);
        }

        /**
         * Get productImage.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductImage()
        {
            return $this->ProductImage;
        }

        /**
         * Add productTag.
         *
         * @param \Eccube\Entity\ProductTag $productTag
         *
         * @return Product
         */
        public function addProductTag(ProductTag $productTag)
        {
            $this->ProductTag[] = $productTag;

            return $this;
        }

        /**
         * Remove productTag.
         *
         * @param \Eccube\Entity\ProductTag $productTag
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductTag(ProductTag $productTag)
        {
            return $this->ProductTag->removeElement($productTag);
        }

        /**
         * Get productTag.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductTag()
        {
            return $this->ProductTag;
        }

        /**
         * Get Tag
         * フロント側タグsort_no順の配列を作成する
         *
         * @return []Tag
         */
        public function getTags()
        {
            $tags = [];

            foreach ($this->getProductTag() as $productTag) {
                $tags[] = $productTag->getTag();
            }

            usort($tags, function (Tag $tag1, Tag $tag2) {
                return $tag1->getSortNo() < $tag2->getSortNo();
            });

            return $tags;
        }

        /**
         * Add customerFavoriteProduct.
         *
         * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return Product
         */
        public function addCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct)
        {
            $this->CustomerFavoriteProducts[] = $customerFavoriteProduct;

            return $this;
        }

        /**
         * Remove customerFavoriteProduct.
         *
         * @param \Eccube\Entity\CustomerFavoriteProduct $customerFavoriteProduct
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct)
        {
            return $this->CustomerFavoriteProducts->removeElement($customerFavoriteProduct);
        }

        /**
         * Get customerFavoriteProducts.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getCustomerFavoriteProducts()
        {
            return $this->CustomerFavoriteProducts;
        }

        /**
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return Product
         */
        public function setCreator(Member $creator = null)
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
         * Set status.
         *
         * @param \Eccube\Entity\Master\ProductStatus|null $status
         *
         * @return Product
         */
        public function setStatus(Master\ProductStatus $status = null)
        {
            $this->Status = $status;

            return $this;
        }

        /**
         * Get status.
         *
         * @return \Eccube\Entity\Master\ProductStatus|null
         */
        public function getStatus()
        {
            return $this->Status;
        }
    }
}

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

if (!class_exists('\Eccube\Entity\ProductCategory')) {
    /**
     * ProductCategory
     *
     * @ORM\Table(name="dtb_product_category")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\ProductCategoryRepository")
     */
    class ProductCategory extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="product_id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="NONE")
         */
        private $product_id;

        /**
         * @var int
         *
         * @ORM\Column(name="category_id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="NONE")
         */
        private $category_id;

        /**
         * @var \Eccube\Entity\Product
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product", inversedBy="ProductCategories")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
         * })
         */
        private $Product;

        /**
         * @var \Eccube\Entity\Category
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Category", inversedBy="ProductCategories")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
         * })
         */
        private $Category;

        /**
         * Set productId.
         *
         * @param int $productId
         *
         * @return ProductCategory
         */
        public function setProductId($productId)
        {
            $this->product_id = $productId;

            return $this;
        }

        /**
         * Get productId.
         *
         * @return int
         */
        public function getProductId()
        {
            return $this->product_id;
        }

        /**
         * Set categoryId.
         *
         * @param int $categoryId
         *
         * @return ProductCategory
         */
        public function setCategoryId($categoryId)
        {
            $this->category_id = $categoryId;

            return $this;
        }

        /**
         * Get categoryId.
         *
         * @return int
         */
        public function getCategoryId()
        {
            return $this->category_id;
        }

        /**
         * Set product.
         *
         * @param \Eccube\Entity\Product|null $product
         *
         * @return ProductCategory
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
         * Set category.
         *
         * @param \Eccube\Entity\Category|null $category
         *
         * @return ProductCategory
         */
        public function setCategory(Category $category = null)
        {
            $this->Category = $category;

            return $this;
        }

        /**
         * Get category.
         *
         * @return \Eccube\Entity\Category|null
         */
        public function getCategory()
        {
            return $this->Category;
        }
    }
}

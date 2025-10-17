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
use Eccube\Repository\ProductCategoryRepository;

if (!class_exists(ProductCategory::class)) {
    /**
     * ProductCategory
     */
    #[ORM\Table(name: 'dtb_product_category')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: ProductCategoryRepository::class)]
    class ProductCategory extends AbstractEntity
    {
        /**
         * @var int
         */
        #[ORM\Column(name: 'product_id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        private $product_id;

        /**
         * @var int
         */
        #[ORM\Column(name: 'category_id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        private $category_id;

        /**
         * @var Product|null
         */
        #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'ProductCategories')]
        #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
        private $Product;

        /**
         * @var Category|null
         */
        #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'ProductCategories')]
        #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
        private $Category;

        /**
         * Set productId.
         *
         * @param int $productId
         *
         * @return ProductCategory
         */
        public function setProductId($productId): ProductCategory
        {
            $this->product_id = $productId;

            return $this;
        }

        /**
         * Get productId.
         *
         * @return int
         */
        public function getProductId(): int
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
        public function setCategoryId($categoryId): ProductCategory
        {
            $this->category_id = $categoryId;

            return $this;
        }

        /**
         * Get categoryId.
         *
         * @return int
         */
        public function getCategoryId(): int
        {
            return $this->category_id;
        }

        /**
         * Set product.
         *
         * @param Product|null $product
         *
         * @return ProductCategory
         */
        public function setProduct(?Product $product = null): ProductCategory
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
         * Set category.
         *
         * @param Category|null $category
         *
         * @return ProductCategory
         */
        public function setCategory(?Category $category = null): ProductCategory
        {
            $this->Category = $category;

            return $this;
        }

        /**
         * Get category.
         *
         * @return Category|null
         */
        public function getCategory(): ?Category
        {
            return $this->Category;
        }
    }
}

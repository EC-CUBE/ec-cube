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
use Eccube\Repository\ProductCategoryRepository;

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
    #[ORM\Column(name: 'product_id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $product_id = null;

    #[ORM\Column(name: 'category_id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $category_id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'ProductCategories')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private ?Product $Product = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'ProductCategories')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    private ?Category $Category = null;

    /**
     * Set productId.
     */
    public function setProductId(int $productId): ProductCategory
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get productId.
     */
    public function getProductId(): int
    {
        return $this->product_id;
    }

    /**
     * Set categoryId.
     */
    public function setCategoryId(int $categoryId): ProductCategory
    {
        $this->category_id = $categoryId;

        return $this;
    }

    /**
     * Get categoryId.
     */
    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    /**
     * Set product.
     */
    public function setProduct(?Product $product = null): ProductCategory
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
     * Set category.
     */
    public function setCategory(?Category $category = null): ProductCategory
    {
        $this->Category = $category;

        return $this;
    }

    /**
     * Get category.
     */
    public function getCategory(): ?Category
    {
        return $this->Category;
    }
}

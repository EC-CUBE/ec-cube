<?php

namespace Eccube\Entity;

/**
 * ProductCategory
 */
class ProductCategory extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $product_id;

    /**
     * @var integer
     */
    private $category_id;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\Category
     */
    private $Category;

    /**
     * Set product_id
     *
     * @param  integer         $productId
     * @return ProductCategory
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get product_id
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set category_id
     *
     * @param  integer         $categoryId
     * @return ProductCategory
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;

        return $this;
    }

    /**
     * Get category_id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Set rank
     *
     * @param  integer         $rank
     * @return ProductCategory
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set Product
     *
     * @param  \Eccube\Entity\Product $product
     * @return ProductCategory
     */
    public function setProduct(\Eccube\Entity\Product $product = null)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get Product
     *
     * @return \Eccube\Entity\Product
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * Set Category
     *
     * @param  \Eccube\Entity\Category $category
     * @return ProductCategory
     */
    public function setCategory(\Eccube\Entity\Category $category = null)
    {
        $this->Category = $category;

        return $this;
    }

    /**
     * Get Category
     *
     * @return \Eccube\Entity\Category
     */
    public function getCategory()
    {
        return $this->Category;
    }
}

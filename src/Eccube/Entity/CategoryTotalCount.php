<?php

namespace Eccube\Entity;

/**
 * CategoryTotalCount
 */
class CategoryTotalCount extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $category_id;

    /**
     * @var integer
     */
    private $product_count;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \Eccube\Entity\Category
     */
    private $Category;

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
     * Set product_count
     *
     * @param  integer            $productCount
     * @return CategoryTotalCount
     */
    public function setProductCount($productCount)
    {
        $this->product_count = $productCount;

        return $this;
    }

    /**
     * Get product_count
     *
     * @return integer
     */
    public function getProductCount()
    {
        return $this->product_count;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime          $createDate
     * @return CategoryTotalCount
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
     * Set Category
     *
     * @param  \Eccube\Entity\Category $category
     * @return CategoryTotalCount
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

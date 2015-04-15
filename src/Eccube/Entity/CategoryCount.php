<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryCount
 */
class CategoryCount
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $product_count;

    /**
     * @var \DateTime
     */
    private $create_date;


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
     * Set product_count
     *
     * @param integer $productCount
     * @return CategoryCount
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
     * @param \DateTime $createDate
     * @return CategoryCount
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
}

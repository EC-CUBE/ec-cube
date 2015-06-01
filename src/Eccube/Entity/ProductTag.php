<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductTag
 */
class ProductTag extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\Master\Tag
     */
    private $Tag;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;


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
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return ProductTag
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
     * Set Product
     *
     * @param \Eccube\Entity\Product $product
     * @return ProductTag
     */
    public function setProduct(\Eccube\Entity\Product $product)
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
     * Set Tag
     *
     * @param \Eccube\Entity\Master\Tag $tag
     * @return ProductTag
     */
    public function setTag(\Eccube\Entity\Master\Tag $tag)
    {
        $this->Tag = $tag;

        return $this;
    }

    /**
     * Get Tag
     *
     * @return \Eccube\Entity\Master\Tag 
     */
    public function getTag()
    {
        return $this->Tag;
    }

    /**
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
     * @return ProductTag
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
}

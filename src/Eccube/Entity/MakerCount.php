<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MakerCount
 */
class MakerCount extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $maker_id;

    /**
     * @var integer
     */
    private $product_count;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \Eccube\Entity\Maker
     */
    private $Maker;


    /**
     * Get maker_id
     *
     * @return integer 
     */
    public function getMakerId()
    {
        return $this->maker_id;
    }

    /**
     * Set product_count
     *
     * @param integer $productCount
     * @return MakerCount
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
     * @return MakerCount
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
     * Set Maker
     *
     * @param \Eccube\Entity\Maker $maker
     * @return MakerCount
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
}

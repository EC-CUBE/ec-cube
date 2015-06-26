<?php

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 */
class Tag extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductTag;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @param string $name
     * @return Tag
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
     * Set rank
     *
     * @param integer $rank
     * @return Tag
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
     * Add ProductTag
     *
     * @param \Eccube\Entity\ProductTag $productTag
     * @return Tag
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

    /**
     * Set id
     *
     * @param integer $id
     * @return Tag
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}

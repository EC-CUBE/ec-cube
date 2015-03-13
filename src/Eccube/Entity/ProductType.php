<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductType
 */
class ProductType
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
    private $ProductClasses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ProductClasses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ProductType
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
     * @return ProductType
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
     * Add ProductClasses
     *
     * @param \Eccube\Entity\ProductClass $productClasses
     * @return ProductType
     */
    public function addProductClass(\Eccube\Entity\ProductClass $productClasses)
    {
        $this->ProductClasses[] = $productClasses;

        return $this;
    }

    /**
     * Remove ProductClasses
     *
     * @param \Eccube\Entity\ProductClass $productClasses
     */
    public function removeProductClass(\Eccube\Entity\ProductClass $productClasses)
    {
        $this->ProductClasses->removeElement($productClasses);
    }

    /**
     * Get ProductClasses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductClasses()
    {
        return $this->ProductClasses;
    }
}

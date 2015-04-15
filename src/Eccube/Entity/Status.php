<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Status
 */
class Status
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
    private $ProductStatuses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ProductStatuses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Status
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
     * @return Status
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
     * Add ProductStatuses
     *
     * @param \Eccube\Entity\ProductStatus $productStatuses
     * @return Status
     */
    public function addProductStatus(\Eccube\Entity\ProductStatus $productStatuses)
    {
        $this->ProductStatuses[] = $productStatuses;

        return $this;
    }

    /**
     * Remove ProductStatuses
     *
     * @param \Eccube\Entity\ProductStatus $productStatuses
     */
    public function removeProductStatus(\Eccube\Entity\ProductStatus $productStatuses)
    {
        $this->ProductStatuses->removeElement($productStatuses);
    }

    /**
     * Get ProductStatuses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductStatuses()
    {
        return $this->ProductStatuses;
    }
}

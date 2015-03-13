<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sex
 */
class Sex
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
    private $Sexes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Sexes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Sex
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
     * @return Sex
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
     * Add Sexes
     *
     * @param \Eccube\Entity\Sex $sexes
     * @return Sex
     */
    public function addSex(\Eccube\Entity\Sex $sexes)
    {
        $this->Sexes[] = $sexes;

        return $this;
    }

    /**
     * Remove Sexes
     *
     * @param \Eccube\Entity\Sex $sexes
     */
    public function removeSex(\Eccube\Entity\Sex $sexes)
    {
        $this->Sexes->removeElement($sexes);
    }

    /**
     * Get Sexes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSexes()
    {
        return $this->Sexes;
    }
}

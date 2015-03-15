<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recommend
 */
class Recommend
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
    private $Reviews;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Reviews = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Recommend
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
     * @return Recommend
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
     * Add Reviews
     *
     * @param \Eccube\Entity\Review $reviews
     * @return Recommend
     */
    public function addReview(\Eccube\Entity\Review $reviews)
    {
        $this->Reviews[] = $reviews;

        return $this;
    }

    /**
     * Remove Reviews
     *
     * @param \Eccube\Entity\Review $reviews
     */
    public function removeReview(\Eccube\Entity\Review $reviews)
    {
        $this->Reviews->removeElement($reviews);
    }

    /**
     * Get Reviews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReviews()
    {
        return $this->Reviews;
    }
}

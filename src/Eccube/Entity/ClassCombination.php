<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClassCombination
 */
class ClassCombination
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $parent_class_combination_id;

    /**
     * @var integer
     */
    private $classcategory_id;

    /**
     * @var integer
     */
    private $level;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductClasses;

    /**
     * @var \Eccube\Entity\ClassCombination
     */
    private $Parent;

    /**
     * @var \Eccube\Entity\ClassCategory
     */
    private $ClassCategory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set parent_class_combination_id
     *
     * @param integer $parentClassCombinationId
     * @return ClassCombination
     */
    public function setParentClassCombinationId($parentClassCombinationId)
    {
        $this->parent_class_combination_id = $parentClassCombinationId;

        return $this;
    }

    /**
     * Get parent_class_combination_id
     *
     * @return integer 
     */
    public function getParentClassCombinationId()
    {
        return $this->parent_class_combination_id;
    }

    /**
     * Set classcategory_id
     *
     * @param integer $classcategoryId
     * @return ClassCombination
     */
    public function setClasscategoryId($classcategoryId)
    {
        $this->classcategory_id = $classcategoryId;

        return $this;
    }

    /**
     * Get classcategory_id
     *
     * @return integer 
     */
    public function getClasscategoryId()
    {
        return $this->classcategory_id;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return ClassCombination
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Add Children
     *
     * @param \Eccube\Entity\ClassCombination $children
     * @return ClassCombination
     */
    public function addChild(\Eccube\Entity\ClassCombination $children)
    {
        $this->Children[] = $children;

        return $this;
    }

    /**
     * Remove Children
     *
     * @param \Eccube\Entity\ClassCombination $children
     */
    public function removeChild(\Eccube\Entity\ClassCombination $children)
    {
        $this->Children->removeElement($children);
    }

    /**
     * Get Children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->Children;
    }

    /**
     * Add ProductClasses
     *
     * @param \Eccube\Entity\ProductClass $productClasses
     * @return ClassCombination
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

    /**
     * Set Parent
     *
     * @param \Eccube\Entity\ClassCombination $parent
     * @return ClassCombination
     */
    public function setParent(\Eccube\Entity\ClassCombination $parent = null)
    {
        $this->Parent = $parent;

        return $this;
    }

    /**
     * Get Parent
     *
     * @return \Eccube\Entity\ClassCombination 
     */
    public function getParent()
    {
        return $this->Parent;
    }

    /**
     * Set ClassCategory
     *
     * @param \Eccube\Entity\ClassCategory $classCategory
     * @return ClassCombination
     */
    public function setClassCategory(\Eccube\Entity\ClassCategory $classCategory = null)
    {
        $this->ClassCategory = $classCategory;

        return $this;
    }

    /**
     * Get ClassCategory
     *
     * @return \Eccube\Entity\ClassCategory 
     */
    public function getClassCategory()
    {
        return $this->ClassCategory;
    }
}

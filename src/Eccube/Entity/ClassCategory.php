<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClassCategory
 */
class ClassCategory
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
    private $class_id;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var integer
     */
    private $creator_id;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ClassCombinations;

    /**
     * @var \Eccube\Entity\ClassName
     */
    private $ClassName;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ClassCombinations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ClassCategory
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
     * Set class_id
     *
     * @param integer $classId
     * @return ClassCategory
     */
    public function setClassId($classId)
    {
        $this->class_id = $classId;

        return $this;
    }

    /**
     * Get class_id
     *
     * @return integer 
     */
    public function getClassId()
    {
        return $this->class_id;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return ClassCategory
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
     * Set creator_id
     *
     * @param integer $creatorId
     * @return ClassCategory
     */
    public function setCreatorId($creatorId)
    {
        $this->creator_id = $creatorId;

        return $this;
    }

    /**
     * Get creator_id
     *
     * @return integer 
     */
    public function getCreatorId()
    {
        return $this->creator_id;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return ClassCategory
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
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return ClassCategory
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return ClassCategory
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer 
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Add Children
     *
     * @param \Eccube\Entity\ClassCombination $children
     * @return ClassCategory
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
     * Add ClassCombinations
     *
     * @param \Eccube\Entity\ClassCombination $classCombinations
     * @return ClassCategory
     */
    public function addClassCombination(\Eccube\Entity\ClassCombination $classCombinations)
    {
        $this->ClassCombinations[] = $classCombinations;

        return $this;
    }

    /**
     * Remove ClassCombinations
     *
     * @param \Eccube\Entity\ClassCombination $classCombinations
     */
    public function removeClassCombination(\Eccube\Entity\ClassCombination $classCombinations)
    {
        $this->ClassCombinations->removeElement($classCombinations);
    }

    /**
     * Get ClassCombinations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClassCombinations()
    {
        return $this->ClassCombinations;
    }

    /**
     * Set ClassName
     *
     * @param \Eccube\Entity\ClassName $className
     * @return ClassCategory
     */
    public function setClassName(\Eccube\Entity\ClassName $className = null)
    {
        $this->ClassName = $className;

        return $this;
    }

    /**
     * Get ClassName
     *
     * @return \Eccube\Entity\ClassName 
     */
    public function getClassName()
    {
        return $this->ClassName;
    }

    /**
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
     * @return ClassCategory
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
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
    /**
     * @ORM\PrePersist
     */
    public function setCreateDateAuto()
    {
        // Add your code here
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDateAuto()
    {
        // Add your code here
    }
}

<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 */
class Category
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $category_name;

    /**
     * @var integer
     */
    private $parent_category_id;

    /**
     * @var integer
     */
    private $level;

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
     * @var \Eccube\Entity\CategoryCount
     */
    private $CategoryCount;

    /**
     * @var \Eccube\Entity\CategoryTotalCount
     */
    private $CategoryTotalCount;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ProductCategories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Children;

    /**
     * @var \Eccube\Entity\Category
     */
    private $Parent;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ProductCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set category_name
     *
     * @param string $categoryName
     * @return Category
     */
    public function setCategoryName($categoryName)
    {
        $this->category_name = $categoryName;

        return $this;
    }

    /**
     * Get category_name
     *
     * @return string 
     */
    public function getCategoryName()
    {
        return $this->category_name;
    }

    /**
     * Set parent_category_id
     *
     * @param integer $parentCategoryId
     * @return Category
     */
    public function setParentCategoryId($parentCategoryId)
    {
        $this->parent_category_id = $parentCategoryId;

        return $this;
    }

    /**
     * Get parent_category_id
     *
     * @return integer 
     */
    public function getParentCategoryId()
    {
        return $this->parent_category_id;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return Category
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
     * Set rank
     *
     * @param integer $rank
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * Set CategoryCount
     *
     * @param \Eccube\Entity\CategoryCount $categoryCount
     * @return Category
     */
    public function setCategoryCount(\Eccube\Entity\CategoryCount $categoryCount = null)
    {
        $this->CategoryCount = $categoryCount;

        return $this;
    }

    /**
     * Get CategoryCount
     *
     * @return \Eccube\Entity\CategoryCount 
     */
    public function getCategoryCount()
    {
        return $this->CategoryCount;
    }

    /**
     * Set CategoryTotalCount
     *
     * @param \Eccube\Entity\CategoryTotalCount $categoryTotalCount
     * @return Category
     */
    public function setCategoryTotalCount(\Eccube\Entity\CategoryTotalCount $categoryTotalCount = null)
    {
        $this->CategoryTotalCount = $categoryTotalCount;

        return $this;
    }

    /**
     * Get CategoryTotalCount
     *
     * @return \Eccube\Entity\CategoryTotalCount 
     */
    public function getCategoryTotalCount()
    {
        return $this->CategoryTotalCount;
    }

    /**
     * Add ProductCategories
     *
     * @param \Eccube\Entity\ProductCategory $productCategories
     * @return Category
     */
    public function addProductCategory(\Eccube\Entity\ProductCategory $productCategories)
    {
        $this->ProductCategories[] = $productCategories;

        return $this;
    }

    /**
     * Remove ProductCategories
     *
     * @param \Eccube\Entity\ProductCategory $productCategories
     */
    public function removeProductCategory(\Eccube\Entity\ProductCategory $productCategories)
    {
        $this->ProductCategories->removeElement($productCategories);
    }

    /**
     * Get ProductCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductCategories()
    {
        return $this->ProductCategories;
    }

    /**
     * Add Children
     *
     * @param \Eccube\Entity\Category $children
     * @return Category
     */
    public function addChild(\Eccube\Entity\Category $children)
    {
        $this->Children[] = $children;

        return $this;
    }

    /**
     * Remove Children
     *
     * @param \Eccube\Entity\Category $children
     */
    public function removeChild(\Eccube\Entity\Category $children)
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
     * Set Parent
     *
     * @param \Eccube\Entity\Category $parent
     * @return Category
     */
    public function setParent(\Eccube\Entity\Category $parent = null)
    {
        $this->Parent = $parent;

        return $this;
    }

    /**
     * Get Parent
     *
     * @return \Eccube\Entity\Category 
     */
    public function getParent()
    {
        return $this->Parent;
    }

    /**
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
     * @return Category
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

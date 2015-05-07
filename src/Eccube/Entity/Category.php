<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 */
class Category extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return integer
     */
    public function countBranches()
    {
        $count = 1;

        foreach ($this->getChildren() as $Child) {
            $count += $Child->countBranches();
        }

        return $count;
    }

    /**
     * @param  \Doctrine\ORM\EntityManager $em
     * @param  integer                     $rank
     * @return \Eccube\Entity\Category
     */
    public function calcChildrenRank(\Doctrine\ORM\EntityManager $em, $rank)
    {
        $this->setRank($this->getRank() + $rank);
        $em->persist($this);

        foreach ($this->getChildren() as $Child) {
            $Child->calcChildrenRank($em, $rank);
        }

        return $this;
    }

    public function getParents()
    {
        $path = $this->getPath();
        array_pop($path);

        return $path;
    }

    public function getPath()
    {
        $path = array();
        $Category = $this;

        $max = 10;
        while ($max--) {
            $path[] = $Category;

            $Category = $Category->getParent();
            if (!$Category || !$Category->getId()) {
                break;
            }
        }

        return array_reverse($path);
    }

    public function getNameWithLevel()
    {
        return str_repeat('　', $this->getLevel() - 1) . $this->getName();
    }

    public function getDescendants()
    {
        $DescendantCategories = array();

        $max = 10;
        $ChildCategories = $this->getChildren();
        foreach ($ChildCategories as $ChildCategory) {
            $DescendantCategories[$ChildCategory->getId()] = $ChildCategory;
            $DescendantCategories2 = $ChildCategory->getDescendants();
            foreach ($DescendantCategories2 as $DescendantCategory) {
                $DescendantCategories[$DescendantCategory->getId()] = $DescendantCategory;
            }
        }

        return $DescendantCategories;
    }

    public function getSelfAndDescendants()
    {
        return array_merge(array($this), $this->getDescendants());

    }

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
    private $level;

    /**
     * @var integer
     */
    private $rank;

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
     * Set name
     *
     * @param  string   $name
     * @return Category
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
     * Set level
     *
     * @param  integer  $level
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
     * @param  integer  $rank
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
     * Set create_date
     *
     * @param  \DateTime $createDate
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
     * @param  \DateTime $updateDate
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
     * @param  integer  $delFlg
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
     * @param  \Eccube\Entity\CategoryCount $categoryCount
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
     * @param  \Eccube\Entity\CategoryTotalCount $categoryTotalCount
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
     * @param  \Eccube\Entity\ProductCategory $productCategories
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
     * @param  \Eccube\Entity\Category $children
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
     * @param  \Eccube\Entity\Category $parent
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
     * @param  \Eccube\Entity\Member $creator
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
}

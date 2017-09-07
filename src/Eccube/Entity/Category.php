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

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="dtb_category")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\CategoryRepository")
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
     * カテゴリに紐づく商品があるかどうかを調べる.
     *
     * ProductCategoriesはExtra Lazyのため, lengthやcountで評価した際にはCOUNTのSQLが発行されるが,
     * COUNT自体が重いので, LIMIT 1で取得し存在チェックを行う.
     *
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/working-with-associations.html#filtering-collections
     * @return bool
     */
    public function hasProductCategories()
    {
        $criteria = Criteria::create()
            ->orderBy(array('category_id' => Criteria::ASC))
            ->setFirstResult(0)
            ->setMaxResults(1);

        return $this->ProductCategories->matching($criteria)->count() === 1;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="category_name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="hierarchy", type="integer")
     */
    private $level;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer")
     */
    private $rank;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductCategory", mappedBy="Category", fetch="EXTRA_LAZY")
     */
    private $ProductCategories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\Category", mappedBy="Parent")
     * @ORM\OrderBy({
     *     "rank"="DESC"
     * })
     */
    private $Children;

    /**
     * @var \Eccube\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Category", inversedBy="Children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_category_id", referencedColumnName="category_id")
     * })
     */
    private $Parent;

    /**
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="member_id")
     * })
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set level.
     *
     * @param int $level
     *
     * @return Category
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return Category
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return Category
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     *
     * @return Category
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Add productCategory.
     *
     * @param \Eccube\Entity\ProductCategory $productCategory
     *
     * @return Category
     */
    public function addProductCategory(\Eccube\Entity\ProductCategory $productCategory)
    {
        $this->ProductCategories[] = $productCategory;

        return $this;
    }

    /**
     * Remove productCategory.
     *
     * @param \Eccube\Entity\ProductCategory $productCategory
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProductCategory(\Eccube\Entity\ProductCategory $productCategory)
    {
        return $this->ProductCategories->removeElement($productCategory);
    }

    /**
     * Get productCategories.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductCategories()
    {
        return $this->ProductCategories;
    }

    /**
     * Add child.
     *
     * @param \Eccube\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(\Eccube\Entity\Category $child)
    {
        $this->Children[] = $child;

        return $this;
    }

    /**
     * Remove child.
     *
     * @param \Eccube\Entity\Category $child
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeChild(\Eccube\Entity\Category $child)
    {
        return $this->Children->removeElement($child);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->Children;
    }

    /**
     * Set parent.
     *
     * @param \Eccube\Entity\Category|null $parent
     *
     * @return Category
     */
    public function setParent(\Eccube\Entity\Category $parent = null)
    {
        $this->Parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return \Eccube\Entity\Category|null
     */
    public function getParent()
    {
        return $this->Parent;
    }

    /**
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     * @return Category
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     *
     * @return \Eccube\Entity\Member|null
     */
    public function getCreator()
    {
        return $this->Creator;
    }
}

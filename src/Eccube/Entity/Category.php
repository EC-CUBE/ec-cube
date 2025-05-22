<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\Category')) {
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
            return (string) $this->getName();
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
         * @param  integer                     $sortNo
         *
         * @return \Eccube\Entity\Category
         */
        public function calcChildrenSortNo(\Doctrine\ORM\EntityManager $em, $sortNo)
        {
            $this->setSortNo($this->getSortNo() + $sortNo);
            $em->persist($this);

            foreach ($this->getChildren() as $Child) {
                $Child->calcChildrenSortNo($em, $sortNo);
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
            $path = [];
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
            return str_repeat('　', $this->getHierarchy() - 1).$this->getName();
        }

        public function getDescendants()
        {
            $DescendantCategories = [];

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
            return array_merge([$this], $this->getDescendants());
        }

        /**
         * カテゴリに紐づく商品があるかどうかを調べる.
         *
         * ProductCategoriesはExtra Lazyのため, lengthやcountで評価した際にはCOUNTのSQLが発行されるが,
         * COUNT自体が重いので, LIMIT 1で取得し存在チェックを行う.
         *
         * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/working-with-associations.html#filtering-collections
         *
         * @return bool
         */
        public function hasProductCategories()
        {
            $criteria = Criteria::create()
            ->orderBy(['category_id' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1);

            return $this->ProductCategories->matching($criteria)->count() > 0;
        }

        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
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
         * @ORM\Column(name="hierarchy", type="integer", options={"unsigned":true})
         */
        private $hierarchy;

        /**
         * @var int
         *
         * @ORM\Column(name="sort_no", type="integer")
         */
        private $sort_no;

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
         *     "sort_no"="DESC"
         * })
         */
        private $Children;

        /**
         * @var \Eccube\Entity\Category
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Category", inversedBy="Children")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id")
         * })
         */
        private $Parent;

        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
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
         * Set hierarchy.
         *
         * @param int $hierarchy
         *
         * @return Category
         */
        public function setHierarchy($hierarchy)
        {
            $this->hierarchy = $hierarchy;

            return $this;
        }

        /**
         * Get hierarchy.
         *
         * @return int
         */
        public function getHierarchy()
        {
            return $this->hierarchy;
        }

        /**
         * Set sortNo.
         *
         * @param int $sortNo
         *
         * @return Category
         */
        public function setSortNo($sortNo)
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int
         */
        public function getSortNo()
        {
            return $this->sort_no;
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
        public function addProductCategory(ProductCategory $productCategory)
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
        public function removeProductCategory(ProductCategory $productCategory)
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
        public function addChild(Category $child)
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
        public function removeChild(Category $child)
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
        public function setParent(Category $parent = null)
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
        public function setCreator(Member $creator = null)
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
}

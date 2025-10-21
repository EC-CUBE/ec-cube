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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Eccube\Repository\CategoryRepository;

if (!class_exists(Category::class)) {
    /**
     * Category
     */
    #[ORM\Table(name: 'dtb_category')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CategoryRepository::class)]
    class Category extends AbstractEntity implements \Stringable
    {
        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return $this->getName();
        }

        /**
         * @return int
         */
        public function countBranches(): int
        {
            $count = 1;

            foreach ($this->getChildren() as $Child) {
                $count += $Child->countBranches();
            }

            return $count;
        }

        /**
         * @param  EntityManager $em
         * @param  int                     $sortNo
         *
         * @return Category
         */
        public function calcChildrenSortNo(EntityManager $em, $sortNo): Category
        {
            $this->setSortNo($this->getSortNo() + $sortNo);
            $em->persist($this);

            foreach ($this->getChildren() as $Child) {
                $Child->calcChildrenSortNo($em, $sortNo);
            }

            return $this;
        }

        /**
         * @return array<mixed>
         */
        public function getParents(): array
        {
            $path = $this->getPath();
            array_pop($path);

            return $path;
        }

        /**
         * @return array<mixed>
         */
        public function getPath(): array
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

        /**
         * @return string
         */
        public function getNameWithLevel(): string
        {
            return str_repeat('　', $this->getHierarchy() - 1).$this->getName();
        }

        /**
         * @return array<int, mixed>
         */
        public function getDescendants(): array
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

        /**
         * @return Category[]|mixed[]
         */
        public function getSelfAndDescendants(): array
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
        public function hasProductCategories(): bool
        {
            $criteria = Criteria::create()
            ->orderBy(['category_id' => Criteria::ASC])
            ->setFirstResult(0)
            ->setMaxResults(1);

            /** @var PersistentCollection <int,ProductCategory> */
            $ProductCategories = $this->ProductCategories;

            return $ProductCategories->matching($criteria)->count() > 0;
        }

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'category_name', type: 'string', length: 255)]
        private $name;

        /**
         * @var int
         */
        #[ORM\Column(name: 'hierarchy', type: 'integer', options: ['unsigned' => true])]
        private $hierarchy;

        /**
         * @var int
         */
        #[ORM\Column(name: 'sort_no', type: 'integer')]
        private $sort_no;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * @var Collection<int, ProductCategory>
         */
        #[ORM\OneToMany(targetEntity: ProductCategory::class, mappedBy: 'Category', fetch: 'EXTRA_LAZY')]
        private $ProductCategories;

        /**
         * @var Collection<int, Category>
         */
        #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'Parent')]
        #[ORM\OrderBy(['sort_no' => 'DESC'])]
        private $Children;

        /**
         * @var Category|null
         */
        #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'Children')]
        #[ORM\JoinColumn(name: 'parent_category_id', referencedColumnName: 'id')]
        private $Parent;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->ProductCategories = new ArrayCollection();
            $this->Children = new ArrayCollection();
        }

        /**
         * Get id.
         *
         * @return int|null
         */
        public function getId(): ?int
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
        public function setName($name): Category
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string
         */
        public function getName(): string
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
        public function setHierarchy($hierarchy): Category
        {
            $this->hierarchy = $hierarchy;

            return $this;
        }

        /**
         * Get hierarchy.
         *
         * @return int
         */
        public function getHierarchy(): int
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
        public function setSortNo($sortNo): Category
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int
         */
        public function getSortNo(): int
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
        public function setCreateDate($createDate): Category
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime|null
         */
        public function getCreateDate(): ?\DateTime
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
        public function setUpdateDate($updateDate): Category
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime|null
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * Add productCategory.
         *
         * @param ProductCategory $productCategory
         *
         * @return Category
         */
        public function addProductCategory(ProductCategory $productCategory): Category
        {
            $this->ProductCategories[] = $productCategory;

            return $this;
        }

        /**
         * Remove productCategory.
         *
         * @param ProductCategory $productCategory
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductCategory(ProductCategory $productCategory): bool
        {
            return $this->ProductCategories->removeElement($productCategory);
        }

        /**
         * Get productCategories.
         *
         * @return Collection<int, ProductCategory>
         */
        public function getProductCategories(): Collection
        {
            return $this->ProductCategories;
        }

        /**
         * Add child.
         *
         * @param Category $child
         *
         * @return Category
         */
        public function addChild(Category $child): Category
        {
            $this->Children[] = $child;

            return $this;
        }

        /**
         * Remove child.
         *
         * @param Category $child
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeChild(Category $child): bool
        {
            return $this->Children->removeElement($child);
        }

        /**
         * Get children.
         *
         * @return Collection<int, Category>
         */
        public function getChildren(): Collection
        {
            return $this->Children;
        }

        /**
         * Set parent.
         *
         * @param Category|null $parent
         *
         * @return Category
         */
        public function setParent(?Category $parent = null): Category
        {
            $this->Parent = $parent;

            return $this;
        }

        /**
         * Get parent.
         *
         * @return Category|null
         */
        public function getParent(): ?Category
        {
            return $this->Parent;
        }

        /**
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return Category
         */
        public function setCreator(?Member $creator = null): Category
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return Member|null
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }
    }
}

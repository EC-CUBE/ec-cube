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
use Doctrine\Common\Collections\Order;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Eccube\Repository\CategoryRepository;

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
    #[\Override]
    public function __toString(): string
    {
        return $this->getName();
    }

    public function countBranches(): int
    {
        $count = 1;

        foreach ($this->getChildren() as $Child) {
            $count += $Child->countBranches();
        }

        return $count;
    }

    public function calcChildrenSortNo(EntityManager $em, int $sortNo): Category
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
     */
    public function hasProductCategories(): bool
    {
        $criteria = Criteria::create()->orderBy(['category_id' => Order::Ascending])
        ->setFirstResult(0)
        ->setMaxResults(1);

        /** @var PersistentCollection <int,ProductCategory> */
        $ProductCategories = $this->ProductCategories;

        return $ProductCategories->matching($criteria)->count() > 0;
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'category_name', type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'hierarchy', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $hierarchy = null;

    #[ORM\Column(name: 'sort_no', type: Types::INTEGER)]
    private ?int $sort_no = null;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    /**
     * @var Collection<int, ProductCategory>
     */
    #[ORM\OneToMany(targetEntity: ProductCategory::class, mappedBy: 'Category', fetch: 'EXTRA_LAZY')]
    private Collection $ProductCategories;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'Parent')]
    #[ORM\OrderBy(['sort_no' => 'DESC'])]
    private Collection $Children;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'Children')]
    #[ORM\JoinColumn(name: 'parent_category_id', referencedColumnName: 'id')]
    private ?Category $Parent = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
    private ?Member $Creator = null;

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
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(string $name): Category
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set hierarchy.
     */
    public function setHierarchy(int $hierarchy): Category
    {
        $this->hierarchy = $hierarchy;

        return $this;
    }

    /**
     * Get hierarchy.
     */
    public function getHierarchy(): int
    {
        return $this->hierarchy;
    }

    /**
     * Set sortNo.
     */
    public function setSortNo(int $sortNo): Category
    {
        $this->sort_no = $sortNo;

        return $this;
    }

    /**
     * Get sortNo.
     */
    public function getSortNo(): int
    {
        return $this->sort_no;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): Category
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     */
    public function setUpdateDate(\DateTime $updateDate): Category
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    /**
     * Add productCategory.
     */
    public function addProductCategory(ProductCategory $productCategory): Category
    {
        $this->ProductCategories[] = $productCategory;

        return $this;
    }

    /**
     * Remove productCategory.
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
     */
    public function addChild(Category $child): Category
    {
        $this->Children[] = $child;

        return $this;
    }

    /**
     * Remove child.
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
     */
    public function setParent(?Category $parent = null): Category
    {
        $this->Parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     */
    public function getParent(): ?Category
    {
        return $this->Parent;
    }

    /**
     * Set creator.
     */
    public function setCreator(?Member $creator = null): Category
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     */
    public function getCreator(): ?Member
    {
        return $this->Creator;
    }
}

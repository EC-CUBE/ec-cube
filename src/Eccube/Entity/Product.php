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
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Repository\ProductRepository;

if (!class_exists(Product::class)) {
    /**
     * Product
     */
    #[ORM\Table(name: 'dtb_product')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: ProductRepository::class)]
    class Product extends AbstractEntity implements \Stringable
    {
        private bool $_calc = false;
        /**
         * @var array<int, bool>
         */
        private array $stockFinds = [];
        /**
         * @var array<int, string|null>
         */
        private array $stocks = [];
        /**
         * @var array<int, bool>
         */
        private array $stockUnlimiteds = [];
        /**
         * @var array<int, string|null>
         */
        private array $price01 = [];
        /**
         * @var array<int, string|null>
         */
        private array $price02 = [];
        /**
         * @var array<int, string|null>
         */
        private array $price01IncTaxs = [];
        /**
         * @var array<int, string|null>
         */
        private array $price02IncTaxs = [];
        /**
         * @var array<int, string|null>
         */
        private array $codes = [];
        /**
         * @var array<string|int, string|null>
         */
        private array $classCategories1 = [];
        /**
         * @var array<string|int, string|null>
         */
        private array $classCategories2 = [];
        private ?string $className1 = null;
        private ?string $className2 = null;

        #[\Override]
        public function __toString(): string
        {
            return $this->getName();
        }

        public function _calc(): void
        {
            if (!$this->_calc) {
                $i = 0;
                foreach ($this->getProductClasses() as $ProductClass) {
                    /** @var ProductClass $ProductClass */
                    // stock_find
                    if ($ProductClass->isVisible() == false) {
                        continue;
                    }
                    $ClassCategory1 = $ProductClass->getClassCategory1();
                    $ClassCategory2 = $ProductClass->getClassCategory2();
                    if ($ClassCategory1 && !$ClassCategory1->isVisible()) {
                        continue;
                    }
                    if ($ClassCategory2 && !$ClassCategory2->isVisible()) {
                        continue;
                    }

                    // stock_find
                    $this->stockFinds[] = $ProductClass->getStockFind();

                    // stock
                    $this->stocks[] = $ProductClass->getStock();

                    // stock_unlimited
                    $this->stockUnlimiteds[] = $ProductClass->isStockUnlimited();

                    // price01
                    if (!is_null($ProductClass->getPrice01())) {
                        $this->price01[] = $ProductClass->getPrice01();
                        // price01IncTax
                        $this->price01IncTaxs[] = $ProductClass->getPrice01IncTax();
                    }

                    // price02
                    $this->price02[] = $ProductClass->getPrice02();

                    // price02IncTax
                    $this->price02IncTaxs[] = $ProductClass->getPrice02IncTax();

                    // product_code
                    $this->codes[] = $ProductClass->getCode();

                    if ($i === 0) {
                        if ($ProductClass->getClassCategory1() && $ProductClass->getClassCategory1()->getId()) {
                            $this->className1 = $ProductClass->getClassCategory1()->getClassName()->getName();
                        }
                        if ($ProductClass->getClassCategory2() && $ProductClass->getClassCategory2()->getId()) {
                            $this->className2 = $ProductClass->getClassCategory2()->getClassName()->getName();
                        }
                    }
                    if ($ProductClass->getClassCategory1()) {
                        $classCategoryId1 = $ProductClass->getClassCategory1()->getId();
                        if (!empty($classCategoryId1)) {
                            if ($ProductClass->getClassCategory2()) {
                                $this->classCategories1[$ProductClass->getClassCategory1()->getId()] = $ProductClass->getClassCategory1()->getName();
                                $this->classCategories2[$ProductClass->getClassCategory1()->getId()][$ProductClass->getClassCategory2()->getId()] = $ProductClass->getClassCategory2()->getName();
                            } else {
                                $this->classCategories1[$ProductClass->getClassCategory1()->getId()] = $ProductClass->getClassCategory1()->getName().($ProductClass->getStockFind() ? '' : trans('front.product.out_of_stock_label'));
                            }
                        }
                    }
                    $i++;
                }
                $this->_calc = true;
            }
        }

        /**
         * Is Enable
         *
         * @deprecated
         */
        public function isEnable(): bool
        {
            return $this->getStatus()->getId() === ProductStatus::DISPLAY_SHOW ? true : false;
        }

        /**
         * Get ClassName1
         */
        public function getClassName1(): ?string
        {
            $this->_calc();

            return $this->className1;
        }

        /**
         * Get ClassName2
         */
        public function getClassName2(): ?string
        {
            $this->_calc();

            return $this->className2;
        }

        /**
         * Get getClassCategories1
         *
         * @return array<int, string|null>
         */
        public function getClassCategories1(): array
        {
            $this->_calc();

            return $this->classCategories1;
        }

        /**
         * @return array<string, int>
         */
        public function getClassCategories1AsFlip(): array
        {
            return array_flip($this->getClassCategories1());
        }

        /**
         * Get getClassCategories2
         *
         * @return array<int, string|null>
         */
        public function getClassCategories2(string $class_category1): array
        {
            $this->_calc();

            return $this->classCategories2[$class_category1] ?? [];
        }

        /**
         * @return array<string, int>
         */
        public function getClassCategories2AsFlip(string $class_category1): array
        {
            return array_flip($this->getClassCategories2($class_category1));
        }

        /**
         * Get StockFind
         */
        public function getStockFind(): ?bool
        {
            $this->_calc();

            return count($this->stockFinds)
                ? max($this->stockFinds)
                : null;
        }

        /**
         * Get Stock min
         */
        public function getStockMin(): ?string
        {
            $this->_calc();

            return count($this->stocks)
                ? min($this->stocks)
                : null;
        }

        /**
         * Get Stock max
         */
        public function getStockMax(): ?string
        {
            $this->_calc();

            return count($this->stocks)
                ? max($this->stocks)
                : null;
        }

        /**
         * Get StockUnlimited min
         */
        public function getStockUnlimitedMin(): ?bool
        {
            $this->_calc();

            return count($this->stockUnlimiteds)
                ? min($this->stockUnlimiteds)
                : null;
        }

        /**
         * Get StockUnlimited max
         */
        public function getStockUnlimitedMax(): ?bool
        {
            $this->_calc();

            return count($this->stockUnlimiteds)
                ? max($this->stockUnlimiteds)
                : null;
        }

        /**
         * Get Price01 min
         */
        public function getPrice01Min(): ?string
        {
            $this->_calc();

            if (count($this->price01) == 0) {
                return null;
            }

            return min($this->price01);
        }

        /**
         * Get Price01 max
         */
        public function getPrice01Max(): ?string
        {
            $this->_calc();

            if (count($this->price01) == 0) {
                return null;
            }

            return max($this->price01);
        }

        /**
         * Get Price02 min
         */
        public function getPrice02Min(): ?string
        {
            $this->_calc();

            return count($this->price02)
                ? min($this->price02)
                : null;
        }

        /**
         * Get Price02 max
         */
        public function getPrice02Max(): ?string
        {
            $this->_calc();

            return count($this->price02)
                ? max($this->price02)
                : null;
        }

        /**
         * Get Price01IncTax min
         */
        public function getPrice01IncTaxMin(): ?string
        {
            $this->_calc();

            return count($this->price01IncTaxs)
                ? min($this->price01IncTaxs)
                : null;
        }

        /**
         * Get Price01IncTax max
         */
        public function getPrice01IncTaxMax(): ?string
        {
            $this->_calc();

            return count($this->price01IncTaxs)
                ? max($this->price01IncTaxs)
                : null;
        }

        /**
         * Get Price02IncTax min
         */
        public function getPrice02IncTaxMin(): ?string
        {
            $this->_calc();

            return count($this->price02IncTaxs)
                ? min($this->price02IncTaxs)
                : null;
        }

        /**
         * Get Price02IncTax max
         */
        public function getPrice02IncTaxMax(): ?string
        {
            $this->_calc();

            return count($this->price02IncTaxs)
                ? max($this->price02IncTaxs)
                : null;
        }

        /**
         * Get Product_code min
         */
        public function getCodeMin(): ?string
        {
            $this->_calc();

            $codes = [];
            foreach ($this->codes as $code) {
                if (!is_null($code)) {
                    $codes[] = $code;
                }
            }

            return count($codes) ? min($codes) : null;
        }

        /**
         * Get Product_code max
         */
        public function getCodeMax(): ?string
        {
            $this->_calc();

            $codes = [];
            foreach ($this->codes as $code) {
                if (!is_null($code)) {
                    $codes[] = $code;
                }
            }

            return count($codes) ? max($codes) : null;
        }

        public function getMainListImage(): ?ProductImage
        {
            $ProductImages = $this->getProductImage();

            return $ProductImages->isEmpty() ? null : $ProductImages[0];
        }

        public function getMainFileName(): ?ProductImage
        {
            if (count($this->ProductImage) > 0) {
                return $this->ProductImage[0];
            } else {
                return null;
            }
        }

        public function hasProductClass(): bool
        {
            foreach ($this->ProductClasses as $ProductClass) {
                if (!$ProductClass->isVisible()) {
                    continue;
                }
                if (!is_null($ProductClass->getClassCategory1())) {
                    return true;
                }
            }

            return false;
        }

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name', type: 'string', length: 255)]
        private $name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'note', type: 'text', nullable: true)]
        private $note;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'description_list', type: 'text', nullable: true)]
        private $description_list;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'description_detail', type: 'text', nullable: true)]
        private $description_detail;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'search_word', type: 'text', nullable: true)]
        private $search_word;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'free_area', type: 'text', nullable: true)]
        private $free_area;

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
        #[ORM\OneToMany(targetEntity: ProductCategory::class, mappedBy: 'Product', cascade: ['persist', 'remove'])]
        private $ProductCategories;

        /**
         * @var Collection<int, ProductClass>
         */
        #[ORM\OneToMany(targetEntity: ProductClass::class, mappedBy: 'Product', cascade: ['persist', 'remove'])]
        private $ProductClasses;

        /**
         * @var Collection<int, ProductImage>
         */
        #[ORM\OneToMany(targetEntity: ProductImage::class, mappedBy: 'Product', cascade: ['remove'])]
        #[ORM\OrderBy(['sort_no' => 'ASC'])]
        private $ProductImage;

        /**
         * @var Collection<int, ProductTag>
         */
        #[ORM\OneToMany(targetEntity: ProductTag::class, mappedBy: 'Product', cascade: ['remove'])]
        private $ProductTag;

        /**
         * @var Collection<int, CustomerFavoriteProduct>
         */
        #[ORM\OneToMany(targetEntity: CustomerFavoriteProduct::class, mappedBy: 'Product')]
        private $CustomerFavoriteProducts;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        /**
         * @var ProductStatus|null
         */
        #[ORM\ManyToOne(targetEntity: ProductStatus::class)]
        #[ORM\JoinColumn(name: 'product_status_id', referencedColumnName: 'id')]
        private $Status;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->ProductCategories = new ArrayCollection();
            $this->ProductClasses = new ArrayCollection();
            $this->ProductImage = new ArrayCollection();
            $this->ProductTag = new ArrayCollection();
            $this->CustomerFavoriteProducts = new ArrayCollection();
        }

        public function __clone()
        {
            $this->id = null;
        }

        /**
         * Productエンティティのコピーを作成する.
         *
         * コピー元のProductを受け取り,
         * 関連エンティティも再帰的にコピーする.
         */
        public function copy(Product $Product): Product
        {
            // コピー対象外
            $this->CustomerFavoriteProducts = new ArrayCollection();

            $Categories = $Product->getProductCategories();
            $this->ProductCategories = new ArrayCollection();
            foreach ($Categories as $Category) {
                $CopyCategory = clone $Category;
                $CopyCategory->setProductId($this->getId());
                $this->addProductCategory($CopyCategory);
                $CopyCategory->setProduct($this);
            }

            $Classes = $Product->getProductClasses();
            $this->ProductClasses = new ArrayCollection();
            foreach ($Classes as $Class) {
                $CopyClass = new ProductClass();
                $CopyClass->copyProperties($Class, ['id', 'product_id']);
                $this->addProductClass($CopyClass);
                $CopyClass->setProduct($this);
            }

            $Images = $Product->getProductImage();
            $this->ProductImage = new ArrayCollection();
            foreach ($Images as $Image) {
                $CloneImage = clone $Image;
                $this->addProductImage($CloneImage);
                $CloneImage->setProduct($this);
            }

            $Tags = $Product->getProductTag();
            $this->ProductTag = new ArrayCollection();
            foreach ($Tags as $Tag) {
                $CloneTag = clone $Tag;
                $this->addProductTag($CloneTag);
                $CloneTag->setProduct($this);
            }

            return $this;
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
        public function setName(string $name): Product
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
         * Set note.
         */
        public function setNote(?string $note = null): Product
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         */
        public function getNote(): ?string
        {
            return $this->note;
        }

        /**
         * Set descriptionList.
         */
        public function setDescriptionList(?string $descriptionList = null): Product
        {
            $this->description_list = $descriptionList;

            return $this;
        }

        /**
         * Get descriptionList.
         */
        public function getDescriptionList(): ?string
        {
            return $this->description_list;
        }

        /**
         * Set descriptionDetail.
         */
        public function setDescriptionDetail(?string $descriptionDetail = null): Product
        {
            $this->description_detail = $descriptionDetail;

            return $this;
        }

        /**
         * Get descriptionDetail.
         */
        public function getDescriptionDetail(): ?string
        {
            return $this->description_detail;
        }

        /**
         * Set searchWord.
         */
        public function setSearchWord(?string $searchWord = null): Product
        {
            $this->search_word = $searchWord;

            return $this;
        }

        /**
         * Get searchWord.
         */
        public function getSearchWord(): ?string
        {
            return $this->search_word;
        }

        /**
         * Set freeArea.
         */
        public function setFreeArea(?string $freeArea = null): Product
        {
            $this->free_area = $freeArea;

            return $this;
        }

        /**
         * Get freeArea.
         */
        public function getFreeArea(): ?string
        {
            return $this->free_area;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Product
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
        public function setUpdateDate(\DateTime $updateDate): Product
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
        public function addProductCategory(ProductCategory $productCategory): Product
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
         * Add productClass.
         */
        public function addProductClass(ProductClass $productClass): Product
        {
            $this->ProductClasses[] = $productClass;

            return $this;
        }

        /**
         * Remove productClass.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductClass(ProductClass $productClass): bool
        {
            return $this->ProductClasses->removeElement($productClass);
        }

        /**
         * Get productClasses.
         *
         * @return Collection<int, ProductClass>|null
         */
        public function getProductClasses(): ?Collection
        {
            return $this->ProductClasses;
        }

        /**
         * Add productImage.
         */
        public function addProductImage(ProductImage $productImage): Product
        {
            $this->ProductImage[] = $productImage;

            return $this;
        }

        /**
         * Remove productImage.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductImage(ProductImage $productImage): bool
        {
            return $this->ProductImage->removeElement($productImage);
        }

        /**
         * Get productImage.
         *
         * @return Collection<int, ProductImage>
         */
        public function getProductImage(): Collection
        {
            return $this->ProductImage;
        }

        /**
         * Add productTag.
         */
        public function addProductTag(ProductTag $productTag): Product
        {
            $this->ProductTag[] = $productTag;

            return $this;
        }

        /**
         * Remove productTag.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductTag(ProductTag $productTag): bool
        {
            return $this->ProductTag->removeElement($productTag);
        }

        /**
         * Get productTag.
         *
         * @return Collection<int, ProductTag>
         */
        public function getProductTag(): Collection
        {
            return $this->ProductTag;
        }

        /**
         * Get Tag
         * フロント側タグsort_no順の配列を作成する
         *
         * @return Tag[]
         */
        public function getTags(): array
        {
            $tags = [];

            foreach ($this->getProductTag() as $productTag) {
                $tags[] = $productTag->getTag();
            }

            usort($tags, function (Tag $tag1, Tag $tag2) {
                return $tag1->getSortNo() <=> $tag2->getSortNo();
            });

            return $tags;
        }

        /**
         * Add customerFavoriteProduct.
         */
        public function addCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct): Product
        {
            $this->CustomerFavoriteProducts[] = $customerFavoriteProduct;

            return $this;
        }

        /**
         * Remove customerFavoriteProduct.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeCustomerFavoriteProduct(CustomerFavoriteProduct $customerFavoriteProduct): bool
        {
            return $this->CustomerFavoriteProducts->removeElement($customerFavoriteProduct);
        }

        /**
         * Get customerFavoriteProducts.
         *
         * @return Collection<int, CustomerFavoriteProduct>
         */
        public function getCustomerFavoriteProducts(): Collection
        {
            return $this->CustomerFavoriteProducts;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): Product
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

        /**
         * Set status.
         */
        public function setStatus(?ProductStatus $status = null): Product
        {
            $this->Status = $status;

            return $this;
        }

        /**
         * Get status.
         */
        public function getStatus(): ?ProductStatus
        {
            return $this->Status;
        }
    }
}

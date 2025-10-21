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
use Eccube\Repository\TagRepository;

if (!class_exists(Tag::class)) {
    /**
     * Tag
     */
    #[ORM\Table(name: 'dtb_tag')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: TagRepository::class)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    class Tag extends AbstractEntity implements \Stringable
    {
        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return $this->getName() ?? '';
        }

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        protected $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name', type: 'string', length: 255)]
        protected $name;

        /**
         * @var int
         */
        #[ORM\Column(name: 'sort_no', type: 'smallint', options: ['unsigned' => true])]
        protected $sort_no;

        /**
         * @var Collection<int, ProductTag>
         */
        #[ORM\OneToMany(targetEntity: ProductTag::class, mappedBy: 'Tag')]
        protected $ProductTag;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->ProductTag = new ArrayCollection();
        }

        /**
         * Set id.
         *
         * @param int $id
         *
         * @return $this
         */
        public function setId($id): static
        {
            $this->id = $id;

            return $this;
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
         * @return $this
         */
        public function setName($name): static
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string|null
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * Set sort_no.
         *
         * @param int $sort_no
         *
         * @return $this
         */
        public function setSortNo($sort_no): static
        {
            $this->sort_no = $sort_no;

            return $this;
        }

        /**
         * Get sort_no.
         *
         * @return int
         */
        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        /**
         * Add productTag.
         *
         * @param ProductTag $productTag
         *
         * @return Tag
         */
        public function addProductTag(ProductTag $productTag): Tag
        {
            $this->ProductTag[] = $productTag;

            return $this;
        }

        /**
         * Remove productTag.
         *
         * @param ProductTag $productTag
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
    }
}

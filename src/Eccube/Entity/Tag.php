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
use Doctrine\DBAL\Types\Types;
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
        #[\Override]
        public function __toString(): string
        {
            return $this->getName() ?? '';
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        protected ?int $id = null;

        #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
        protected ?string $name = null;

        #[ORM\Column(name: 'sort_no', type: Types::SMALLINT, options: ['unsigned' => true])]
        protected ?int $sort_no = null;

        /**
         * @var Collection<int, ProductTag>
         */
        #[ORM\OneToMany(targetEntity: ProductTag::class, mappedBy: 'Tag')]
        protected Collection $ProductTag;

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
         * @return $this
         */
        public function setId(int $id): static
        {
            $this->id = $id;

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
         *
         * @return $this
         */
        public function setName(?string $name): static
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * Set sort_no.
         *
         * @return $this
         */
        public function setSortNo(int $sort_no): static
        {
            $this->sort_no = $sort_no;

            return $this;
        }

        /**
         * Get sort_no.
         */
        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        /**
         * Add productTag.
         */
        public function addProductTag(ProductTag $productTag): Tag
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
    }
}

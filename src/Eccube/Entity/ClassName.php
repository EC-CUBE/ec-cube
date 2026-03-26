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
use Eccube\Repository\ClassNameRepository;

if (!class_exists(ClassName::class)) {
    /**
     * ClassName
     */
    #[ORM\Table(name: 'dtb_class_name')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: ClassNameRepository::class)]
    class ClassName extends AbstractEntity implements \Stringable
    {
        #[\Override]
        public function __toString(): string
        {
            return $this->getName();
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'backend_name', type: Types::STRING, length: 255, nullable: true)]
        private ?string $backend_name = null;

        #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
        private ?string $name = null;

        #[ORM\Column(name: 'sort_no', type: Types::INTEGER, options: ['unsigned' => true])]
        private ?int $sort_no = null;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
        private $update_date;

        /**
         * @var Collection<int, ClassCategory>
         */
        #[ORM\OneToMany(targetEntity: ClassCategory::class, mappedBy: 'ClassName')]
        #[ORM\OrderBy(['sort_no' => 'DESC'])]
        private $ClassCategories;

        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private ?Member $Creator = null;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->ClassCategories = new ArrayCollection();
        }

        /**
         * Get id.
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set backend_name.
         */
        public function setBackendName(?string $backendName): ClassName
        {
            $this->backend_name = $backendName;

            return $this;
        }

        /**
         * Get backend_name.
         */
        public function getBackendName(): ?string
        {
            return $this->backend_name;
        }

        /**
         * Set name.
         */
        public function setName(?string $name): ClassName
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
         * Set sortNo.
         */
        public function setSortNo(int $sortNo): ClassName
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
        public function setCreateDate(\DateTime $createDate): ClassName
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
        public function setUpdateDate(\DateTime $updateDate): ClassName
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
         * Add classCategory.
         */
        public function addClassCategory(ClassCategory $classCategory): ClassName
        {
            $this->ClassCategories[] = $classCategory;

            return $this;
        }

        /**
         * Remove classCategory.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeClassCategory(ClassCategory $classCategory): bool
        {
            return $this->ClassCategories->removeElement($classCategory);
        }

        /**
         * Get classCategories.
         *
         * @return Collection<int, ClassCategory>
         */
        public function getClassCategories(): Collection
        {
            return $this->ClassCategories;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): ClassName
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
}

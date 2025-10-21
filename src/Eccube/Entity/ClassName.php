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
        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return $this->getName();
        }

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'backend_name', type: 'string', length: 255, nullable: true)]
        private $backend_name;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name', type: 'string', length: 255)]
        private $name;

        /**
         * @var int
         */
        #[ORM\Column(name: 'sort_no', type: 'integer', options: ['unsigned' => true])]
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
         * @var Collection<int, ClassCategory>
         */
        #[ORM\OneToMany(targetEntity: ClassCategory::class, mappedBy: 'ClassName')]
        #[ORM\OrderBy(['sort_no' => 'DESC'])]
        private $ClassCategories;

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
            $this->ClassCategories = new ArrayCollection();
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
         * Set backend_name.
         *
         * @param string $backendName
         *
         * @return ClassName
         */
        public function setBackendName($backendName): ClassName
        {
            $this->backend_name = $backendName;

            return $this;
        }

        /**
         * Get backend_name.
         *
         * @return string|null
         */
        public function getBackendName(): ?string
        {
            return $this->backend_name;
        }

        /**
         * Set name.
         *
         * @param string $name
         *
         * @return ClassName
         */
        public function setName($name): ClassName
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
         * Set sortNo.
         *
         * @param int $sortNo
         *
         * @return ClassName
         */
        public function setSortNo($sortNo): ClassName
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
         * @return ClassName
         */
        public function setCreateDate($createDate): ClassName
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
         * @return ClassName
         */
        public function setUpdateDate($updateDate): ClassName
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
         * Add classCategory.
         *
         * @param ClassCategory $classCategory
         *
         * @return ClassName
         */
        public function addClassCategory(ClassCategory $classCategory): ClassName
        {
            $this->ClassCategories[] = $classCategory;

            return $this;
        }

        /**
         * Remove classCategory.
         *
         * @param ClassCategory $classCategory
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
         *
         * @param Member|null $creator
         *
         * @return ClassName
         */
        public function setCreator(?Member $creator = null): ClassName
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

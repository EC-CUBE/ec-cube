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
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\BlockRepository;

if (!class_exists(Block::class)) {
    /**
     * Block
     */
    #[ORM\Table(name: 'dtb_block')]
    #[ORM\UniqueConstraint(name: 'device_type_id', columns: ['device_type_id', 'file_name'])]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: BlockRepository::class)]
    class Block extends AbstractEntity
    {
        /**
         * @var int
         */
        public const UNUSED_BLOCK_ID = 0;

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'block_name', type: 'string', length: 255, nullable: true)]
        private $name;

        /**
         * @var string
         */
        #[ORM\Column(name: 'file_name', type: 'string', length: 255)]
        private $file_name;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'use_controller', type: 'boolean', options: ['default' => false])]
        private $use_controller = false;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'deletable', type: 'boolean', options: ['default' => true])]
        private $deletable = true;

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
         * @var Collection<int, BlockPosition>
         */
        #[ORM\OneToMany(targetEntity: BlockPosition::class, mappedBy: 'Block', cascade: ['persist', 'remove'])]
        private $BlockPositions;

        /**
         * @var DeviceType|null
         */
        #[ORM\ManyToOne(targetEntity: DeviceType::class)]
        #[ORM\JoinColumn(name: 'device_type_id', referencedColumnName: 'id')]
        private $DeviceType;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->BlockPositions = new ArrayCollection();
        }

        /**
         * Set id
         *
         * @param int $id
         *
         * @return Block
         */
        public function setId($id): Block
        {
            $this->id = $id;

            return $this;
        }

        /**
         * Get id
         *
         * @return int
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set name
         *
         * @param string $name
         *
         * @return Block
         */
        public function setName($name): Block
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name
         *
         * @return string
         */
        public function getName(): string
        {
            return $this->name;
        }

        /**
         * Set fileName
         *
         * @param string $fileName
         *
         * @return Block
         */
        public function setFileName($fileName): Block
        {
            $this->file_name = $fileName;

            return $this;
        }

        /**
         * Get fileName
         *
         * @return string
         */
        public function getFileName(): string
        {
            return $this->file_name;
        }

        /**
         * Set useController
         *
         * @param bool $useController
         *
         * @return Block
         */
        public function setUseController($useController): Block
        {
            $this->use_controller = $useController;

            return $this;
        }

        /**
         * Get useController
         *
         * @return bool
         */
        public function isUseController(): bool
        {
            return $this->use_controller;
        }

        /**
         * Set deletable
         *
         * @param bool $deletable
         *
         * @return Block
         */
        public function setDeletable($deletable): Block
        {
            $this->deletable = $deletable;

            return $this;
        }

        /**
         * Get deletable
         *
         * @return bool
         */
        public function isDeletable(): bool
        {
            return $this->deletable;
        }

        /**
         * Set createDate
         *
         * @param \DateTime $createDate
         *
         * @return Block
         */
        public function setCreateDate($createDate): Block
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate
         *
         * @return \DateTime
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate
         *
         * @param \DateTime $updateDate
         *
         * @return Block
         */
        public function setUpdateDate($updateDate): Block
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate
         *
         * @return \DateTime
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * Add blockPosition
         *
         * @param BlockPosition $blockPosition
         *
         * @return Block
         */
        public function addBlockPosition(BlockPosition $blockPosition): Block
        {
            $this->BlockPositions[] = $blockPosition;

            return $this;
        }

        /**
         * Remove blockPosition
         *
         * @param BlockPosition $blockPosition
         *
         * @return void
         */
        public function removeBlockPosition(BlockPosition $blockPosition): void
        {
            $this->BlockPositions->removeElement($blockPosition);
        }

        /**
         * Get blockPositions
         *
         * @return Collection<int, BlockPosition>
         */
        public function getBlockPositions(): Collection
        {
            return $this->BlockPositions;
        }

        /**
         * Set deviceType
         *
         * @param DeviceType $deviceType
         *
         * @return Block
         */
        public function setDeviceType(?DeviceType $deviceType = null): Block
        {
            $this->DeviceType = $deviceType;

            return $this;
        }

        /**
         * Get deviceType
         *
         * @return DeviceType|null
         */
        public function getDeviceType(): ?DeviceType
        {
            return $this->DeviceType;
        }
    }
}

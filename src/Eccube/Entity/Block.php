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
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\BlockRepository;

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

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'block_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'file_name', type: Types::STRING, length: 255)]
    private ?string $file_name = null;

    #[ORM\Column(name: 'use_controller', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $use_controller = false;

    #[ORM\Column(name: 'deletable', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $deletable = true;

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
     * @var Collection<int, BlockPosition>
     */
    #[ORM\OneToMany(targetEntity: BlockPosition::class, mappedBy: 'Block', cascade: ['persist', 'remove'])]
    private $BlockPositions;

    #[ORM\ManyToOne(targetEntity: DeviceType::class)]
    #[ORM\JoinColumn(name: 'device_type_id', referencedColumnName: 'id')]
    private ?DeviceType $DeviceType = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->BlockPositions = new ArrayCollection();
    }

    /**
     * Set id
     */
    public function setId(int $id): Block
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
     */
    public function setName(string $name): Block
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set fileName
     */
    public function setFileName(string $fileName): Block
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get fileName
     */
    public function getFileName(): string
    {
        return $this->file_name;
    }

    /**
     * Set useController
     */
    public function setUseController(bool $useController): Block
    {
        $this->use_controller = $useController;

        return $this;
    }

    /**
     * Get useController
     */
    public function isUseController(): bool
    {
        return $this->use_controller;
    }

    /**
     * Set deletable
     */
    public function setDeletable(bool $deletable): Block
    {
        $this->deletable = $deletable;

        return $this;
    }

    /**
     * Get deletable
     */
    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    /**
     * Set createDate
     */
    public function setCreateDate(\DateTime $createDate): Block
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
     */
    public function setUpdateDate(\DateTime $updateDate): Block
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
     */
    public function addBlockPosition(BlockPosition $blockPosition): Block
    {
        $this->BlockPositions[] = $blockPosition;

        return $this;
    }

    /**
     * Remove blockPosition
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
     */
    public function setDeviceType(?DeviceType $deviceType = null): Block
    {
        $this->DeviceType = $deviceType;

        return $this;
    }

    /**
     * Get deviceType
     */
    public function getDeviceType(): ?DeviceType
    {
        return $this->DeviceType;
    }
}

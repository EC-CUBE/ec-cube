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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\BlockPositionRepository;

/**
 * BlockPosition
 */
#[ORM\Table(name: 'dtb_block_position')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: BlockPositionRepository::class)]
class BlockPosition extends AbstractEntity
{
    #[ORM\Column(name: 'section', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $section = null;

    #[ORM\Column(name: 'block_id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $block_id = null;

    #[ORM\Column(name: 'layout_id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?int $layout_id = null;

    #[ORM\Column(name: 'block_row', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $block_row = null;

    #[ORM\ManyToOne(targetEntity: Block::class, inversedBy: 'BlockPositions')]
    #[ORM\JoinColumn(name: 'block_id', referencedColumnName: 'id')]
    private ?Block $Block = null;

    #[ORM\ManyToOne(targetEntity: Layout::class, inversedBy: 'BlockPositions')]
    #[ORM\JoinColumn(name: 'layout_id', referencedColumnName: 'id')]
    private ?Layout $Layout = null;

    /**
     * Set section.
     */
    public function setSection(int $section): BlockPosition
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section.
     */
    public function getSection(): int
    {
        return $this->section;
    }

    /**
     * Set blockId.
     */
    public function setBlockId(int $blockId): BlockPosition
    {
        $this->block_id = $blockId;

        return $this;
    }

    /**
     * Get blockId.
     */
    public function getBlockId(): int
    {
        return $this->block_id;
    }

    /**
     * Set layoutId.
     */
    public function setLayoutId(int $layoutId): BlockPosition
    {
        $this->layout_id = $layoutId;

        return $this;
    }

    /**
     * Get layoutId.
     */
    public function getLayoutId(): int
    {
        return $this->layout_id;
    }

    /**
     * Set blockRow.
     */
    public function setBlockRow(?int $blockRow = null): BlockPosition
    {
        $this->block_row = $blockRow;

        return $this;
    }

    /**
     * Get blockRow.
     */
    public function getBlockRow(): ?int
    {
        return $this->block_row;
    }

    /**
     * Set block.
     */
    public function setBlock(?Block $block = null): BlockPosition
    {
        $this->Block = $block;

        return $this;
    }

    /**
     * Get block.
     */
    public function getBlock(): ?Block
    {
        return $this->Block;
    }

    /**
     * Set layout.
     */
    public function setLayout(?Layout $Layout = null): BlockPosition
    {
        $this->Layout = $Layout;

        return $this;
    }

    /**
     * Get Layout.
     */
    public function getLayout(): ?Layout
    {
        return $this->Layout;
    }
}

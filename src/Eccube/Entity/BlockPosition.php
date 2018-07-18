<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlockPosition
 *
 * @ORM\Table(name="dtb_block_position")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\BlockPositionRepository")
 */
class BlockPosition extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="page_id", type="integer", options={"unsigned":true}, nullable=true)
     *
     * @deprecated
     */
    private $page_id;

    /**
     * @var int
     *
     * @ORM\Column(name="section", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $section;

    /**
     * @var int
     *
     * @ORM\Column(name="block_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $block_id;

    /**
     * @var int
     *
     * @ORM\Column(name="layout_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $layout_id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="block_row", type="integer", nullable=true, options={"unsigned":true})
     */
    private $block_row;

    /**
     * @var int
     *
     * @ORM\Column(name="anywhere", type="smallint", options={"default":0})
     *
     * @deprecated
     */
    private $anywhere = 0;

    /**
     * @var \Eccube\Entity\Block
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Block", inversedBy="BlockPositions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     * })
     */
    private $Block;

    /**
     * @var \Eccube\Entity\PageLayout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Page", inversedBy="BlockPositions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     * })
     *
     * @deprecated
     */
    private $Page;

    /**
     * @var \Eccube\Entity\Layout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Layout", inversedBy="BlockPositions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     * })
     */
    private $Layout;

    /**
     * Set pageId.
     *
     * @param int $pageId
     *
     * @return BlockPosition
     *
     * @deprecated
     */
    public function setPageId($pageId)
    {
        $this->page_id = $pageId;

        return $this;
    }

    /**
     * Get pageId.
     *
     * @return int
     *
     * @deprecated
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Set section.
     *
     * @param int $section
     *
     * @return BlockPosition
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section.
     *
     * @return int
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set blockId.
     *
     * @param int $blockId
     *
     * @return BlockPosition
     */
    public function setBlockId($blockId)
    {
        $this->block_id = $blockId;

        return $this;
    }

    /**
     * Get blockId.
     *
     * @return int
     */
    public function getBlockId()
    {
        return $this->block_id;
    }

    /**
     * Set layoutId.
     *
     * @param int $layoutId
     *
     * @return BlockPosition
     */
    public function setLayoutId($layoutId)
    {
        $this->layout_id = $layoutId;

        return $this;
    }

    /**
     * Get layoutId.
     *
     * @return int
     */
    public function getLayoutId()
    {
        return $this->layout_id;
    }

    /**
     * Set blockRow.
     *
     * @param int|null $blockRow
     *
     * @return BlockPosition
     */
    public function setBlockRow($blockRow = null)
    {
        $this->block_row = $blockRow;

        return $this;
    }

    /**
     * Get blockRow.
     *
     * @return int|null
     */
    public function getBlockRow()
    {
        return $this->block_row;
    }

    /**
     * Set anywhere.
     *
     * @param int $anywhere
     *
     * @return BlockPosition
     *
     * @deprecated
     */
    public function setAnywhere($anywhere)
    {
        $this->anywhere = $anywhere;

        return $this;
    }

    /**
     * Get anywhere.
     *
     * @return int
     *
     * @deprecated
     */
    public function getAnywhere()
    {
        return $this->anywhere;
    }

    /**
     * Set block.
     *
     * @param \Eccube\Entity\Block|null $block
     *
     * @return BlockPosition
     */
    public function setBlock(\Eccube\Entity\Block $block = null)
    {
        $this->Block = $block;

        return $this;
    }

    /**
     * Get block.
     *
     * @return \Eccube\Entity\Block|null
     */
    public function getBlock()
    {
        return $this->Block;
    }

    /**
     * Set layout.
     *
     * @param \Eccube\Entity\Layout|null $layout
     *
     * @return BlockPosition
     */
    public function setLayout(\Eccube\Entity\Layout $Layout = null)
    {
        $this->Layout = $Layout;

        return $this;
    }

    /**
     * Get Layout.
     *
     * @return \Eccube\Entity\Layout|null
     */
    public function getLayout()
    {
        return $this->Layout;
    }

    /**
     * Set pageLayout.
     *
     * @param \Eccube\Entity\Page|null $page
     *
     * @return BlockPosition
     *
     * @deprecated
     */
    public function setPage(\Eccube\Entity\Page $Page = null)
    {
        $this->Page = $Page;

        return $this;
    }

    /**
     * Get pageLayout.
     *
     * @return \Eccube\Entity\Page|null
     *
     * @deprecated
     */
    public function getPage()
    {
        return $this->Page;
    }
}

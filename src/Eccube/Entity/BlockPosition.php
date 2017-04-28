<?php

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
     * @ORM\Column(name="target_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $target_id;

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
     *   @ORM\JoinColumn(name="block_id", referencedColumnName="block_id")
     * })
     */
    private $Block;

    /**
     * @var \Eccube\Entity\PageLayout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\PageLayout", inversedBy="BlockPositions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="page_id", referencedColumnName="page_id")
     * })
     *
     * @deprecated
     */
    private $PageLayout;

    /**
     * @var \Eccube\Entity\Layout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Layout", inversedBy="BlockPositions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="layout_id", referencedColumnName="layout_id")
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
     * Set targetId.
     *
     * @param int $targetId
     *
     * @return BlockPosition
     */
    public function setTargetId($targetId)
    {
        $this->target_id = $targetId;

        return $this;
    }

    /**
     * Get targetId.
     *
     * @return int
     */
    public function getTargetId()
    {
        return $this->target_id;
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
     * @param \Eccube\Entity\PageLayout|null $pageLayout
     *
     * @return BlockPosition
     *
     * @deprecated
     */
    public function setPageLayout(\Eccube\Entity\PageLayout $pageLayout = null)
    {
        $this->PageLayout = $pageLayout;

        return $this;
    }

    /**
     * Get pageLayout.
     *
     * @return \Eccube\Entity\PageLayout|null
     *
     * @deprecated
     */
    public function getPageLayout()
    {
        return $this->PageLayout;
    }
}

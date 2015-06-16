<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlockPosition
 */
class BlockPosition extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $page_id;

    /**
     * @var integer
     */
    private $target_id;

    /**
     * @var integer
     */
    private $bloc_id;

    /**
     * @var integer
     */
    private $bloc_row;

    /**
     * @var integer
     */
    private $anywhere;

    /**
     * @var \Eccube\Entity\Block
     */
    private $Block;

    /**
     * @var \Eccube\Entity\PageLayout
     */
    private $PageLayout;


    /**
     * Set page_id
     *
     * @param integer $pageId
     * @return BlockPosition
     */
    public function setPageId($pageId)
    {
        $this->page_id = $pageId;

        return $this;
    }

    /**
     * Get page_id
     *
     * @return integer 
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Set target_id
     *
     * @param integer $targetId
     * @return BlockPosition
     */
    public function setTargetId($targetId)
    {
        $this->target_id = $targetId;

        return $this;
    }

    /**
     * Get target_id
     *
     * @return integer 
     */
    public function getTargetId()
    {
        return $this->target_id;
    }

    /**
     * Set bloc_id
     *
     * @param integer $blocId
     * @return BlockPosition
     */
    public function setBlocId($blocId)
    {
        $this->bloc_id = $blocId;

        return $this;
    }

    /**
     * Get bloc_id
     *
     * @return integer 
     */
    public function getBlocId()
    {
        return $this->bloc_id;
    }

    /**
     * Set bloc_row
     *
     * @param integer $blocRow
     * @return BlockPosition
     */
    public function setBlocRow($blocRow)
    {
        $this->bloc_row = $blocRow;

        return $this;
    }

    /**
     * Get bloc_row
     *
     * @return integer 
     */
    public function getBlocRow()
    {
        return $this->bloc_row;
    }

    /**
     * Set anywhere
     *
     * @param integer $anywhere
     * @return BlockPosition
     */
    public function setAnywhere($anywhere)
    {
        $this->anywhere = $anywhere;

        return $this;
    }

    /**
     * Get anywhere
     *
     * @return integer 
     */
    public function getAnywhere()
    {
        return $this->anywhere;
    }

    /**
     * Set Block
     *
     * @param \Eccube\Entity\Block $block
     * @return BlockPosition
     */
    public function setBlock(\Eccube\Entity\Block $block)
    {
        $this->Block = $block;

        return $this;
    }

    /**
     * Get Block
     *
     * @return \Eccube\Entity\Block 
     */
    public function getBlock()
    {
        return $this->Block;
    }

    /**
     * Set PageLayout
     *
     * @param \Eccube\Entity\PageLayout $pageLayout
     * @return BlockPosition
     */
    public function setPageLayout(\Eccube\Entity\PageLayout $pageLayout)
    {
        $this->PageLayout = $pageLayout;

        return $this;
    }

    /**
     * Get PageLayout
     *
     * @return \Eccube\Entity\PageLayout 
     */
    public function getPageLayout()
    {
        return $this->PageLayout;
    }
}

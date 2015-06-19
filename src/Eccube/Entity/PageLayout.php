<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageLayout
 */
class PageLayout extends \Eccube\Entity\AbstractEntity
{
    // 配置ID
    /** 配置ID: 未使用 */
    const TARGET_ID_UNUSED = 0;
    const TARGET_ID_HEAD = 1;
    const TARGET_ID_HEADER = 2;
    const TARGET_ID_CONTENTS_TOP = 3;
    const TARGET_ID_SIDE_LEFT = 4;
    const TARGET_ID_MAIN_TOP = 5;
    const TARGET_ID_MAIN_BOTTOM = 6;
    const TARGET_ID_SIDE_RIGHT = 7;
    const TARGET_ID_CONTENTS_BOTTOM = 8;
    const TARGET_ID_FOOTER = 9;

    // 編集可能フラグ
    const EDIT_FLG_USER = 0;
    const EDIT_FLG_PREVIEW = 1;
    const EDIT_FLG_DEFAULT = 2;

    /**
     * Get ColumnNum
     *
     * @return integer
     */
    public function getColumnNum()
    {
        return 1 + ($this->getSideLeft() ? 1 : 0) + ($this->getSideRight() ? 1 : 0);
    }

    public function getTheme()
    {
        $hasLeft = $this->getSideLeft() ? true : false;
        $hasRight = $this->getSideRight() ? true : false;

        $theme = 'theme_main_only';
        if ($hasLeft && $hasRight) {
            $theme = 'theme_side_both';
        } elseif ($hasLeft) {
            $theme = 'theme_side_left';
        } elseif ($hasRight) {
            $theme = 'theme_side_right';
        }

        return $theme;
    }

    /**
     * Get BlockPositionByTargetId
     *
     * @param integer $target_id
     * @return \Eccube\Entity\BlockPosition
     */
    public function getBlocksPositionByTargetId($target_id)
    {
        $BlockPositions = array();
        foreach ($this->getBlockPositions() as $BlockPosition) {
            if ($BlockPosition->getTargetId() === $target_id) {
                $BlockPositions[] = $BlockPosition;
            }
        }

        return $BlockPositions;
    }

    public function getUnusedPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_UNUSED);
    }

    public function getHeadPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_HEAD);
    }

    public function getHeaderPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_HEADER);
    }

    public function getContentsTopPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_CONTENTS_TOP);
    }

    public function getSideLeftPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_SIDE_LEFT);
    }

    public function getMainTopPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_MAIN_TOP);
    }

    public function getMainBottomPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_MAIN_BOTTOM);
    }

    public function getSideRightPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_SIDE_RIGHT);
    }

    public function getContentsBottomPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_CONTENTS_BOTTOM);
    }

    public function getFooterPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_FOOTER);
    }

    /**
     * Get BlocsByTargetId
     *
     * @param integer $target_id
     * @return \Eccube\Entity\Bloc[]
     */
    public function getBlocksByTargetId($target_id)
    {
        $Blocks = array();
        foreach ($this->getBlockPositions() as $BlockPositions) {
            if ($BlockPositions->getTargetId() === $target_id) {
                $Blocks[] = $BlockPositions->getBlock();
            }
        }
        return $Blocks;
    }

    public function getUnused()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_UNUSED);
    }

    public function getHead()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_HEAD);
    }

    public function getHeader()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_HEADER);
    }

    public function getContentsTop()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_CONTENTS_TOP);
    }

    public function getSideLeft()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_SIDE_LEFT);
    }

    public function getMainTop()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_MAIN_TOP);
    }

    public function getMainBottom()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_MAIN_BOTTOM);
    }

    public function getSideRight()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_SIDE_RIGHT);
    }

    public function getContentsBottom()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_CONTENTS_BOTTOM);
    }

    public function getFooter()
    {
        return $this->getBlocksByTargetId(self::TARGET_ID_FOOTER);
    }


    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $file_name;

    /**
     * @var integer
     */
    private $edit_flg;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $keyword;

    /**
     * @var string
     */
    private $update_url;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var string
     */
    private $meta_robots;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $BlockPositions;

    /**
     * @var \Eccube\Entity\Master\DeviceType
     */
    private $DeviceType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->BlockPositions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     * @return PageLayout
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PageLayout
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return PageLayout
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set file_name
     *
     * @param string $fileName
     * @return PageLayout
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set edit_flg
     *
     * @param integer $editFlg
     * @return PageLayout
     */
    public function setEditFlg($editFlg)
    {
        $this->edit_flg = $editFlg;

        return $this;
    }

    /**
     * Get edit_flg
     *
     * @return integer 
     */
    public function getEditFlg()
    {
        return $this->edit_flg;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return PageLayout
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return PageLayout
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keyword
     *
     * @param string $keyword
     * @return PageLayout
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set update_url
     *
     * @param string $updateUrl
     * @return PageLayout
     */
    public function setUpdateUrl($updateUrl)
    {
        $this->update_url = $updateUrl;

        return $this;
    }

    /**
     * Get update_url
     *
     * @return string 
     */
    public function getUpdateUrl()
    {
        return $this->update_url;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return PageLayout
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return PageLayout
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set meta_robots
     *
     * @param string $metaRobots
     * @return PageLayout
     */
    public function setMetaRobots($metaRobots)
    {
        $this->meta_robots = $metaRobots;

        return $this;
    }

    /**
     * Get meta_robots
     *
     * @return string 
     */
    public function getMetaRobots()
    {
        return $this->meta_robots;
    }

    /**
     * Add BlockPosition
     *
     * @param \Eccube\Entity\BlockPosition $blockPosition
     * @return PageLayout
     */
    public function addBlockPosition(\Eccube\Entity\BlockPosition $blockPosition)
    {
        $this->BlockPositions[] = $blockPosition;

        return $this;
    }

    /**
     * Remove BlockPosition
     *
     * @param \Eccube\Entity\BlockPosition $blockPosition
     */
    public function removeBlockPosition(\Eccube\Entity\BlockPosition $blockPosition)
    {
        $this->BlockPositions->removeElement($blockPosition);
    }

    /**
     * Get BlockPositions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBlockPositions()
    {
        return $this->BlockPositions;
    }

    /**
     * Set DeviceType
     *
     * @param \Eccube\Entity\Master\DeviceType $deviceType
     * @return PageLayout
     */
    public function setDeviceType(\Eccube\Entity\Master\DeviceType $deviceType = null)
    {
        $this->DeviceType = $deviceType;

        return $this;
    }

    /**
     * Get DeviceType
     *
     * @return \Eccube\Entity\Master\DeviceType 
     */
    public function getDeviceType()
    {
        return $this->DeviceType;
    }
}

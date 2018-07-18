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
 * Page
 *
 * @ORM\Table(name="dtb_page", indexes={@ORM\Index(name="dtb_page_url_idx", columns={"url"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\PageRepository")
 */
class Page extends \Eccube\Entity\AbstractEntity
{
    // 配置ID
    /** 配置ID: 未使用 */
    const TARGET_ID_UNUSED = 0;
    const TARGET_ID_HEAD = 1;
    const TARGET_ID_BODY_AFTER = 2;
    const TARGET_ID_HEADER = 3;
    const TARGET_ID_CONTENTS_TOP = 4;
    const TARGET_ID_SIDE_LEFT = 5;
    const TARGET_ID_MAIN_TOP = 6;
    const TARGET_ID_MAIN_BOTTOM = 7;
    const TARGET_ID_SIDE_RIGHT = 8;
    const TARGET_ID_CONTENTS_BOTTOM = 9;
    const TARGET_ID_FOOTER = 10;
    const TARGET_ID_DRAWER = 11;
    const TARGET_ID_CLOSE_BODY_BEFORE = 12;

    // 編集可能フラグ
    const EDIT_TYPE_USER = 0;
    const EDIT_TYPE_PREVIEW = 1;
    const EDIT_TYPE_DEFAULT = 2;

    public function getLayouts()
    {
        $Layouts = [];
        foreach ($this->PageLayouts as $PageLayout) {
            $Layouts[] = $PageLayout->getLayout();
        }

        return $Layouts;
    }

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
     *
     * @return array
     */
    public function getBlocksPositionByTargetId($target_id)
    {
        $BlockPositions = [];
        foreach ($this->getBlockPositions() as $BlockPosition) {
            if ($BlockPosition->getSection() === $target_id) {
                $BlockPositions[] = $BlockPosition;
            }
        }

        return $BlockPositions;
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getUnusedPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_UNUSED);
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getHeadPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_HEAD);
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getHeaderPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_HEADER);
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getContentsTopPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_CONTENTS_TOP);
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getSideLeftPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_SIDE_LEFT);
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getMainTopPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_MAIN_TOP);
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getMainBottomPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_MAIN_BOTTOM);
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getSideRightPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_SIDE_RIGHT);
    }

    /**
     * @deprecated
     *
     * @return BlockPosition
     */
    public function getContentsBottomPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_CONTENTS_BOTTOM);
    }

    public function getFooterPosition()
    {
        return $this->getBlocksPositionByTargetId(self::TARGET_ID_FOOTER);
    }

    /**
     * @deprecated
     *
     * Get BlocsByTargetId
     *
     * @param integer $target_id
     *
     * @return \Eccube\Entity\Block[]
     */
    public function getBlocksByTargetId($target_id)
    {
        $Blocks = [];
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
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_HEAD) : [];
    }

    public function getBodyAfter()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_BODY_AFTER) : [];
    }

    public function getHeader()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_HEADER) : [];
    }

    public function getContentsTop()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_CONTENTS_TOP) : [];
    }

    public function getSideLeft()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_SIDE_LEFT) : [];
    }

    public function getMainTop()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_MAIN_TOP) : [];
    }

    public function getMainBottom()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_MAIN_BOTTOM) : [];
    }

    public function getSideRight()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_SIDE_RIGHT) : [];
    }

    public function getContentsBottom()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_CONTENTS_BOTTOM) : [];
    }

    public function getFooter()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_FOOTER) : [];
    }

    public function getDrawer()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_DRAWER) : [];
    }

    public function getCloseBodyBefore()
    {
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_CLOSE_BODY_BEFORE) : [];
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="page_name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_name", type="string", length=255, nullable=true)
     */
    private $file_name;

    /**
     * @var int
     *
     * @ORM\Column(name="edit_type", type="smallint", options={"unsigned":true,"default":1})
     */
    private $edit_type = 1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="keyword", type="string", length=255, nullable=true)
     */
    private $keyword;

    /**
     * @var string|null
     *
     * @ORM\Column(name="update_url", type="string", length=255, nullable=true)
     */
    private $update_url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="meta_robots", type="string", length=255, nullable=true)
     */
    private $meta_robots;

    /**
     * @var string|null
     *
     * @ORM\Column(name="meta_tags", type="string", length=4000, nullable=true)
     */
    private $meta_tags;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\BlockPosition", mappedBy="Page", cascade={"persist","remove"})
     *
     * @deprecated
     */
    private $BlockPositions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\PageLayout", mappedBy="Page", cascade={"persist","remove"})
     */
    private $PageLayouts;

    /**
     * @var \Eccube\Entity\Master\DeviceType
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\DeviceType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="device_type_id", referencedColumnName="id")
     * })
     */
    private $DeviceType;

    /**
     * @var \Eccube\Entity\Page
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Page")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="master_page_id", referencedColumnName="id")
     * })
     */
    private $MasterPage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->BlockPositions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->PageLayouts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @return Page
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
     * Set name.
     *
     * @param string|null $name
     *
     * @return Page
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Page
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set fileName.
     *
     * @param string|null $fileName
     *
     * @return Page
     */
    public function setFileName($fileName = null)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get fileName.
     *
     * @return string|null
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set editType.
     *
     * @param int $editType
     *
     * @return Page
     */
    public function setEditType($editType)
    {
        $this->edit_type = $editType;

        return $this;
    }

    /**
     * Get editType.
     *
     * @return int
     */
    public function getEditType()
    {
        return $this->edit_type;
    }

    /**
     * Set author.
     *
     * @param string|null $author
     *
     * @return Page
     */
    public function setAuthor($author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return string|null
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Page
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keyword.
     *
     * @param string|null $keyword
     *
     * @return Page
     */
    public function setKeyword($keyword = null)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword.
     *
     * @return string|null
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set updateUrl.
     *
     * @param string|null $updateUrl
     *
     * @return Page
     */
    public function setUpdateUrl($updateUrl = null)
    {
        $this->update_url = $updateUrl;

        return $this;
    }

    /**
     * Get updateUrl.
     *
     * @return string|null
     */
    public function getUpdateUrl()
    {
        return $this->update_url;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return Page
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     *
     * @return Page
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set metaRobots.
     *
     * @param string|null $metaRobots
     *
     * @return Page
     */
    public function setMetaRobots($metaRobots = null)
    {
        $this->meta_robots = $metaRobots;

        return $this;
    }

    /**
     * Get metaRobots.
     *
     * @return string|null
     */
    public function getMetaRobots()
    {
        return $this->meta_robots;
    }

    /**
     * Set meta_tags
     *
     * @param string $metaTags
     *
     * @return Page
     */
    public function setMetaTags($metaTags)
    {
        $this->meta_tags = $metaTags;

        return $this;
    }

    /**
     * Get meta_tags
     *
     * @return string
     */
    public function getMetaTags()
    {
        return $this->meta_tags;
    }

    /**
     * Add blockPosition.
     *
     * @param \Eccube\Entity\BlockPosition $blockPosition
     *
     * @return Page
     */
    public function addBlockPosition(\Eccube\Entity\BlockPosition $blockPosition)
    {
        $this->BlockPositions[] = $blockPosition;

        return $this;
    }

    /**
     * Remove blockPosition.
     *
     * @param \Eccube\Entity\BlockPosition $blockPosition
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBlockPosition(\Eccube\Entity\BlockPosition $blockPosition)
    {
        return $this->BlockPositions->removeElement($blockPosition);
    }

    /**
     * Get pageLayoutLayout.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPageLayouts()
    {
        return $this->PageLayouts;
    }

    /**
     * Add pageLayoutLayout
     *
     * @param \Eccube\Entity\PageLayout $PageLayout
     *
     * @return Page
     */
    public function addPageLayout(\Eccube\Entity\PageLayout $PageLayout)
    {
        $this->PageLayouts[] = $PageLayout;

        return $this;
    }

    /**
     * Remove pageLayoutLayout
     *
     * @param \Eccube\Entity\PageLayout $PageLayout
     */
    public function removePageLayout(\Eccube\Entity\PageLayout $PageLayout)
    {
        $this->PageLayouts->removeElement($PageLayout);
    }

    /**
     * Get blockPositions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlockPositions()
    {
        return $this->BlockPositions;
    }

    /**
     * Set deviceType.
     *
     * @param \Eccube\Entity\Master\DeviceType|null $deviceType
     *
     * @return Page
     */
    public function setDeviceType(\Eccube\Entity\Master\DeviceType $deviceType = null)
    {
        $this->DeviceType = $deviceType;

        return $this;
    }

    /**
     * Get deviceType.
     *
     * @return \Eccube\Entity\Master\DeviceType|null
     */
    public function getDeviceType()
    {
        return $this->DeviceType;
    }

    /**
     * Set MasterPage.
     *
     * @param \Eccube\Entity\Page|null $page
     *
     * @return Page
     */
    public function setMasterPage(\Eccube\Entity\Page $page = null)
    {
        $this->MasterPage = $page;

        return $this;
    }

    /**
     * Get MasterPage.
     *
     * @return \Eccube\Entity\Page|null
     */
    public function getMasterPage()
    {
        return $this->MasterPage;
    }

    /**
     * @param $layoutId
     *
     * @return null|int
     */
    public function getSortNo($layoutId)
    {
        $pageLayouts = $this->getPageLayouts();

        /** @var PageLayout $pageLayout */
        foreach ($pageLayouts as $pageLayout) {
            if ($pageLayout->getLayoutId() == $layoutId) {
                return $pageLayout->getSortNo();
            }
        }

        return null;
    }
}

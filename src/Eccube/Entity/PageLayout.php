<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageLayout
 *
 * @ORM\Table(name="dtb_page_layout", indexes={@ORM\Index(name="dtb_page_layout_url_idx", columns={"url"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\PageLayoutRepository")
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

    public function getLayouts()
    {
        $Layouts = [];
        foreach ($this->PageLayoutLayouts as $PageLayoutLayout) {
            $Layouts[] = $PageLayoutLayout->getLayout();
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
        $Layout = current($this->getLayouts());

        return $Layout ? $Layout->getBlocks(self::TARGET_ID_HEAD) : [];
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


    /**
     * @var int
     *
     * @ORM\Column(name="page_id", type="integer", options={"unsigned":true})
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
     * @ORM\Column(name="edit_flg", type="smallint", options={"unsigned":true,"default":1})
     */
    private $edit_flg = 1;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\BlockPosition", mappedBy="PageLayout", cascade={"persist","remove"})
     *
     * @deprecated
     */
    private $BlockPositions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\PageLayoutLayout", mappedBy="PageLayout", cascade={"persist","remove"})
     */
    private $PageLayoutLayouts;

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
     * Constructor
     */
    public function __construct()
    {
        $this->BlockPositions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->PageLayoutLayouts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
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
     * Set name.
     *
     * @param string|null $name
     *
     * @return PageLayout
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
     * @return PageLayout
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
     * @return PageLayout
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
     * Set editFlg.
     *
     * @param int $editFlg
     *
     * @return PageLayout
     */
    public function setEditFlg($editFlg)
    {
        $this->edit_flg = $editFlg;

        return $this;
    }

    /**
     * Get editFlg.
     *
     * @return int
     */
    public function getEditFlg()
    {
        return $this->edit_flg;
    }

    /**
     * Set author.
     *
     * @param string|null $author
     *
     * @return PageLayout
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
     * @return PageLayout
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
     * @return PageLayout
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
     * @return PageLayout
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
     * @return PageLayout
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
     * @return PageLayout
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
     * @return PageLayout
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
     * Add blockPosition.
     *
     * @param \Eccube\Entity\BlockPosition $blockPosition
     *
     * @return PageLayout
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
    public function getPageLayoutLayouts()
    {
        return $this->PageLayoutLayouts;
    }

    /**
     * Add pageLayoutLayout
     *
     * @param \Eccube\Entity\PageLayoutLayout $pageLayoutLayout
     *
     * @return PageLayout
     */
    public function addPageLayoutLayout(\Eccube\Entity\PageLayoutLayout $pageLayoutLayout)
    {
        $this->PageLayoutLayouts[] = $pageLayoutLayout;

        return $this;
    }

    /**
     * Remove pageLayoutLayout
     *
     * @param \Eccube\Entity\PageLayoutLayout $pageLayoutLayout
     */
    public function removePageLayoutLayout(\Eccube\Entity\PageLayoutLayout $pageLayoutLayout)
    {
        $this->PageLayoutLayouts->removeElement($pageLayoutLayout);
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
     * @return PageLayout
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
}

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

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\Layout')) {
    /**
     * Layout
     *
     * @ORM\Table(name="dtb_layout")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\LayoutRepository")
     */
    class Layout extends AbstractEntity
    {
        // 配置ID
        /** 配置ID: 未使用 */
        public const TARGET_ID_UNUSED = 0;
        public const TARGET_ID_HEAD = 1;
        public const TARGET_ID_BODY_AFTER = 2;
        public const TARGET_ID_HEADER = 3;
        public const TARGET_ID_CONTENTS_TOP = 4;
        public const TARGET_ID_SIDE_LEFT = 5;
        public const TARGET_ID_MAIN_TOP = 6;
        public const TARGET_ID_MAIN_BOTTOM = 7;
        public const TARGET_ID_SIDE_RIGHT = 8;
        public const TARGET_ID_CONTENTS_BOTTOM = 9;
        public const TARGET_ID_FOOTER = 10;
        public const TARGET_ID_DRAWER = 11;
        public const TARGET_ID_CLOSE_BODY_BEFORE = 12;

        /**
         * プレビュー用レイアウト
         */
        public const DEFAULT_LAYOUT_PREVIEW_PAGE = 0;

        /**
         * トップページ用レイアウト
         */
        public const DEFAULT_LAYOUT_TOP_PAGE = 1;

        /**
         * 下層ページ用レイアウト
         */
        public const DEFAULT_LAYOUT_UNDERLAYER_PAGE = 2;

        /**
         * @return string
         */
        public function __toString()
        {
            return (string) $this->name;
        }

        public function isDefault()
        {
            return in_array($this->id, [self::DEFAULT_LAYOUT_PREVIEW_PAGE, self::DEFAULT_LAYOUT_TOP_PAGE, self::DEFAULT_LAYOUT_UNDERLAYER_PAGE]);
        }

        /**
         * @return Page[]
         */
        public function getPages()
        {
            $Pages = [];
            foreach ($this->PageLayouts as $PageLayout) {
                $Pages[] = $PageLayout->getPage();
            }

            return $Pages;
        }

        /**
         * @param integer|null $targetId
         *
         * @return Block[]
         */
        public function getBlocks($targetId = null)
        {
            /** @var BlockPosition[] $TargetBlockPositions */
            $TargetBlockPositions = [];
            // $targetIdのBlockPositionのみ抽出
            foreach ($this->BlockPositions as $BlockPosition) {
                if (is_null($targetId)) {
                    $TargetBlockPositions[] = $BlockPosition;
                    continue;
                }

                if ($BlockPosition->getSection() == $targetId) {
                    $TargetBlockPositions[] = $BlockPosition;
                }
            }

            // blockRow順にsort
            uasort($TargetBlockPositions, function (BlockPosition $a, BlockPosition $b) {
                return ($a->getBlockRow() < $b->getBlockRow()) ? -1 : 1;
            });

            // Blockの配列を作成
            $TargetBlocks = [];
            foreach ($TargetBlockPositions as $BlockPosition) {
                $TargetBlocks[] = $BlockPosition->getBlock();
            }

            return $TargetBlocks;
        }

        /**
         * @param integer $targetId
         *
         * @return BlockPosition[]
         */
        public function getBlockPositionsByTargetId($targetId)
        {
            return $this->BlockPositions->filter(
                function ($BlockPosition) use ($targetId) {
                    return $BlockPosition->getSection() == $targetId;
                }
            );
        }

        public function getUnused()
        {
            return $this->getBlocks(self::TARGET_ID_UNUSED);
        }

        public function getHead()
        {
            return $this->getBlocks(self::TARGET_ID_HEAD);
        }

        public function getBodyAfter()
        {
            return $this->getBlocks(self::TARGET_ID_BODY_AFTER);
        }

        public function getHeader()
        {
            return $this->getBlocks(self::TARGET_ID_HEADER);
        }

        public function getContentsTop()
        {
            return $this->getBlocks(self::TARGET_ID_CONTENTS_TOP);
        }

        public function getSideLeft()
        {
            return $this->getBlocks(self::TARGET_ID_SIDE_LEFT);
        }

        public function getMainTop()
        {
            return $this->getBlocks(self::TARGET_ID_MAIN_TOP);
        }

        public function getMainBottom()
        {
            return $this->getBlocks(self::TARGET_ID_MAIN_BOTTOM);
        }

        public function getSideRight()
        {
            return $this->getBlocks(self::TARGET_ID_SIDE_RIGHT);
        }

        public function getContentsBottom()
        {
            return $this->getBlocks(self::TARGET_ID_CONTENTS_BOTTOM);
        }

        public function getFooter()
        {
            return $this->getBlocks(self::TARGET_ID_FOOTER);
        }

        public function getDrawer()
        {
            return $this->getBlocks(self::TARGET_ID_DRAWER);
        }

        public function getCloseBodyBefore()
        {
            return $this->getBlocks(self::TARGET_ID_CLOSE_BODY_BEFORE);
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

        // -----------------------
        // generated by doctrine
        // -----------------------

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="layout_name", type="string", length=255, nullable=true)
         */
        private $name;

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
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\BlockPosition", mappedBy="Layout", cascade={"persist","remove"})
         */
        private $BlockPositions;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\PageLayout", mappedBy="Layout", cascade={"persist","remove"})
         * @ORM\OrderBy({"sort_no" = "ASC"})
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
         * Constructor
         */
        public function __construct()
        {
            $this->BlockPositions = new \Doctrine\Common\Collections\ArrayCollection();
            $this->PageLayouts = new \Doctrine\Common\Collections\ArrayCollection();
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
         *
         * @return Layout
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
         * Set createDate
         *
         * @param \DateTime $createDate
         *
         * @return Layout
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set updateDate
         *
         * @param \DateTime $updateDate
         *
         * @return Layout
         */
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

        /**
         * Add blockPosition
         *
         * @param \Eccube\Entity\BlockPosition $blockPosition
         *
         * @return Layout
         */
        public function addBlockPosition(BlockPosition $blockPosition)
        {
            $this->BlockPositions[] = $blockPosition;

            return $this;
        }

        /**
         * Remove blockPosition
         *
         * @param \Eccube\Entity\BlockPosition $blockPosition
         */
        public function removeBlockPosition(BlockPosition $blockPosition)
        {
            $this->BlockPositions->removeElement($blockPosition);
        }

        /**
         * Get blockPositions
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getBlockPositions()
        {
            return $this->BlockPositions;
        }

        /**
         * Add pageLayoutLayout
         *
         * @param \Eccube\Entity\PageLayout $PageLayout
         *
         * @return Layout
         */
        public function addPageLayout(PageLayout $PageLayout)
        {
            $this->PageLayouts[] = $PageLayout;

            return $this;
        }

        /**
         * Remove pageLayoutLayout
         *
         * @param \Eccube\Entity\PageLayout $PageLayout
         */
        public function removePageLayout(PageLayout $PageLayout)
        {
            $this->PageLayouts->removeElement($PageLayout);
        }

        /**
         * Get pageLayoutLayouts
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getPageLayouts()
        {
            return $this->PageLayouts;
        }

        /**
         * Set deviceType
         *
         * @param \Eccube\Entity\Master\DeviceType $deviceType
         *
         * @return Layout
         */
        public function setDeviceType(Master\DeviceType $deviceType = null)
        {
            $this->DeviceType = $deviceType;

            return $this;
        }

        /**
         * Get deviceType
         *
         * @return \Eccube\Entity\Master\DeviceType
         */
        public function getDeviceType()
        {
            return $this->DeviceType;
        }

        /**
         * Check layout can delete or not
         *
         * @return boolean
         */
        public function isDeletable()
        {
            if (!$this->getPageLayouts()->isEmpty()) {
                return false;
            }

            return true;
        }
    }
}

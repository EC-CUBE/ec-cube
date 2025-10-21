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
use Eccube\Repository\LayoutRepository;

if (!class_exists(Layout::class)) {
    /**
     * Layout
     */
    #[ORM\Table(name: 'dtb_layout')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: LayoutRepository::class)]
    class Layout extends AbstractEntity implements \Stringable
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
        #[\Override]
        public function __toString(): string
        {
            return (string) $this->name;
        }

        /**
         * @return bool
         */
        public function isDefault(): bool
        {
            return in_array($this->id, [self::DEFAULT_LAYOUT_PREVIEW_PAGE, self::DEFAULT_LAYOUT_TOP_PAGE, self::DEFAULT_LAYOUT_UNDERLAYER_PAGE]);
        }

        /**
         * @return Page[]
         */
        public function getPages(): array
        {
            $Pages = [];
            foreach ($this->PageLayouts as $PageLayout) {
                $Pages[] = $PageLayout->getPage();
            }

            return $Pages;
        }

        /**
         * @param int|null $targetId
         *
         * @return Block[]
         */
        public function getBlocks($targetId = null): array
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
         * @param int $targetId
         *
         * @return Collection<int, BlockPosition>
         */
        public function getBlockPositionsByTargetId($targetId): Collection
        {
            return $this->BlockPositions->filter(
                function ($BlockPosition) use ($targetId) {
                    return $BlockPosition->getSection() == $targetId;
                }
            );
        }

        /**
         * @return Block[]
         */
        public function getUnused(): array
        {
            return $this->getBlocks(self::TARGET_ID_UNUSED);
        }

        /**
         * @return Block[]
         */
        public function getHead(): array
        {
            return $this->getBlocks(self::TARGET_ID_HEAD);
        }

        /**
         * @return Block[]
         */
        public function getBodyAfter(): array
        {
            return $this->getBlocks(self::TARGET_ID_BODY_AFTER);
        }

        /**
         * @return Block[]
         */
        public function getHeader(): array
        {
            return $this->getBlocks(self::TARGET_ID_HEADER);
        }

        /**
         * @return Block[]
         */
        public function getContentsTop(): array
        {
            return $this->getBlocks(self::TARGET_ID_CONTENTS_TOP);
        }

        /**
         * @return Block[]
         */
        public function getSideLeft(): array
        {
            return $this->getBlocks(self::TARGET_ID_SIDE_LEFT);
        }

        /**
         * @return Block[]
         */
        public function getMainTop(): array
        {
            return $this->getBlocks(self::TARGET_ID_MAIN_TOP);
        }

        /**
         * @return Block[]
         */
        public function getMainBottom(): array
        {
            return $this->getBlocks(self::TARGET_ID_MAIN_BOTTOM);
        }

        /**
         * @return Block[]
         */
        public function getSideRight(): array
        {
            return $this->getBlocks(self::TARGET_ID_SIDE_RIGHT);
        }

        /**
         * @return Block[]
         */
        public function getContentsBottom(): array
        {
            return $this->getBlocks(self::TARGET_ID_CONTENTS_BOTTOM);
        }

        /**
         * @return Block[]
         */
        public function getFooter(): array
        {
            return $this->getBlocks(self::TARGET_ID_FOOTER);
        }

        /**
         * @return Block[]
         */
        public function getDrawer(): array
        {
            return $this->getBlocks(self::TARGET_ID_DRAWER);
        }

        /**
         * @return Block[]
         */
        public function getCloseBodyBefore(): array
        {
            return $this->getBlocks(self::TARGET_ID_CLOSE_BODY_BEFORE);
        }

        /**
         * Get ColumnNum
         *
         * @return int
         */
        public function getColumnNum(): int
        {
            return 1 + ($this->getSideLeft() ? 1 : 0) + ($this->getSideRight() ? 1 : 0);
        }

        // -----------------------
        // generated by doctrine
        // -----------------------
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'layout_name', type: 'string', length: 255, nullable: true)]
        private $name;

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
        #[ORM\OneToMany(targetEntity: BlockPosition::class, mappedBy: 'Layout', cascade: ['persist', 'remove'])]
        private $BlockPositions;

        /**
         * @var Collection<int, PageLayout>
         */
        #[ORM\OneToMany(targetEntity: PageLayout::class, mappedBy: 'Layout', cascade: ['persist', 'remove'])]
        #[ORM\OrderBy(['sort_no' => 'ASC'])]
        private $PageLayouts;

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
            $this->PageLayouts = new ArrayCollection();
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
         * @return Layout
         */
        public function setName($name): Layout
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
         * Set createDate
         *
         * @param \DateTime $createDate
         *
         * @return Layout
         */
        public function setCreateDate($createDate): Layout
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
         * @return Layout
         */
        public function setUpdateDate($updateDate): Layout
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
         * @return Layout
         */
        public function addBlockPosition(BlockPosition $blockPosition): Layout
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
         * Add pageLayoutLayout
         *
         * @param PageLayout $PageLayout
         *
         * @return Layout
         */
        public function addPageLayout(PageLayout $PageLayout): Layout
        {
            $this->PageLayouts[] = $PageLayout;

            return $this;
        }

        /**
         * Remove pageLayoutLayout
         *
         * @param PageLayout $PageLayout
         *
         * @return void
         */
        public function removePageLayout(PageLayout $PageLayout): void
        {
            $this->PageLayouts->removeElement($PageLayout);
        }

        /**
         * Get pageLayoutLayouts
         *
         * @return Collection<int, PageLayout>
         */
        public function getPageLayouts(): Collection
        {
            return $this->PageLayouts;
        }

        /**
         * Set deviceType
         *
         * @param DeviceType $deviceType
         *
         * @return Layout
         */
        public function setDeviceType(?DeviceType $deviceType = null): Layout
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

        /**
         * Check layout can delete or not
         *
         * @return bool
         */
        public function isDeletable(): bool
        {
            if (!$this->getPageLayouts()->isEmpty()) {
                return false;
            }

            return true;
        }
    }
}

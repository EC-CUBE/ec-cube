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

if (!class_exists('\Eccube\Entity\BlockPosition')) {
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
         * @var \Eccube\Entity\Block
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Block", inversedBy="BlockPositions")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="block_id", referencedColumnName="id")
         * })
         */
        private $Block;

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
         * Set block.
         *
         * @param \Eccube\Entity\Block|null $block
         *
         * @return BlockPosition
         */
        public function setBlock(Block $block = null)
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
         * @param \Eccube\Entity\Layout|null $Layout
         *
         * @return BlockPosition
         */
        public function setLayout(Layout $Layout = null)
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
    }
}

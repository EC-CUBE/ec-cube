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
use Eccube\Repository\PageLayoutRepository;

if (!class_exists(PageLayout::class)) {
    /**
     * PageLayout
     */
    #[ORM\Table(name: 'dtb_page_layout')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: PageLayoutRepository::class)]
    class PageLayout extends AbstractEntity
    {
        /**
         * @var int
         */
        #[ORM\Column(name: 'page_id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        private $page_id;

        /**
         * @var int
         */
        #[ORM\Column(name: 'layout_id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        private $layout_id;

        /**
         * @var int
         */
        #[ORM\Column(name: 'sort_no', type: 'smallint', options: ['unsigned' => true])]
        private $sort_no;

        /**
         * @var Page|null
         */
        #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'PageLayouts')]
        #[ORM\JoinColumn(name: 'page_id', referencedColumnName: 'id')]
        private $Page;

        /**
         * @var Layout|null
         */
        #[ORM\ManyToOne(targetEntity: Layout::class, inversedBy: 'PageLayouts')]
        #[ORM\JoinColumn(name: 'layout_id', referencedColumnName: 'id')]
        private $Layout;

        /**
         * Set pageId
         */
        public function setPageId(int $pageId): PageLayout
        {
            $this->page_id = $pageId;

            return $this;
        }

        /**
         * Get pageId
         */
        public function getPageId(): int
        {
            return $this->page_id;
        }

        /**
         * Set layoutId
         */
        public function setLayoutId(int $layoutId): PageLayout
        {
            $this->layout_id = $layoutId;

            return $this;
        }

        /**
         * Get layoutId
         */
        public function getLayoutId(): int
        {
            return $this->layout_id;
        }

        /**
         * Set sort_no
         */
        public function setSortNo(int $sortNo): PageLayout
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sort_no
         */
        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        /**
         * Set pageLayout
         *
         * @param Page $Page
         */
        public function setPage(?Page $Page = null): PageLayout
        {
            $this->Page = $Page;

            return $this;
        }

        /**
         * Get pageLayout
         */
        public function getPage(): Page
        {
            return $this->Page;
        }

        /**
         * Set layout
         *
         * @param Layout $layout
         */
        public function setLayout(?Layout $layout = null): PageLayout
        {
            $this->Layout = $layout;

            return $this;
        }

        /**
         * Get layout
         */
        public function getLayout(): Layout
        {
            return $this->Layout;
        }

        /**
         * DeviceTypeがあればDeviceTypeIdを返す
         * DeviceTypeがなければnullを返す
         */
        public function getDeviceTypeId(): ?int
        {
            if ($this->Layout->getDeviceType()) {
                return $this->Layout->getDeviceType()->getId();
            }

            return null;
        }
    }
}

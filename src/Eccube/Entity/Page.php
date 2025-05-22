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

if (!class_exists('\Eccube\Entity\Page')) {
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
        // 編集可能フラグ
        public const EDIT_TYPE_USER = 0;
        public const EDIT_TYPE_PREVIEW = 1;
        public const EDIT_TYPE_DEFAULT = 2;
        public const EDIT_TYPE_DEFAULT_CONFIRM = 3;

        // 特定商取引法ページID
        public const TRADELAW_PAGE_ID = 21;

        // ご利用規約ページID
        public const AGREEMENT_PAGE_ID = 19;

        public function getLayouts()
        {
            $Layouts = [];
            foreach ($this->PageLayouts as $PageLayout) {
                $Layouts[] = $PageLayout->getLayout();
            }

            return $Layouts;
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
         * @ORM\OneToMany(targetEntity="Eccube\Entity\PageLayout", mappedBy="Page", cascade={"persist","remove"})
         */
        private $PageLayouts;

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
         * Set MasterPage.
         *
         * @param \Eccube\Entity\Page|null $page
         *
         * @return Page
         */
        public function setMasterPage(Page $page = null)
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
         * @return int|null
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
}

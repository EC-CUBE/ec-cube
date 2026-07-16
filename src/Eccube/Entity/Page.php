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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\PageRepository;

/**
 * Page
 */
#[ORM\Table(name: 'dtb_page')]
#[ORM\Index(columns: ['url'], name: 'dtb_page_url_idx')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page extends AbstractEntity
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

    /**
     * @return array|Layout[]
     */
    public function getLayouts(): array
    {
        $Layouts = [];
        foreach ($this->PageLayouts as $PageLayout) {
            $Layouts[] = $PageLayout->getLayout();
        }

        return $Layouts;
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'page_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'url', type: Types::STRING, length: 255)]
    private ?string $url = null;

    #[ORM\Column(name: 'file_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $file_name = null;

    #[ORM\Column(name: 'edit_type', type: Types::SMALLINT, options: ['unsigned' => true, 'default' => 1])]
    private int $edit_type = 1;

    #[ORM\Column(name: 'author', type: Types::STRING, length: 255, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(name: 'description', type: Types::STRING, length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'keyword', type: Types::STRING, length: 255, nullable: true)]
    private ?string $keyword = null;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private $create_date;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private $update_date;

    #[ORM\Column(name: 'meta_robots', type: Types::STRING, length: 255, nullable: true)]
    private ?string $meta_robots = null;

    #[ORM\Column(name: 'meta_tags', type: Types::STRING, length: 4000, nullable: true)]
    private ?string $meta_tags = null;

    /**
     * @var Collection<int, PageLayout>
     */
    #[ORM\OneToMany(targetEntity: PageLayout::class, mappedBy: 'Page', cascade: ['persist', 'remove'])]
    private $PageLayouts;

    #[ORM\ManyToOne(targetEntity: Page::class)]
    #[ORM\JoinColumn(name: 'master_page_id', referencedColumnName: 'id')]
    private ?Page $MasterPage = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->PageLayouts = new ArrayCollection();
    }

    /**
     * Set id
     */
    public function setId(int $id): Page
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(?string $name = null): Page
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set url.
     */
    public function setUrl(?string $url): Page
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set fileName.
     */
    public function setFileName(?string $fileName = null): Page
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get fileName.
     */
    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    /**
     * Set editType.
     */
    public function setEditType(int $editType): Page
    {
        $this->edit_type = $editType;

        return $this;
    }

    /**
     * Get editType.
     */
    public function getEditType(): int
    {
        return $this->edit_type;
    }

    /**
     * Set author.
     */
    public function setAuthor(?string $author = null): Page
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Set description.
     */
    public function setDescription(?string $description = null): Page
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set keyword.
     */
    public function setKeyword(?string $keyword = null): Page
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword.
     */
    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): Page
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     */
    public function setUpdateDate(\DateTime $updateDate): Page
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    /**
     * Set metaRobots.
     */
    public function setMetaRobots(?string $metaRobots = null): Page
    {
        $this->meta_robots = $metaRobots;

        return $this;
    }

    /**
     * Get metaRobots.
     */
    public function getMetaRobots(): ?string
    {
        return $this->meta_robots;
    }

    /**
     * Set meta_tags
     */
    public function setMetaTags(?string $metaTags): Page
    {
        $this->meta_tags = $metaTags;

        return $this;
    }

    /**
     * Get meta_tags
     */
    public function getMetaTags(): ?string
    {
        return $this->meta_tags;
    }

    /**
     * Get pageLayoutLayout.
     *
     * @return Collection<int, PageLayout>
     */
    public function getPageLayouts(): Collection
    {
        return $this->PageLayouts;
    }

    /**
     * Add pageLayoutLayout
     */
    public function addPageLayout(PageLayout $PageLayout): Page
    {
        $this->PageLayouts[] = $PageLayout;

        return $this;
    }

    /**
     * Remove pageLayoutLayout
     */
    public function removePageLayout(PageLayout $PageLayout): void
    {
        $this->PageLayouts->removeElement($PageLayout);
    }

    /**
     * Set MasterPage.
     */
    public function setMasterPage(?Page $page = null): Page
    {
        $this->MasterPage = $page;

        return $this;
    }

    /**
     * Get MasterPage.
     */
    public function getMasterPage(): ?Page
    {
        return $this->MasterPage;
    }

    public function getSortNo(int $layoutId): ?int
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

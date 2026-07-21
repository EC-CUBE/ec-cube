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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\NewsRepository;

/**
 * News
 */
#[ORM\Table(name: 'dtb_news')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class News extends AbstractEntity implements \Stringable
{
    #[\Override]
    public function __toString(): string
    {
        return $this->getTitle();
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'publish_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    private $publish_date;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255)]
    private ?string $title = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'url', type: Types::STRING, length: 4000, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(name: 'link_method', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $link_method = false;

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

    #[ORM\Column(name: 'visible', type: Types::BOOLEAN, options: ['default' => true])]
    private ?bool $visible = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
    private ?Member $Creator = null;

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set publishDate.
     */
    public function setPublishDate(?\DateTime $publishDate = null): News
    {
        $this->publish_date = $publishDate;

        return $this;
    }

    /**
     * Get publishDate.
     */
    public function getPublishDate(): ?\DateTime
    {
        return $this->publish_date;
    }

    /**
     * Set title.
     */
    public function setTitle(string $title): News
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set description.
     */
    public function setDescription(?string $description = null): News
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
     * Set url.
     */
    public function setUrl(?string $url = null): News
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
     * Set linkMethod.
     */
    public function setLinkMethod(bool $linkMethod): News
    {
        $this->link_method = $linkMethod;

        return $this;
    }

    /**
     * Get linkMethod.
     */
    public function isLinkMethod(): bool
    {
        return $this->link_method;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): News
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
    public function setUpdateDate(\DateTime $updateDate): News
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

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): News
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Set creator.
     */
    public function setCreator(?Member $creator = null): News
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     */
    public function getCreator(): ?Member
    {
        return $this->Creator;
    }
}

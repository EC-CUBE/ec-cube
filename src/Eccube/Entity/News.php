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
use Eccube\Repository\NewsRepository;

if (!class_exists(News::class)) {
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
        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return $this->getTitle();
        }

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'publish_date', type: 'datetimetz', nullable: true)]
        private $publish_date;

        /**
         * @var string
         */
        #[ORM\Column(name: 'title', type: 'string', length: 255)]
        private $title;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'description', type: 'text', nullable: true)]
        private $description;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'url', type: 'string', length: 4000, nullable: true)]
        private $url;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'link_method', type: 'boolean', options: ['default' => false])]
        private $link_method = false;

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
         * @var bool
         */
        #[ORM\Column(name: 'visible', type: 'boolean', options: ['default' => true])]
        private $visible;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        /**
         * Get id.
         *
         * @return int|null
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set publishDate.
         *
         * @param \DateTime|null $publishDate
         *
         * @return News
         */
        public function setPublishDate($publishDate = null): News
        {
            $this->publish_date = $publishDate;

            return $this;
        }

        /**
         * Get publishDate.
         *
         * @return \DateTime|null
         */
        public function getPublishDate(): ?\DateTime
        {
            return $this->publish_date;
        }

        /**
         * Set title.
         *
         * @param string $title
         *
         * @return News
         */
        public function setTitle($title): News
        {
            $this->title = $title;

            return $this;
        }

        /**
         * Get title.
         *
         * @return string
         */
        public function getTitle(): string
        {
            return $this->title;
        }

        /**
         * Set description.
         *
         * @param string|null $description
         *
         * @return News
         */
        public function setDescription($description = null): News
        {
            $this->description = $description;

            return $this;
        }

        /**
         * Get description.
         *
         * @return string|null
         */
        public function getDescription(): ?string
        {
            return $this->description;
        }

        /**
         * Set url.
         *
         * @param string|null $url
         *
         * @return News
         */
        public function setUrl($url = null): News
        {
            $this->url = $url;

            return $this;
        }

        /**
         * Get url.
         *
         * @return string|null
         */
        public function getUrl(): ?string
        {
            return $this->url;
        }

        /**
         * Set linkMethod.
         *
         * @param bool $linkMethod
         *
         * @return News
         */
        public function setLinkMethod($linkMethod): News
        {
            $this->link_method = $linkMethod;

            return $this;
        }

        /**
         * Get linkMethod.
         *
         * @return bool
         */
        public function isLinkMethod(): bool
        {
            return $this->link_method;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return News
         */
        public function setCreateDate($createDate): News
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime|null
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return News
         */
        public function setUpdateDate($updateDate): News
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime|null
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * @return bool
         */
        public function isVisible(): bool
        {
            return $this->visible;
        }

        /**
         * @param bool $visible
         *
         * @return News
         */
        public function setVisible($visible): News
        {
            $this->visible = $visible;

            return $this;
        }

        /**
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return News
         */
        public function setCreator(?Member $creator = null): News
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return Member|null
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }
    }
}

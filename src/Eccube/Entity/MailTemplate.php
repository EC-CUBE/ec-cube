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
use Eccube\Repository\MailTemplateRepository;

if (!class_exists(MailTemplate::class)) {
    /**
     * MailTemplate
     */
    #[ORM\Table(name: 'dtb_mail_template')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: MailTemplateRepository::class)]
    class MailTemplate extends AbstractEntity implements \Stringable
    {
        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return $this->getName() ?: '';
        }

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: true)]
        private $name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'file_name', type: 'string', length: 255, nullable: true)]
        private $file_name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'mail_subject', type: 'string', length: 255, nullable: true)]
        private $mail_subject;

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
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        /**
         * テンプレートの削除可否。
         *
         * @var bool
         */
        #[ORM\Column(name: 'deletable', type: 'boolean', options: ['default' => false])]
        private bool $deletable = false;

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
         * Set name.
         *
         * @param string|null $name
         *
         * @return MailTemplate
         */
        public function setName($name = null): MailTemplate
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string|null
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * Set fileName.
         *
         * @param string|null $fileName
         *
         * @return MailTemplate
         */
        public function setFileName($fileName = null): MailTemplate
        {
            $this->file_name = $fileName;

            return $this;
        }

        /**
         * Get fileName.
         *
         * @return string|null
         */
        public function getFileName(): ?string
        {
            return $this->file_name;
        }

        /**
         * Set mailSubject.
         *
         * @param string|null $mailSubject
         *
         * @return MailTemplate
         */
        public function setMailSubject($mailSubject = null): MailTemplate
        {
            $this->mail_subject = $mailSubject;

            return $this;
        }

        /**
         * Get mailSubject.
         *
         * @return string|null
         */
        public function getMailSubject(): ?string
        {
            return $this->mail_subject;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return MailTemplate
         */
        public function setCreateDate($createDate): MailTemplate
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
         * @return MailTemplate
         */
        public function setUpdateDate($updateDate): MailTemplate
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
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return MailTemplate
         */
        public function setCreator(?Member $creator = null): MailTemplate
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

        /**
         * @return bool
         */
        public function isDeletable(): bool
        {
            return $this->deletable;
        }

        /**
         * @param bool $deletable
         *
         * @return $this
         */
        public function setDeletable(bool $deletable): static
        {
            $this->deletable = $deletable;

            return $this;
        }
    }
}

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
use Eccube\Repository\MailTemplateRepository;

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
    #[\Override]
    public function __toString(): string
    {
        return $this->getName() ?: '';
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'file_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $file_name = null;

    #[ORM\Column(name: 'mail_subject', type: Types::STRING, length: 255, nullable: true)]
    private ?string $mail_subject = null;

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

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
    private ?Member $Creator = null;

    /**
     * テンプレートの削除可否。
     */
    #[ORM\Column(name: 'deletable', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $deletable = false;

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(?string $name = null): MailTemplate
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
     * Set fileName.
     */
    public function setFileName(?string $fileName = null): MailTemplate
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
     * Set mailSubject.
     */
    public function setMailSubject(?string $mailSubject = null): MailTemplate
    {
        $this->mail_subject = $mailSubject;

        return $this;
    }

    /**
     * Get mailSubject.
     */
    public function getMailSubject(): ?string
    {
        return $this->mail_subject;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): MailTemplate
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
    public function setUpdateDate(\DateTime $updateDate): MailTemplate
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
     * Set creator.
     */
    public function setCreator(?Member $creator = null): MailTemplate
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

    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    /**
     * @return $this
     */
    public function setDeletable(bool $deletable): static
    {
        $this->deletable = $deletable;

        return $this;
    }
}

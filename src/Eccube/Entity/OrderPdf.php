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
use Eccube\Repository\OrderPdfRepository;

/**
 * OrderPdf
 */
#[ORM\Table(name: 'dtb_order_pdf')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: OrderPdfRepository::class)]
class OrderPdf extends AbstractEntity
{
    public mixed $ids;
    public \DateTime|string $issue_date;
    public mixed $default;

    #[ORM\Column(name: 'member_id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    private ?int $member_id = null;

    #[ORM\Column(name: 'title', type: Types::STRING, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(name: 'message1', type: Types::STRING, nullable: true)]
    private ?string $message1 = null;

    #[ORM\Column(name: 'message2', type: Types::STRING, nullable: true)]
    private ?string $message2 = null;

    #[ORM\Column(name: 'message3', type: Types::STRING, nullable: true)]
    private ?string $message3 = null;

    #[ORM\Column(name: 'note1', type: Types::STRING, nullable: true)]
    private ?string $note1 = null;

    #[ORM\Column(name: 'note2', type: Types::STRING, nullable: true)]
    private ?string $note2 = null;

    #[ORM\Column(name: 'note3', type: Types::STRING, nullable: true)]
    private ?string $note3 = null;

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
    private bool $visible = true;

    public function getMemberId(): int
    {
        return $this->member_id;
    }

    /**
     * @return $this
     */
    public function setMemberId(int $member_id): static
    {
        $this->member_id = $member_id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage1(): ?string
    {
        return $this->message1;
    }

    /**
     * @return $this
     */
    public function setMessage1(?string $message1): static
    {
        $this->message1 = $message1;

        return $this;
    }

    public function getMessage2(): ?string
    {
        return $this->message2;
    }

    /**
     * @return $this
     */
    public function setMessage2(?string $message2): static
    {
        $this->message2 = $message2;

        return $this;
    }

    public function getMessage3(): ?string
    {
        return $this->message3;
    }

    /**
     * @return $this
     */
    public function setMessage3(?string $message3): static
    {
        $this->message3 = $message3;

        return $this;
    }

    public function getNote1(): ?string
    {
        return $this->note1;
    }

    /**
     * @return $this
     */
    public function setNote1(?string $note1): static
    {
        $this->note1 = $note1;

        return $this;
    }

    public function getNote2(): ?string
    {
        return $this->note2;
    }

    /**
     * @return $this
     */
    public function setNote2(?string $note2): static
    {
        $this->note2 = $note2;

        return $this;
    }

    public function getNote3(): ?string
    {
        return $this->note3;
    }

    /**
     * @return $this
     */
    public function setNote3(?string $note3): static
    {
        $this->note3 = $note3;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * @return $this
     */
    public function setCreateDate(\DateTime|string $create_date): static
    {
        $this->create_date = $create_date;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    /**
     * @return $this
     */
    public function setUpdateDate(\DateTime|string $update_date): static
    {
        $this->update_date = $update_date;

        return $this;
    }

    /**
     * Set visible
     */
    public function setVisible(bool $visible): OrderPdf
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Is the visibility visible?
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }
}

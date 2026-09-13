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
use Eccube\Repository\DeliveryTimeRepository;

/**
 * DeliveryTime
 */
#[ORM\Table(name: 'dtb_delivery_time')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: DeliveryTimeRepository::class)]
class DeliveryTime extends AbstractEntity implements \Stringable
{
    #[\Override]
    public function __toString(): string
    {
        return (string) $this->delivery_time;
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'delivery_time', type: Types::STRING, length: 255)]
    private ?string $delivery_time = null;

    #[ORM\ManyToOne(targetEntity: Delivery::class, inversedBy: 'DeliveryTimes')]
    #[ORM\JoinColumn(name: 'delivery_id', referencedColumnName: 'id')]
    private ?Delivery $Delivery = null;

    #[ORM\Column(name: 'sort_no', type: Types::SMALLINT, options: ['unsigned' => true])]
    protected ?int $sort_no = null;

    #[ORM\Column(name: 'visible', type: Types::BOOLEAN, options: ['default' => true])]
    private ?bool $visible = null;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set deliveryTime.
     */
    public function setDeliveryTime(string $deliveryTime): DeliveryTime
    {
        $this->delivery_time = $deliveryTime;

        return $this;
    }

    /**
     * Get deliveryTime.
     */
    public function getDeliveryTime(): string
    {
        return $this->delivery_time;
    }

    /**
     * Set delivery.
     */
    public function setDelivery(?Delivery $delivery = null): DeliveryTime
    {
        $this->Delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery.
     */
    public function getDelivery(): ?Delivery
    {
        return $this->Delivery;
    }

    /**
     * Set sort_no.
     */
    public function setSortNo(int $sort_no): static
    {
        $this->sort_no = $sort_no;

        return $this;
    }

    /**
     * Get sort_no.
     */
    public function getSortNo(): int
    {
        return $this->sort_no;
    }

    /**
     * Set visible
     */
    public function setVisible(bool $visible): DeliveryTime
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

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): DeliveryTime
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
    public function setUpdateDate(\DateTime $updateDate): DeliveryTime
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
}

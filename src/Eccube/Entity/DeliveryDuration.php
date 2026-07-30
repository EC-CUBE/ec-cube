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
use Eccube\Repository\DeliveryDurationRepository;

/**
 * DeliveryDuration
 */
#[ORM\Table(name: 'dtb_delivery_duration')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: DeliveryDurationRepository::class)]
class DeliveryDuration extends AbstractEntity implements \Stringable
{
    #[\Override]
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'duration', type: Types::SMALLINT, options: ['default' => 0])]
    private int $duration = 0;

    #[ORM\Column(name: 'sort_no', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $sort_no = null;

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
     * Set name.
     */
    public function setName(?string $name = null): DeliveryDuration
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
     * Set duration.
     */
    public function setDuration(int $duration): DeliveryDuration
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration.
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Set sortNo.
     */
    public function setSortNo(int $sortNo): DeliveryDuration
    {
        $this->sort_no = $sortNo;

        return $this;
    }

    /**
     * Get sortNo.
     */
    public function getSortNo(): int
    {
        return $this->sort_no;
    }
}

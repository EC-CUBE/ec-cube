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
use Eccube\Repository\ClassCategoryRepository;

/**
 * ClassCategory
 */
#[ORM\Table(name: 'dtb_class_category')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ClassCategoryRepository::class)]
class ClassCategory extends AbstractEntity implements \Stringable
{
    #[\Override]
    public function __toString(): string
    {
        return $this->getName();
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'backend_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $backend_name = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'sort_no', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $sort_no = null;

    #[ORM\Column(name: 'visible', type: Types::BOOLEAN, options: ['default' => true])]
    private ?bool $visible = null;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    #[ORM\ManyToOne(targetEntity: ClassName::class, inversedBy: 'ClassCategories')]
    #[ORM\JoinColumn(name: 'class_name_id', referencedColumnName: 'id')]
    private ?ClassName $ClassName = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
    private ?Member $Creator = null;

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
     * Set backend_name.
     */
    public function setBackendName(?string $backendName): ClassCategory
    {
        $this->backend_name = $backendName;

        return $this;
    }

    /**
     * Get backend_name.
     */
    public function getBackendName(): ?string
    {
        return $this->backend_name;
    }

    /**
     * Set name.
     */
    public function setName(?string $name): ClassCategory
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set sortNo.
     */
    public function setSortNo(int $sortNo): ClassCategory
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

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): ClassCategory
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
    public function setUpdateDate(\DateTime $updateDate): ClassCategory
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
     * Set className.
     */
    public function setClassName(?ClassName $className = null): ClassCategory
    {
        $this->ClassName = $className;

        return $this;
    }

    /**
     * Get className.
     */
    public function getClassName(): ?ClassName
    {
        return $this->ClassName;
    }

    /**
     * Set creator.
     */
    public function setCreator(?Member $creator = null): ClassCategory
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

    /**
     * Set visible
     */
    public function setVisible(bool $visible): ClassCategory
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

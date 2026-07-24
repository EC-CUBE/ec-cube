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
use Eccube\Repository\ProductStockRepository;

/**
 * ProductStock
 */
#[ORM\Table(name: 'dtb_product_stock')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ProductStockRepository::class)]
class ProductStock extends AbstractEntity
{
    public const IN_STOCK = 1;
    public const OUT_OF_STOCK = 2;

    private ?int $product_class_id = null;

    /**
     * Set product_class_id
     */
    public function setProductClassId(?int $productClassId): ProductStock
    {
        $this->product_class_id = $productClassId;

        return $this;
    }

    /**
     * Get product_class_id
     */
    public function getProductClassId(): ?int
    {
        return $this->product_class_id;
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'stock', type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $stock = null;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    #[ORM\OneToOne(targetEntity: ProductClass::class, inversedBy: 'ProductStock')]
    #[ORM\JoinColumn(name: 'product_class_id', referencedColumnName: 'id')]
    private ?ProductClass $ProductClass = null;

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
     * Set stock.
     */
    public function setStock(?string $stock = null): ProductStock
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock.
     */
    public function getStock(): ?string
    {
        return $this->stock;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): ProductStock
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
    public function setUpdateDate(\DateTime $updateDate): ProductStock
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
     * Set productClass.
     */
    public function setProductClass(?ProductClass $productClass = null): ProductStock
    {
        $this->ProductClass = $productClass;

        return $this;
    }

    /**
     * Get productClass.
     */
    public function getProductClass(): ?ProductClass
    {
        return $this->ProductClass;
    }

    /**
     * Set creator.
     */
    public function setCreator(?Member $creator = null): ProductStock
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

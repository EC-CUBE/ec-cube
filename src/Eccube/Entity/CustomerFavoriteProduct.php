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
use Eccube\Repository\CustomerFavoriteProductRepository;

/**
 * CustomerFavoriteProduct
 */
#[ORM\Table(name: 'dtb_customer_favorite_product')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CustomerFavoriteProductRepository::class)]
class CustomerFavoriteProduct extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

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

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'CustomerFavoriteProducts')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    private ?Customer $Customer = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'CustomerFavoriteProducts')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private ?Product $Product = null;

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): CustomerFavoriteProduct
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
    public function setUpdateDate(\DateTime $updateDate): CustomerFavoriteProduct
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
     * Set customer.
     */
    public function setCustomer(?Customer $customer = null): CustomerFavoriteProduct
    {
        $this->Customer = $customer;

        return $this;
    }

    /**
     * Get customer.
     */
    public function getCustomer(): ?Customer
    {
        return $this->Customer;
    }

    /**
     * Set product.
     */
    public function setProduct(?Product $product = null): CustomerFavoriteProduct
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get product.
     */
    public function getProduct(): ?Product
    {
        return $this->Product;
    }
}

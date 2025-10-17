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
use Eccube\Repository\CustomerFavoriteProductRepository;

if (!class_exists(CustomerFavoriteProduct::class)) {
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
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

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
         * @var Customer|null
         */
        #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'CustomerFavoriteProducts')]
        #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
        private $Customer;

        /**
         * @var Product|null
         */
        #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'CustomerFavoriteProducts')]
        #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
        private $Product;

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
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return CustomerFavoriteProduct
         */
        public function setCreateDate($createDate): CustomerFavoriteProduct
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
         * @return CustomerFavoriteProduct
         */
        public function setUpdateDate($updateDate): CustomerFavoriteProduct
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
         * Set customer.
         *
         * @param Customer|null $customer
         *
         * @return CustomerFavoriteProduct
         */
        public function setCustomer(?Customer $customer = null): CustomerFavoriteProduct
        {
            $this->Customer = $customer;

            return $this;
        }

        /**
         * Get customer.
         *
         * @return Customer|null
         */
        public function getCustomer(): ?Customer
        {
            return $this->Customer;
        }

        /**
         * Set product.
         *
         * @param Product|null $product
         *
         * @return CustomerFavoriteProduct
         */
        public function setProduct(?Product $product = null): CustomerFavoriteProduct
        {
            $this->Product = $product;

            return $this;
        }

        /**
         * Get product.
         *
         * @return Product|null
         */
        public function getProduct(): ?Product
        {
            return $this->Product;
        }
    }
}

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
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Repository\CustomerAddressRepository;

if (!class_exists(CustomerAddress::class)) {
    /**
     * CustomerAddress
     */
    #[ORM\Table(name: 'dtb_customer_address')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CustomerAddressRepository::class)]
    class CustomerAddress extends AbstractEntity
    {
        /**
         * getShippingMultipleDefaultName
         */
        public function getShippingMultipleDefaultName(): string
        {
            return $this->getName01().' '.$this->getPref()->getName().' '.$this->getAddr01().' '.$this->getAddr02();
        }

        /**
         * Set from customer.
         */
        public function setFromCustomer(Customer $Customer): CustomerAddress
        {
            $this
            ->setCustomer($Customer)
            ->setName01($Customer->getName01())
            ->setName02($Customer->getName02())
            ->setKana01($Customer->getKana01())
            ->setKana02($Customer->getKana02())
            ->setCompanyName($Customer->getCompanyName())
            ->setPhoneNumber($Customer->getPhoneNumber())
            ->setPostalCode($Customer->getPostalCode())
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02());

            return $this;
        }

        /**
         * Set from Shipping.
         */
        public function setFromShipping(Shipping $Shipping): CustomerAddress
        {
            $this
            ->setName01($Shipping->getName01())
            ->setName02($Shipping->getName02())
            ->setKana01($Shipping->getKana01())
            ->setKana02($Shipping->getKana02())
            ->setCompanyName($Shipping->getCompanyName())
            ->setPhoneNumber($Shipping->getPhoneNumber())
            ->setPostalCode($Shipping->getPostalCode())
            ->setPref($Shipping->getPref())
            ->setAddr01($Shipping->getAddr01())
            ->setAddr02($Shipping->getAddr02());

            return $this;
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'name01', type: Types::STRING, length: 255)]
        private ?string $name01 = null;

        #[ORM\Column(name: 'name02', type: Types::STRING, length: 255)]
        private ?string $name02 = null;

        #[ORM\Column(name: 'kana01', type: Types::STRING, length: 255, nullable: true)]
        private ?string $kana01 = null;

        #[ORM\Column(name: 'kana02', type: Types::STRING, length: 255, nullable: true)]
        private ?string $kana02 = null;

        #[ORM\Column(name: 'company_name', type: Types::STRING, length: 255, nullable: true)]
        private ?string $company_name = null;

        #[ORM\Column(name: 'postal_code', type: Types::STRING, length: 8, nullable: true)]
        private ?string $postal_code = null;

        #[ORM\Column(name: 'addr01', type: Types::STRING, length: 255, nullable: true)]
        private ?string $addr01 = null;

        #[ORM\Column(name: 'addr02', type: Types::STRING, length: 255, nullable: true)]
        private ?string $addr02 = null;

        #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 14, nullable: true)]
        private ?string $phone_number = null;

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

        #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'CustomerAddresses')]
        #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
        private ?Customer $Customer = null;

        #[ORM\ManyToOne(targetEntity: Country::class)]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private ?Country $Country = null;

        #[ORM\ManyToOne(targetEntity: Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private ?Pref $Pref = null;

        /**
         * Get id.
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set name01.
         */
        public function setName01(?string $name01 = null): CustomerAddress
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         */
        public function getName01(): ?string
        {
            return $this->name01;
        }

        /**
         * Set name02.
         */
        public function setName02(?string $name02 = null): CustomerAddress
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         */
        public function getName02(): ?string
        {
            return $this->name02;
        }

        /**
         * Set kana01.
         */
        public function setKana01(?string $kana01 = null): CustomerAddress
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         */
        public function getKana01(): ?string
        {
            return $this->kana01;
        }

        /**
         * Set kana02.
         */
        public function setKana02(?string $kana02 = null): CustomerAddress
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         */
        public function getKana02(): ?string
        {
            return $this->kana02;
        }

        /**
         * Set companyName.
         */
        public function setCompanyName(?string $companyName = null): CustomerAddress
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         */
        public function getCompanyName(): ?string
        {
            return $this->company_name;
        }

        /**
         * Set postal_code.
         */
        public function setPostalCode(?string $postal_code = null): CustomerAddress
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         */
        public function getPostalCode(): ?string
        {
            return $this->postal_code;
        }

        /**
         * Set addr01.
         */
        public function setAddr01(?string $addr01 = null): CustomerAddress
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         */
        public function getAddr01(): ?string
        {
            return $this->addr01;
        }

        /**
         * Set addr02.
         */
        public function setAddr02(?string $addr02 = null): CustomerAddress
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         */
        public function getAddr02(): ?string
        {
            return $this->addr02;
        }

        /**
         * Set phone_number.
         */
        public function setPhoneNumber(?string $phone_number = null): CustomerAddress
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         */
        public function getPhoneNumber(): ?string
        {
            return $this->phone_number;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): CustomerAddress
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
        public function setUpdateDate(\DateTime $updateDate): CustomerAddress
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
        public function setCustomer(?Customer $customer = null): CustomerAddress
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
         * Set country.
         */
        public function setCountry(?Country $country = null): CustomerAddress
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         */
        public function getCountry(): ?Country
        {
            return $this->Country;
        }

        /**
         * Set pref.
         */
        public function setPref(?Pref $pref = null): CustomerAddress
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         */
        public function getPref(): ?Pref
        {
            return $this->Pref;
        }
    }
}

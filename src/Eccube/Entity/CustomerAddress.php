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
         *
         * @return string
         */
        public function getShippingMultipleDefaultName(): string
        {
            return $this->getName01().' '.$this->getPref()->getName().' '.$this->getAddr01().' '.$this->getAddr02();
        }

        /**
         * Set from customer.
         *
         * @return CustomerAddress
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
         *
         * @return CustomerAddress
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

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name01', type: 'string', length: 255)]
        private $name01;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name02', type: 'string', length: 255)]
        private $name02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'kana01', type: 'string', length: 255, nullable: true)]
        private $kana01;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'kana02', type: 'string', length: 255, nullable: true)]
        private $kana02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'company_name', type: 'string', length: 255, nullable: true)]
        private $company_name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'postal_code', type: 'string', length: 8, nullable: true)]
        private $postal_code;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'addr01', type: 'string', length: 255, nullable: true)]
        private $addr01;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'addr02', type: 'string', length: 255, nullable: true)]
        private $addr02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'phone_number', type: 'string', length: 14, nullable: true)]
        private $phone_number;

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
        #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'CustomerAddresses')]
        #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
        private $Customer;

        /**
         * @var Country|null
         */
        #[ORM\ManyToOne(targetEntity: Country::class)]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private $Country;

        /**
         * @var Pref|null
         */
        #[ORM\ManyToOne(targetEntity: Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private $Pref;

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
         * Set name01.
         *
         * @return CustomerAddress
         */
        public function setName01(?string $name01 = null): CustomerAddress
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         *
         * @return string|null
         */
        public function getName01(): ?string
        {
            return $this->name01;
        }

        /**
         * Set name02.
         *
         * @return CustomerAddress
         */
        public function setName02(?string $name02 = null): CustomerAddress
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         *
         * @return string|null
         */
        public function getName02(): ?string
        {
            return $this->name02;
        }

        /**
         * Set kana01.
         *
         * @return CustomerAddress
         */
        public function setKana01(?string $kana01 = null): CustomerAddress
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         *
         * @return string|null
         */
        public function getKana01(): ?string
        {
            return $this->kana01;
        }

        /**
         * Set kana02.
         *
         * @return CustomerAddress
         */
        public function setKana02(?string $kana02 = null): CustomerAddress
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         *
         * @return string|null
         */
        public function getKana02(): ?string
        {
            return $this->kana02;
        }

        /**
         * Set companyName.
         *
         * @return CustomerAddress
         */
        public function setCompanyName(?string $companyName = null): CustomerAddress
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         *
         * @return string|null
         */
        public function getCompanyName(): ?string
        {
            return $this->company_name;
        }

        /**
         * Set postal_code.
         *
         * @return CustomerAddress
         */
        public function setPostalCode(?string $postal_code = null): CustomerAddress
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         *
         * @return string|null
         */
        public function getPostalCode(): ?string
        {
            return $this->postal_code;
        }

        /**
         * Set addr01.
         *
         * @return CustomerAddress
         */
        public function setAddr01(?string $addr01 = null): CustomerAddress
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         *
         * @return string|null
         */
        public function getAddr01(): ?string
        {
            return $this->addr01;
        }

        /**
         * Set addr02.
         *
         * @return CustomerAddress
         */
        public function setAddr02(?string $addr02 = null): CustomerAddress
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         *
         * @return string|null
         */
        public function getAddr02(): ?string
        {
            return $this->addr02;
        }

        /**
         * Set phone_number.
         *
         * @return CustomerAddress
         */
        public function setPhoneNumber(?string $phone_number = null): CustomerAddress
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         *
         * @return string|null
         */
        public function getPhoneNumber(): ?string
        {
            return $this->phone_number;
        }

        /**
         * Set createDate.
         *
         * @return CustomerAddress
         */
        public function setCreateDate(\DateTime $createDate): CustomerAddress
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
         * @return CustomerAddress
         */
        public function setUpdateDate(\DateTime $updateDate): CustomerAddress
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
         * @return CustomerAddress
         */
        public function setCustomer(?Customer $customer = null): CustomerAddress
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
         * Set country.
         *
         * @return CustomerAddress
         */
        public function setCountry(?Country $country = null): CustomerAddress
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return Country|null
         */
        public function getCountry(): ?Country
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @return CustomerAddress
         */
        public function setPref(?Pref $pref = null): CustomerAddress
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return Pref|null
         */
        public function getPref(): ?Pref
        {
            return $this->Pref;
        }
    }
}

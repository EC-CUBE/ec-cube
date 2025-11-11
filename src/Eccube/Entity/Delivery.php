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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\SaleType;
use Eccube\Repository\DeliveryRepository;

if (!class_exists(Delivery::class)) {
    /**
     * Delivery
     */
    #[ORM\Table(name: 'dtb_delivery')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: DeliveryRepository::class)]
    class Delivery extends AbstractEntity implements \Stringable
    {
        #[\Override]
        public function __toString(): string
        {
            return (string) $this->name;
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private ?int $id = null;

        #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: true)]
        private ?string $name = null;

        #[ORM\Column(name: 'service_name', type: Types::STRING, length: 255, nullable: true)]
        private ?string $service_name = null;

        #[ORM\Column(name: 'description', type: Types::STRING, length: 4000, nullable: true)]
        private ?string $description = null;

        #[ORM\Column(name: 'confirm_url', type: Types::STRING, length: 4000, nullable: true)]
        private ?string $confirm_url = null;

        #[ORM\Column(name: 'sort_no', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
        private ?int $sort_no = null;

        #[ORM\Column(name: 'visible', type: Types::BOOLEAN, options: ['default' => true])]
        private bool $visible = true;

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

        /**
         * @var Collection<int, PaymentOption>
         */
        #[ORM\OneToMany(targetEntity: PaymentOption::class, mappedBy: 'Delivery', cascade: ['persist', 'remove'])]
        private $PaymentOptions;

        /**
         * @var Collection<int, DeliveryFee>
         */
        #[ORM\OneToMany(targetEntity: DeliveryFee::class, mappedBy: 'Delivery', cascade: ['persist', 'remove'])]
        private $DeliveryFees;

        /**
         * @var Collection<int, DeliveryTime>
         */
        #[ORM\OneToMany(targetEntity: DeliveryTime::class, mappedBy: 'Delivery', cascade: ['persist', 'remove'])]
        #[ORM\OrderBy(['sort_no' => 'ASC'])]
        private $DeliveryTimes;

        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private ?Member $Creator = null;

        #[ORM\ManyToOne(targetEntity: SaleType::class)]
        #[ORM\JoinColumn(name: 'sale_type_id', referencedColumnName: 'id')]
        private ?SaleType $SaleType = null;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->PaymentOptions = new ArrayCollection();
            $this->DeliveryFees = new ArrayCollection();
            $this->DeliveryTimes = new ArrayCollection();
        }

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
        public function setName(?string $name = null): Delivery
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
         * Set serviceName.
         */
        public function setServiceName(?string $serviceName = null): Delivery
        {
            $this->service_name = $serviceName;

            return $this;
        }

        /**
         * Get serviceName.
         */
        public function getServiceName(): ?string
        {
            return $this->service_name;
        }

        /**
         * Set description.
         */
        public function setDescription(?string $description = null): Delivery
        {
            $this->description = $description;

            return $this;
        }

        /**
         * Get description.
         */
        public function getDescription(): ?string
        {
            return $this->description;
        }

        /**
         * Set confirmUrl.
         */
        public function setConfirmUrl(?string $confirmUrl = null): Delivery
        {
            $this->confirm_url = $confirmUrl;

            return $this;
        }

        /**
         * Get confirmUrl.
         */
        public function getConfirmUrl(): ?string
        {
            return $this->confirm_url;
        }

        /**
         * Set sortNo.
         */
        public function setSortNo(?int $sortNo = null): Delivery
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         */
        public function getSortNo(): ?int
        {
            return $this->sort_no;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Delivery
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
        public function setUpdateDate(\DateTime $updateDate): Delivery
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
         * Add paymentOption.
         */
        public function addPaymentOption(PaymentOption $paymentOption): Delivery
        {
            $this->PaymentOptions[] = $paymentOption;

            return $this;
        }

        /**
         * Remove paymentOption.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removePaymentOption(PaymentOption $paymentOption): bool
        {
            return $this->PaymentOptions->removeElement($paymentOption);
        }

        /**
         * Get paymentOptions.
         *
         * @return Collection<int, PaymentOption>
         */
        public function getPaymentOptions(): Collection
        {
            return $this->PaymentOptions;
        }

        /**
         * Add deliveryFee.
         */
        public function addDeliveryFee(DeliveryFee $deliveryFee): Delivery
        {
            $this->DeliveryFees[] = $deliveryFee;

            return $this;
        }

        /**
         * Remove deliveryFee.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeDeliveryFee(DeliveryFee $deliveryFee): bool
        {
            return $this->DeliveryFees->removeElement($deliveryFee);
        }

        /**
         * Get deliveryFees.
         *
         * @return Collection<int, DeliveryFee>
         */
        public function getDeliveryFees(): Collection
        {
            return $this->DeliveryFees;
        }

        /**
         * Add deliveryTime.
         */
        public function addDeliveryTime(DeliveryTime $deliveryTime): Delivery
        {
            $this->DeliveryTimes[] = $deliveryTime;

            return $this;
        }

        /**
         * Remove deliveryTime.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeDeliveryTime(DeliveryTime $deliveryTime): bool
        {
            return $this->DeliveryTimes->removeElement($deliveryTime);
        }

        /**
         * Get deliveryTimes.
         *
         * @return Collection<int, DeliveryTime>
         */
        public function getDeliveryTimes(): Collection
        {
            return $this->DeliveryTimes;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): Delivery
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
         * Set saleType.
         */
        public function setSaleType(?SaleType $saleType = null): Delivery
        {
            $this->SaleType = $saleType;

            return $this;
        }

        /**
         * Get saleType.
         */
        public function getSaleType(): ?SaleType
        {
            return $this->SaleType;
        }

        /**
         * Set visible
         */
        public function setVisible(bool $visible): Delivery
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
}

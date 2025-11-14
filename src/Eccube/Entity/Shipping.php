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
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Repository\ShippingRepository;
use Eccube\Service\Calculator\OrderItemCollection;
use Eccube\Service\PurchaseFlow\ItemCollection;

if (!class_exists(Shipping::class)) {
    /**
     * Shipping
     */
    #[ORM\Table(name: 'dtb_shipping')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: ShippingRepository::class)]
    class Shipping extends AbstractEntity
    {
        use NameTrait;

        /**
         * 出荷メール未送信
         */
        public const SHIPPING_MAIL_UNSENT = 1;
        /**
         * 出荷メール送信済
         */
        public const SHIPPING_MAIL_SENT = 2;

        public function getShippingMultipleDefaultName(): string
        {
            return $this->getName01().' '.$this->getPref()->getName().' '.$this->getAddr01().' '.$this->getAddr02();
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
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

        #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 14, nullable: true)]
        private ?string $phone_number = null;

        #[ORM\Column(name: 'postal_code', type: Types::STRING, length: 8, nullable: true)]
        private ?string $postal_code = null;

        #[ORM\Column(name: 'addr01', type: Types::STRING, length: 255, nullable: true)]
        private ?string $addr01 = null;

        #[ORM\Column(name: 'addr02', type: Types::STRING, length: 255, nullable: true)]
        private ?string $addr02 = null;

        #[ORM\Column(name: 'delivery_name', type: Types::STRING, length: 255, nullable: true)]
        private ?string $shipping_delivery_name = null;

        #[ORM\Column(name: 'time_id', type: Types::INTEGER, options: ['unsigned' => true], nullable: true)]
        private ?int $time_id = null;

        #[ORM\Column(name: 'delivery_time', type: Types::STRING, length: 255, nullable: true)]
        private ?string $shipping_delivery_time = null;

        /**
         * お届け予定日/お届け希望日
         *
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'delivery_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private $shipping_delivery_date;

        /**
         * 出荷日
         *
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'shipping_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private $shipping_date;

        #[ORM\Column(name: 'tracking_number', type: Types::STRING, length: 255, nullable: true)]
        private ?string $tracking_number = null;

        #[ORM\Column(name: 'note', type: Types::STRING, length: 4000, nullable: true)]
        private ?string $note = null;

        #[ORM\Column(name: 'sort_no', type: Types::SMALLINT, nullable: true, options: ['unsigned' => true])]
        private ?int $sort_no = null;

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
         * @var \DateTime
         */
        #[ORM\Column(name: 'mail_send_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
        private $mail_send_date;

        #[ORM\ManyToOne(targetEntity: Order::class, cascade: ['persist'], inversedBy: 'Shippings')]
        #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
        private ?Order $Order = null;

        /**
         * @var Collection<int, OrderItem>
         */
        #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'Shipping', cascade: ['persist'])]
        private $OrderItems;

        #[ORM\ManyToOne(targetEntity: Country::class)]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private ?Country $Country = null;

        #[ORM\ManyToOne(targetEntity: Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private ?Pref $Pref = null;

        #[ORM\ManyToOne(targetEntity: Delivery::class)]
        #[ORM\JoinColumn(name: 'delivery_id', referencedColumnName: 'id')]
        private ?Delivery $Delivery = null;

        private ProductClass $ProductClassOfTemp;

        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private ?Member $Creator = null;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->OrderItems = new ArrayCollection();
        }

        /**
         * CustomerAddress から個人情報を設定.
         */
        public function setFromCustomerAddress(CustomerAddress $CustomerAddress): Shipping
        {
            $this
            ->setName01($CustomerAddress->getName01())
            ->setName02($CustomerAddress->getName02())
            ->setKana01($CustomerAddress->getKana01())
            ->setKana02($CustomerAddress->getKana02())
            ->setCompanyName($CustomerAddress->getCompanyName())
            ->setPhoneNumber($CustomerAddress->getPhonenumber())
            ->setPostalCode($CustomerAddress->getPostalCode())
            ->setPref($CustomerAddress->getPref())
            ->setAddr01($CustomerAddress->getAddr01())
            ->setAddr02($CustomerAddress->getAddr02());

            return $this;
        }

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
         * Set name01.
         */
        public function setName01(?string $name01): Shipping
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         */
        public function getName01(): string
        {
            return $this->name01;
        }

        /**
         * Set name02.
         */
        public function setName02(?string $name02): Shipping
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         */
        public function getName02(): string
        {
            return $this->name02;
        }

        /**
         * Set kana01.
         */
        public function setKana01(string $kana01): Shipping
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         */
        public function getKana01(): string
        {
            return $this->kana01;
        }

        /**
         * Set kana02.
         */
        public function setKana02(string $kana02): Shipping
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         */
        public function getKana02(): string
        {
            return $this->kana02;
        }

        /**
         * Set companyName.
         */
        public function setCompanyName(?string $companyName = null): Shipping
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
         * Set phone_number.
         */
        public function setPhoneNumber(?string $phone_number = null): Shipping
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
         * Set postal_code.
         */
        public function setPostalCode(?string $postal_code = null): Shipping
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
        public function setAddr01(?string $addr01 = null): Shipping
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
        public function setAddr02(?string $addr02 = null): Shipping
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
         * Set shippingDeliveryName.
         */
        public function setShippingDeliveryName(?string $shippingDeliveryName = null): Shipping
        {
            $this->shipping_delivery_name = $shippingDeliveryName;

            return $this;
        }

        /**
         * Get shippingDeliveryName.
         */
        public function getShippingDeliveryName(): ?string
        {
            return $this->shipping_delivery_name;
        }

        /**
         * Set shippingDeliveryTime.
         */
        public function setShippingDeliveryTime(?string $shippingDeliveryTime = null): Shipping
        {
            $this->shipping_delivery_time = $shippingDeliveryTime;

            return $this;
        }

        /**
         * Get shippingDeliveryTime.
         */
        public function getShippingDeliveryTime(): ?string
        {
            return $this->shipping_delivery_time;
        }

        /**
         * Set shippingDeliveryDate.
         */
        public function setShippingDeliveryDate(?\DateTime $shippingDeliveryDate = null): Shipping
        {
            $this->shipping_delivery_date = $shippingDeliveryDate;

            return $this;
        }

        /**
         * Get shippingDeliveryDate.
         */
        public function getShippingDeliveryDate(): ?\DateTime
        {
            return $this->shipping_delivery_date;
        }

        /**
         * Set shippingDate.
         */
        public function setShippingDate(?\DateTime $shippingDate = null): Shipping
        {
            $this->shipping_date = $shippingDate;

            return $this;
        }

        /**
         * Get shippingDate.
         */
        public function getShippingDate(): ?\DateTime
        {
            return $this->shipping_date;
        }

        /**
         * Set sortNo.
         */
        public function setSortNo(?int $sortNo = null): Shipping
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
        public function setCreateDate(\DateTime $createDate): Shipping
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
        public function setUpdateDate(\DateTime $updateDate): Shipping
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
         * Set mailSendDate.
         */
        public function setMailSendDate(?\DateTime $mailSendDate): Shipping
        {
            $this->mail_send_date = $mailSendDate;

            return $this;
        }

        /**
         * Get mailSendDate.
         */
        public function getMailSendDate(): ?\DateTime
        {
            return $this->mail_send_date;
        }

        /**
         * Add orderItem.
         */
        public function addOrderItem(OrderItem $OrderItem): Shipping
        {
            $this->OrderItems[] = $OrderItem;

            return $this;
        }

        /**
         * Remove orderItem.
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeOrderItem(OrderItem $OrderItem): bool
        {
            return $this->OrderItems->removeElement($OrderItem);
        }

        /**
         * Get orderItems.
         */
        public function getOrderItems(): ItemCollection
        {
            return (new ItemCollection($this->OrderItems))->sort();
        }

        /**
         * 商品の受注明細を取得
         *
         * @return OrderItem[]
         */
        public function getProductOrderItems(): array
        {
            $sio = new OrderItemCollection($this->OrderItems->toArray());

            return $sio->getProductClasses()->toArray();
        }

        /**
         * Set country.
         */
        public function setCountry(?Country $country = null): Shipping
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
        public function setPref(?Pref $pref = null): Shipping
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

        /**
         * Set delivery.
         */
        public function setDelivery(?Delivery $delivery = null): Shipping
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
         * Product class of shipment item (temp)
         */
        public function getProductClassOfTemp(): ProductClass
        {
            return $this->ProductClassOfTemp;
        }

        /**
         * Product class of shipment item (temp)
         *
         * @return $this
         */
        public function setProductClassOfTemp(ProductClass $ProductClassOfTemp): static
        {
            $this->ProductClassOfTemp = $ProductClassOfTemp;

            return $this;
        }

        /**
         * Set order.
         *
         * @return $this
         */
        public function setOrder(Order $Order): static
        {
            $this->Order = $Order;

            return $this;
        }

        /**
         * Get order.
         */
        public function getOrder(): Order
        {
            return $this->Order;
        }

        /**
         * Set trackingNumber
         */
        public function setTrackingNumber(string $trackingNumber): Shipping
        {
            $this->tracking_number = $trackingNumber;

            return $this;
        }

        /**
         * Get trackingNumber
         */
        public function getTrackingNumber(): ?string
        {
            return $this->tracking_number;
        }

        /**
         * Set note.
         */
        public function setNote(?string $note = null): Shipping
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         */
        public function getNote(): ?string
        {
            return $this->note;
        }

        /**
         * 出荷済みの場合はtrue, 未出荷の場合はfalseを返す
         */
        public function isShipped(): bool
        {
            return !is_null($this->shipping_date);
        }

        /**
         * Set timeId
         */
        public function setTimeId(?int $timeId): Shipping
        {
            $this->time_id = $timeId;

            return $this;
        }

        /**
         * Get timeId
         */
        public function getTimeId(): ?int
        {
            return $this->time_id;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): Shipping
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
}

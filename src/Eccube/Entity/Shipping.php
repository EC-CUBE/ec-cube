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
use Eccube\Service\Calculator\OrderItemCollection;
use Eccube\Service\PurchaseFlow\ItemCollection;

if (!class_exists('\Eccube\Entity\Shipping')) {
    /**
     * Shipping
     *
     * @ORM\Table(name="dtb_shipping")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\ShippingRepository")
     */
    class Shipping extends \Eccube\Entity\AbstractEntity
    {
        use NameTrait;

        /**
         * 出荷メール未送信
         */
        const SHIPPING_MAIL_UNSENT = 1;
        /**
         * 出荷メール送信済
         */
        const SHIPPING_MAIL_SENT = 2;

        public function getShippingMultipleDefaultName()
        {
            return $this->getName01().' '.$this->getPref()->getName().' '.$this->getAddr01().' '.$this->getAddr02();
        }

        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="name01", type="string", length=255)
         */
        private $name01;

        /**
         * @var string
         *
         * @ORM\Column(name="name02", type="string", length=255)
         */
        private $name02;

        /**
         * @var string
         *
         * @ORM\Column(name="kana01", type="string", length=255, nullable=true)
         */
        private $kana01;

        /**
         * @var string
         *
         * @ORM\Column(name="kana02", type="string", length=255, nullable=true)
         */
        private $kana02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
         */
        private $company_name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="phone_number", type="string", length=14, nullable=true)
         */
        private $phone_number;

        /**
         * @var string|null
         *
         * @ORM\Column(name="postal_code", type="string", length=8, nullable=true)
         */
        private $postal_code;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr01", type="string", length=255, nullable=true)
         */
        private $addr01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr02", type="string", length=255, nullable=true)
         */
        private $addr02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="delivery_name", type="string", length=255, nullable=true)
         */
        private $shipping_delivery_name;

        /**
         * @var int
         *
         * @ORM\Column(name="time_id", type="integer", options={"unsigned":true}, nullable=true)
         */
        private $time_id;

        /**
         * @var string|null
         *
         * @ORM\Column(name="delivery_time", type="string", length=255, nullable=true)
         */
        private $shipping_delivery_time;

        /**
         * お届け予定日/お届け希望日
         *
         * @var \DateTime|null
         *
         * @ORM\Column(name="delivery_date", type="datetimetz", nullable=true)
         */
        private $shipping_delivery_date;

        /**
         * 出荷日
         *
         * @var \DateTime|null
         *
         * @ORM\Column(name="shipping_date", type="datetimetz", nullable=true)
         */
        private $shipping_date;

        /**
         * @var string
         *
         * @ORM\Column(name="tracking_number", type="string", length=255, nullable=true)
         */
        private $tracking_number;

        /**
         * @var string
         *
         * @ORM\Column(name="note", type="string", length=4000, nullable=true)
         */
        private $note;

        /**
         * @var int|null
         *
         * @ORM\Column(name="sort_no", type="smallint", nullable=true, options={"unsigned":true})
         */
        private $sort_no;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="mail_send_date", type="datetimetz", nullable=true)
         */
        private $mail_send_date;

        /**
         * @var \Eccube\Entity\Order
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Order", inversedBy="Shippings", cascade={"persist"})
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
         * })
         */
        private $Order;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\OrderItem", mappedBy="Shipping", cascade={"persist"})
         */
        private $OrderItems;

        /**
         * @var \Eccube\Entity\Master\Country
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Country")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
         * })
         */
        private $Country;

        /**
         * @var \Eccube\Entity\Master\Pref
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="pref_id", referencedColumnName="id")
         * })
         */
        private $Pref;

        /**
         * @var \Eccube\Entity\Delivery
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Delivery")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="delivery_id", referencedColumnName="id")
         * })
         */
        private $Delivery;

        /**
         * @var \Eccube\Entity\ProductClass
         */
        private $ProductClassOfTemp;

        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
         * })
         */
        private $Creator;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->OrderItems = new \Doctrine\Common\Collections\ArrayCollection();
        }

        /**
         * CustomerAddress から個人情報を設定.
         *
         * @param \Eccube\Entity\CustomerAddress $CustomerAddress
         *
         * @return \Eccube\Entity\Shipping
         */
        public function setFromCustomerAddress(CustomerAddress $CustomerAddress)
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
         * 個人情報をクリア.
         *
         * @return \Eccube\Entity\Shipping
         */
        public function clearCustomerAddress()
        {
            $this
            ->setName01(null)
            ->setName02(null)
            ->setKana01(null)
            ->setKana02(null)
            ->setCompanyName(null)
            ->setPhoneNumber(null)
            ->setPostalCode(null)
            ->setPref(null)
            ->setAddr01(null)
            ->setAddr02(null);

            return $this;
        }

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set name01.
         *
         * @param string $name01
         *
         * @return Shipping
         */
        public function setName01($name01)
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         *
         * @return string
         */
        public function getName01()
        {
            return $this->name01;
        }

        /**
         * Set name02.
         *
         * @param string $name02
         *
         * @return Shipping
         */
        public function setName02($name02)
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         *
         * @return string
         */
        public function getName02()
        {
            return $this->name02;
        }

        /**
         * Set kana01.
         *
         * @param string $kana01
         *
         * @return Shipping
         */
        public function setKana01($kana01)
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         *
         * @return string
         */
        public function getKana01()
        {
            return $this->kana01;
        }

        /**
         * Set kana02.
         *
         * @param string $kana02
         *
         * @return Shipping
         */
        public function setKana02($kana02)
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         *
         * @return string
         */
        public function getKana02()
        {
            return $this->kana02;
        }

        /**
         * Set companyName.
         *
         * @param string|null $companyName
         *
         * @return Shipping
         */
        public function setCompanyName($companyName = null)
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         *
         * @return string|null
         */
        public function getCompanyName()
        {
            return $this->company_name;
        }

        /**
         * Set phone_number.
         *
         * @param string|null $phone_number
         *
         * @return Shipping
         */
        public function setPhoneNumber($phone_number = null)
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         *
         * @return string|null
         */
        public function getPhoneNumber()
        {
            return $this->phone_number;
        }

        /**
         * Set postal_code.
         *
         * @param string|null $postal_code
         *
         * @return Shipping
         */
        public function setPostalCode($postal_code = null)
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         *
         * @return string|null
         */
        public function getPostalCode()
        {
            return $this->postal_code;
        }

        /**
         * Set addr01.
         *
         * @param string|null $addr01
         *
         * @return Shipping
         */
        public function setAddr01($addr01 = null)
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         *
         * @return string|null
         */
        public function getAddr01()
        {
            return $this->addr01;
        }

        /**
         * Set addr02.
         *
         * @param string|null $addr02
         *
         * @return Shipping
         */
        public function setAddr02($addr02 = null)
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         *
         * @return string|null
         */
        public function getAddr02()
        {
            return $this->addr02;
        }

        /**
         * Set shippingDeliveryName.
         *
         * @param string|null $shippingDeliveryName
         *
         * @return Shipping
         */
        public function setShippingDeliveryName($shippingDeliveryName = null)
        {
            $this->shipping_delivery_name = $shippingDeliveryName;

            return $this;
        }

        /**
         * Get shippingDeliveryName.
         *
         * @return string|null
         */
        public function getShippingDeliveryName()
        {
            return $this->shipping_delivery_name;
        }

        /**
         * Set shippingDeliveryTime.
         *
         * @param string|null $shippingDeliveryTime
         *
         * @return Shipping
         */
        public function setShippingDeliveryTime($shippingDeliveryTime = null)
        {
            $this->shipping_delivery_time = $shippingDeliveryTime;

            return $this;
        }

        /**
         * Get shippingDeliveryTime.
         *
         * @return string|null
         */
        public function getShippingDeliveryTime()
        {
            return $this->shipping_delivery_time;
        }

        /**
         * Set shippingDeliveryDate.
         *
         * @param \DateTime|null $shippingDeliveryDate
         *
         * @return Shipping
         */
        public function setShippingDeliveryDate($shippingDeliveryDate = null)
        {
            $this->shipping_delivery_date = $shippingDeliveryDate;

            return $this;
        }

        /**
         * Get shippingDeliveryDate.
         *
         * @return \DateTime|null
         */
        public function getShippingDeliveryDate()
        {
            return $this->shipping_delivery_date;
        }

        /**
         * Set shippingDate.
         *
         * @param \DateTime|null $shippingDate
         *
         * @return Shipping
         */
        public function setShippingDate($shippingDate = null)
        {
            $this->shipping_date = $shippingDate;

            return $this;
        }

        /**
         * Get shippingDate.
         *
         * @return \DateTime|null
         */
        public function getShippingDate()
        {
            return $this->shipping_date;
        }

        /**
         * Set sortNo.
         *
         * @param int|null $sortNo
         *
         * @return Shipping
         */
        public function setSortNo($sortNo = null)
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int|null
         */
        public function getSortNo()
        {
            return $this->sort_no;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Shipping
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return Shipping
         */
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

        /**
         * Set mailSendDate.
         *
         * @param \DateTime $mailSendDate
         *
         * @return Shipping
         */
        public function setMailSendDate($mailSendDate)
        {
            $this->mail_send_date = $mailSendDate;

            return $this;
        }

        /**
         * Get mailSendDate.
         *
         * @return \DateTime
         */
        public function getMailSendDate()
        {
            return $this->mail_send_date;
        }

        /**
         * Add orderItem.
         *
         * @param \Eccube\Entity\OrderItem $OrderItem
         *
         * @return Shipping
         */
        public function addOrderItem(OrderItem $OrderItem)
        {
            $this->OrderItems[] = $OrderItem;

            return $this;
        }

        /**
         * Remove orderItem.
         *
         * @param \Eccube\Entity\OrderItem $OrderItem
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeOrderItem(OrderItem $OrderItem)
        {
            return $this->OrderItems->removeElement($OrderItem);
        }

        /**
         * Get orderItems.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getOrderItems()
        {
            return (new ItemCollection($this->OrderItems))->sort();
        }

        /**
         * 商品の受注明細を取得
         *
         * @return OrderItem[]
         */
        public function getProductOrderItems()
        {
            $sio = new OrderItemCollection($this->OrderItems->toArray());

            return $sio->getProductClasses()->toArray();
        }

        /**
         * Set country.
         *
         * @param \Eccube\Entity\Master\Country|null $country
         *
         * @return Shipping
         */
        public function setCountry(Master\Country $country = null)
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return \Eccube\Entity\Master\Country|null
         */
        public function getCountry()
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @param \Eccube\Entity\Master\Pref|null $pref
         *
         * @return Shipping
         */
        public function setPref(Master\Pref $pref = null)
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return \Eccube\Entity\Master\Pref|null
         */
        public function getPref()
        {
            return $this->Pref;
        }

        /**
         * Set delivery.
         *
         * @param \Eccube\Entity\Delivery|null $delivery
         *
         * @return Shipping
         */
        public function setDelivery(Delivery $delivery = null)
        {
            $this->Delivery = $delivery;

            return $this;
        }

        /**
         * Get delivery.
         *
         * @return \Eccube\Entity\Delivery|null
         */
        public function getDelivery()
        {
            return $this->Delivery;
        }

        /**
         * Product class of shipment item (temp)
         *
         * @return \Eccube\Entity\ProductClass
         */
        public function getProductClassOfTemp()
        {
            return $this->ProductClassOfTemp;
        }

        /**
         * Product class of shipment item (temp)
         *
         * @param \Eccube\Entity\ProductClass $ProductClassOfTemp
         *
         * @return $this
         */
        public function setProductClassOfTemp(ProductClass $ProductClassOfTemp)
        {
            $this->ProductClassOfTemp = $ProductClassOfTemp;

            return $this;
        }

        /**
         * Set order.
         *
         * @param Order $Order
         *
         * @return $this
         */
        public function setOrder(Order $Order)
        {
            $this->Order = $Order;

            return $this;
        }

        /**
         * Get order.
         *
         * @return Order
         */
        public function getOrder()
        {
            return $this->Order;
        }

        /**
         * Set trackingNumber
         *
         * @param string $trackingNumber
         *
         * @return Shipping
         */
        public function setTrackingNumber($trackingNumber)
        {
            $this->tracking_number = $trackingNumber;

            return $this;
        }

        /**
         * Get trackingNumber
         *
         * @return string
         */
        public function getTrackingNumber()
        {
            return $this->tracking_number;
        }

        /**
         * Set note.
         *
         * @param string|null $note
         *
         * @return Shipping
         */
        public function setNote($note = null)
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         *
         * @return string|null
         */
        public function getNote()
        {
            return $this->note;
        }

        /**
         * 出荷済みの場合はtrue, 未出荷の場合はfalseを返す
         *
         * @return boolean
         */
        public function isShipped()
        {
            return !is_null($this->shipping_date);
        }

        /**
         * Set timeId
         *
         * @param integer $timeId
         *
         * @return Shipping
         */
        public function setTimeId($timeId)
        {
            $this->time_id = $timeId;

            return $this;
        }

        /**
         * Get timeId
         *
         * @return integer
         */
        public function getTimeId()
        {
            return $this->time_id;
        }

        /**
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return Shipping
         */
        public function setCreator(Member $creator = null)
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return \Eccube\Entity\Member|null
         */
        public function getCreator()
        {
            return $this->Creator;
        }
    }
}

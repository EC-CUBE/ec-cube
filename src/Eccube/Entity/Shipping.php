<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Entity;

use Eccube\Common\Constant;
use Eccube\Util\EntityUtil;
use Doctrine\ORM\Mapping as ORM;

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
    /**
     * @var int
     *
     * @ORM\Column(name="shipping_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_name01", type="string", length=255)
     */
    private $name01;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_name02", type="string", length=255)
     */
    private $name02;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_kana01", type="string", length=255)
     */
    private $kana01;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_kana02", type="string", length=255)
     */
    private $kana02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_company_name", type="string", length=255, nullable=true)
     */
    private $company_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_tel01", type="string", length=5, nullable=true)
     */
    private $tel01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_tel02", type="string", length=4, nullable=true)
     */
    private $tel02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_tel03", type="string", length=4, nullable=true)
     */
    private $tel03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_fax01", type="string", length=5, nullable=true)
     */
    private $fax01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_fax02", type="string", length=4, nullable=true)
     */
    private $fax02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_fax03", type="string", length=4, nullable=true)
     */
    private $fax03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_zip01", type="string", length=3, nullable=true)
     */
    private $zip01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_zip02", type="string", length=4, nullable=true)
     */
    private $zip02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_zipcode", type="string", length=7, nullable=true)
     */
    private $zipcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_addr01", type="string", length=255, nullable=true)
     */
    private $addr01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_addr02", type="string", length=255, nullable=true)
     */
    private $addr02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_delivery_name", type="string", length=255, nullable=true)
     */
    private $shipping_delivery_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_delivery_time", type="string", length=255, nullable=true)
     */
    private $shipping_delivery_time;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="shipping_delivery_date", type="datetime", nullable=true)
     */
    private $shipping_delivery_date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shipping_delivery_fee", type="decimal", precision=10, scale=0, nullable=true, options={"unsigned":true,"default":0})
     */
    private $shipping_delivery_fee = 0;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="shipping_commit_date", type="datetime", nullable=true)
     */
    private $shipping_commit_date;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_number", type="string", length=255, nullable=true)
     */
    private $tracking_number;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rank", type="smallint", nullable=true, options={"unsigned":true})
     */
    private $rank;

    /**
     * @var int
     *
     * @ORM\Column(name="del_flg", type="smallint", options={"unsigned":true,"default":0})
     */
    private $del_flg = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime")
     */
    private $update_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\ShipmentItem", mappedBy="Shipping", cascade={"persist","remove"})
     */
    private $ShipmentItems;

    /**
     * @var \Eccube\Entity\Master\Country
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shipping_country_id", referencedColumnName="id")
     * })
     */
    private $Country;

    /**
     * @var \Eccube\Entity\Master\Pref
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shipping_pref", referencedColumnName="id")
     * })
     */
    private $Pref;

    /**
     * @var \Eccube\Entity\Delivery
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Delivery")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="delivery_id", referencedColumnName="delivery_id")
     * })
     */
    private $Delivery;

    /**
     * @var \Eccube\Entity\DeliveryTime
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\DeliveryTime")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="time_id", referencedColumnName="time_id")
     * })
     */
    private $DeliveryTime;

    /**
     * @var \Eccube\Entity\DeliveryFee
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\DeliveryFee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fee_id", referencedColumnName="fee_id")
     * })
     */
    private $DeliveryFee;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Orders;

    /**
     * @var \Eccube\Entity\ProductClass
     */
    private $ProductClassOfTemp;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ShipmentItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * CustomerAddress から個人情報を設定.
     *
     * @param \Eccube\Entity\CustomerAddress $CustomerAddress
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
            ->setTel01($CustomerAddress->getTel01())
            ->setTel02($CustomerAddress->getTel02())
            ->setTel03($CustomerAddress->getTel03())
            ->setFax01($CustomerAddress->getFax01())
            ->setFax02($CustomerAddress->getFax02())
            ->setFax03($CustomerAddress->getFax03())
            ->setZip01($CustomerAddress->getZip01())
            ->setZip02($CustomerAddress->getZip02())
            ->setZipCode($CustomerAddress->getZip01() . $CustomerAddress->getZip02())
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
            ->setTel01(null)
            ->setTel02(null)
            ->setTel03(null)
            ->setFax01(null)
            ->setFax02(null)
            ->setFax03(null)
            ->setZip01(null)
            ->setZip02(null)
            ->setZipCode(null)
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
     * Set tel01.
     *
     * @param string|null $tel01
     *
     * @return Shipping
     */
    public function setTel01($tel01 = null)
    {
        $this->tel01 = $tel01;

        return $this;
    }

    /**
     * Get tel01.
     *
     * @return string|null
     */
    public function getTel01()
    {
        return $this->tel01;
    }

    /**
     * Set tel02.
     *
     * @param string|null $tel02
     *
     * @return Shipping
     */
    public function setTel02($tel02 = null)
    {
        $this->tel02 = $tel02;

        return $this;
    }

    /**
     * Get tel02.
     *
     * @return string|null
     */
    public function getTel02()
    {
        return $this->tel02;
    }

    /**
     * Set tel03.
     *
     * @param string|null $tel03
     *
     * @return Shipping
     */
    public function setTel03($tel03 = null)
    {
        $this->tel03 = $tel03;

        return $this;
    }

    /**
     * Get tel03.
     *
     * @return string|null
     */
    public function getTel03()
    {
        return $this->tel03;
    }

    /**
     * Set fax01.
     *
     * @param string|null $fax01
     *
     * @return Shipping
     */
    public function setFax01($fax01 = null)
    {
        $this->fax01 = $fax01;

        return $this;
    }

    /**
     * Get fax01.
     *
     * @return string|null
     */
    public function getFax01()
    {
        return $this->fax01;
    }

    /**
     * Set fax02.
     *
     * @param string|null $fax02
     *
     * @return Shipping
     */
    public function setFax02($fax02 = null)
    {
        $this->fax02 = $fax02;

        return $this;
    }

    /**
     * Get fax02.
     *
     * @return string|null
     */
    public function getFax02()
    {
        return $this->fax02;
    }

    /**
     * Set fax03.
     *
     * @param string|null $fax03
     *
     * @return Shipping
     */
    public function setFax03($fax03 = null)
    {
        $this->fax03 = $fax03;

        return $this;
    }

    /**
     * Get fax03.
     *
     * @return string|null
     */
    public function getFax03()
    {
        return $this->fax03;
    }

    /**
     * Set zip01.
     *
     * @param string|null $zip01
     *
     * @return Shipping
     */
    public function setZip01($zip01 = null)
    {
        $this->zip01 = $zip01;

        return $this;
    }

    /**
     * Get zip01.
     *
     * @return string|null
     */
    public function getZip01()
    {
        return $this->zip01;
    }

    /**
     * Set zip02.
     *
     * @param string|null $zip02
     *
     * @return Shipping
     */
    public function setZip02($zip02 = null)
    {
        $this->zip02 = $zip02;

        return $this;
    }

    /**
     * Get zip02.
     *
     * @return string|null
     */
    public function getZip02()
    {
        return $this->zip02;
    }

    /**
     * Set zipcode.
     *
     * @param string|null $zipcode
     *
     * @return Shipping
     */
    public function setZipcode($zipcode = null)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode.
     *
     * @return string|null
     */
    public function getZipcode()
    {
        return $this->zipcode;
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
     * Set shippingDeliveryFee.
     *
     * @param string|null $shippingDeliveryFee
     *
     * @return Shipping
     */
    public function setShippingDeliveryFee($shippingDeliveryFee = null)
    {
        $this->shipping_delivery_fee = $shippingDeliveryFee;

        return $this;
    }

    /**
     * Get shippingDeliveryFee.
     *
     * @return string|null
     */
    public function getShippingDeliveryFee()
    {
        return $this->shipping_delivery_fee;
    }

    /**
     * Set shippingCommitDate.
     *
     * @param \DateTime|null $shippingCommitDate
     *
     * @return Shipping
     */
    public function setShippingCommitDate($shippingCommitDate = null)
    {
        $this->shipping_commit_date = $shippingCommitDate;

        return $this;
    }

    /**
     * Get shippingCommitDate.
     *
     * @return \DateTime|null
     */
    public function getShippingCommitDate()
    {
        return $this->shipping_commit_date;
    }

    /**
     * Set rank.
     *
     * @param int|null $rank
     *
     * @return Shipping
     */
    public function setRank($rank = null)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank.
     *
     * @return int|null
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set delFlg.
     *
     * @param int $delFlg
     *
     * @return Shipping
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get delFlg.
     *
     * @return int
     */
    public function getDelFlg()
    {
        return $this->del_flg;
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
     * Add shipmentItem.
     *
     * @param \Eccube\Entity\ShipmentItem $shipmentItem
     *
     * @return Shipping
     */
    public function addShipmentItem(\Eccube\Entity\ShipmentItem $shipmentItem)
    {
        $this->ShipmentItems[] = $shipmentItem;

        return $this;
    }

    /**
     * Remove shipmentItem.
     *
     * @param \Eccube\Entity\ShipmentItem $shipmentItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeShipmentItem(\Eccube\Entity\ShipmentItem $shipmentItem)
    {
        return $this->ShipmentItems->removeElement($shipmentItem);
    }

    /**
     * Get shipmentItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipmentItems()
    {
        return $this->ShipmentItems;
    }

    /**
     * Set country.
     *
     * @param \Eccube\Entity\Master\Country|null $country
     *
     * @return Shipping
     */
    public function setCountry(\Eccube\Entity\Master\Country $country = null)
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
    public function setPref(\Eccube\Entity\Master\Pref $pref = null)
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
    public function setDelivery(\Eccube\Entity\Delivery $delivery = null)
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
     * Set deliveryTime.
     *
     * @param \Eccube\Entity\DeliveryTime|null $deliveryTime
     *
     * @return Shipping
     */
    public function setDeliveryTime(\Eccube\Entity\DeliveryTime $deliveryTime = null)
    {
        $this->DeliveryTime = $deliveryTime;

        return $this;
    }

    /**
     * Get deliveryTime.
     *
     * @return \Eccube\Entity\DeliveryTime|null
     */
    public function getDeliveryTime()
    {
        return $this->DeliveryTime;
    }

    /**
     * Set deliveryFee.
     *
     * @param \Eccube\Entity\DeliveryFee|null $deliveryFee
     *
     * @return Shipping
     */
    public function setDeliveryFee(\Eccube\Entity\DeliveryFee $deliveryFee = null)
    {
        $this->DeliveryFee = $deliveryFee;

        return $this;
    }

    /**
     * Get deliveryFee.
     *
     * @return \Eccube\Entity\DeliveryFee|null
     */
    public function getDeliveryFee()
    {
        if (EntityUtil::isEmpty($this->DeliveryFee)) {
            return null;
        }

        return $this->DeliveryFee;
    }

    /**
     * Product class of shipment item (temp)
     * @return \Eccube\Entity\ProductClass
     */
    public function getProductClassOfTemp()
    {
        return $this->ProductClassOfTemp;
    }

    /**
     * Product class of shipment item (temp)
     * @param \Eccube\Entity\ProductClass $ProductClassOfTemp
     * @return $this
     */
    public function setProductClassOfTemp(\Eccube\Entity\ProductClass $ProductClassOfTemp)
    {
        $this->ProductClassOfTemp = $ProductClassOfTemp;

        return $this;
    }

    /**
     * Get orders.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        $Orders = array_map(function ($ShipmentItem) {
                return $ShipmentItem->getOrder();
            },
            $this->getShipmentItems()->toArray()
        );
        return new \Doctrine\Common\Collections\ArrayCollection($Orders);
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
}

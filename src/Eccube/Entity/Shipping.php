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

/**
 * Shipping
 */
class Shipping extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $shipping_id;

    /**
     * @var integer
     */
    private $order_id;

    /**
     * @var string
     */
    private $name01;

    /**
     * @var string
     */
    private $name02;

    /**
     * @var string
     */
    private $kana01;

    /**
     * @var string
     */
    private $kana02;

    /**
     * @var string
     */
    private $company_name;

    /**
     * @var string
     */
    private $tel01;

    /**
     * @var string
     */
    private $tel02;

    /**
     * @var string
     */
    private $tel03;

    /**
     * @var string
     */
    private $fax01;

    /**
     * @var string
     */
    private $fax02;

    /**
     * @var string
     */
    private $fax03;

    /**
     * @var string
     */
    private $zip01;

    /**
     * @var string
     */
    private $zip02;

    /**
     * @var string
     */
    private $zipcode;

    /**
     * @var string
     */
    private $addr01;

    /**
     * @var string
     */
    private $addr02;

    /**
     * @var integer
     */
    private $time_id;

    /**
     * @var string
     */
    private $shipping_time;

    /**
     * @var \DateTime
     */
    private $shipping_date;

    /**
     * @var \DateTime
     */
    private $shipping_commit_date;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ShipmentItems;

    /**
     * @var \Eccube\Entity\Master\Country
     */
    private $Country;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $Pref;

    /**
     * @var \Eccube\Entity\Order
     */
    private $Order;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ShipmentItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set shipping_id
     *
     * @param  integer  $shippingId
     * @return Shipping
     */
    public function setShippingId($shippingId)
    {
        $this->shipping_id = $shippingId;

        return $this;
    }

    /**
     * Get shipping_id
     *
     * @return integer
     */
    public function getShippingId()
    {
        return $this->shipping_id;
    }

    /**
     * Set order_id
     *
     * @param  integer  $orderId
     * @return Shipping
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get order_id
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set name01
     *
     * @param  string   $name01
     * @return Shipping
     */
    public function setName01($name01)
    {
        $this->name01 = $name01;

        return $this;
    }

    /**
     * Get name01
     *
     * @return string
     */
    public function getName01()
    {
        return $this->name01;
    }

    /**
     * Set name02
     *
     * @param  string   $name02
     * @return Shipping
     */
    public function setName02($name02)
    {
        $this->name02 = $name02;

        return $this;
    }

    /**
     * Get name02
     *
     * @return string
     */
    public function getName02()
    {
        return $this->name02;
    }

    /**
     * Set kana01
     *
     * @param  string   $kana01
     * @return Shipping
     */
    public function setKana01($kana01)
    {
        $this->kana01 = $kana01;

        return $this;
    }

    /**
     * Get kana01
     *
     * @return string
     */
    public function getKana01()
    {
        return $this->kana01;
    }

    /**
     * Set kana02
     *
     * @param  string   $kana02
     * @return Shipping
     */
    public function setKana02($kana02)
    {
        $this->kana02 = $kana02;

        return $this;
    }

    /**
     * Get kana02
     *
     * @return string
     */
    public function getKana02()
    {
        return $this->kana02;
    }

    /**
     * Set company_name
     *
     * @param  string   $companyName
     * @return Shipping
     */
    public function setCompanyName($companyName)
    {
        $this->company_name = $companyName;

        return $this;
    }

    /**
     * Get company_name
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->company_name;
    }

    /**
     * Set tel01
     *
     * @param  string   $tel01
     * @return Shipping
     */
    public function setTel01($tel01)
    {
        $this->tel01 = $tel01;

        return $this;
    }

    /**
     * Get tel01
     *
     * @return string
     */
    public function getTel01()
    {
        return $this->tel01;
    }

    /**
     * Set tel02
     *
     * @param  string   $tel02
     * @return Shipping
     */
    public function setTel02($tel02)
    {
        $this->tel02 = $tel02;

        return $this;
    }

    /**
     * Get tel02
     *
     * @return string
     */
    public function getTel02()
    {
        return $this->tel02;
    }

    /**
     * Set tel03
     *
     * @param  string   $tel03
     * @return Shipping
     */
    public function setTel03($tel03)
    {
        $this->tel03 = $tel03;

        return $this;
    }

    /**
     * Get tel03
     *
     * @return string
     */
    public function getTel03()
    {
        return $this->tel03;
    }

    /**
     * Set fax01
     *
     * @param  string   $fax01
     * @return Shipping
     */
    public function setFax01($fax01)
    {
        $this->fax01 = $fax01;

        return $this;
    }

    /**
     * Get fax01
     *
     * @return string
     */
    public function getFax01()
    {
        return $this->fax01;
    }

    /**
     * Set fax02
     *
     * @param  string   $fax02
     * @return Shipping
     */
    public function setFax02($fax02)
    {
        $this->fax02 = $fax02;

        return $this;
    }

    /**
     * Get fax02
     *
     * @return string
     */
    public function getFax02()
    {
        return $this->fax02;
    }

    /**
     * Set fax03
     *
     * @param  string   $fax03
     * @return Shipping
     */
    public function setFax03($fax03)
    {
        $this->fax03 = $fax03;

        return $this;
    }

    /**
     * Get fax03
     *
     * @return string
     */
    public function getFax03()
    {
        return $this->fax03;
    }

    /**
     * Set zip01
     *
     * @param  string   $zip01
     * @return Shipping
     */
    public function setZip01($zip01)
    {
        $this->zip01 = $zip01;

        return $this;
    }

    /**
     * Get zip01
     *
     * @return string
     */
    public function getZip01()
    {
        return $this->zip01;
    }

    /**
     * Set zip02
     *
     * @param  string   $zip02
     * @return Shipping
     */
    public function setZip02($zip02)
    {
        $this->zip02 = $zip02;

        return $this;
    }

    /**
     * Get zip02
     *
     * @return string
     */
    public function getZip02()
    {
        return $this->zip02;
    }

    /**
     * Set zipcode
     *
     * @param  string   $zipcode
     * @return Shipping
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set addr01
     *
     * @param  string   $addr01
     * @return Shipping
     */
    public function setAddr01($addr01)
    {
        $this->addr01 = $addr01;

        return $this;
    }

    /**
     * Get addr01
     *
     * @return string
     */
    public function getAddr01()
    {
        return $this->addr01;
    }

    /**
     * Set addr02
     *
     * @param  string   $addr02
     * @return Shipping
     */
    public function setAddr02($addr02)
    {
        $this->addr02 = $addr02;

        return $this;
    }

    /**
     * Get addr02
     *
     * @return string
     */
    public function getAddr02()
    {
        return $this->addr02;
    }

    /**
     * Set time_id
     *
     * @param  integer  $timeId
     * @return Shipping
     */
    public function setTimeId($timeId)
    {
        $this->time_id = $timeId;

        return $this;
    }

    /**
     * Get time_id
     *
     * @return integer
     */
    public function getTimeId()
    {
        return $this->time_id;
    }

    /**
     * Set shipping_time
     *
     * @param  string   $shippingTime
     * @return Shipping
     */
    public function setShippingTime($shippingTime)
    {
        $this->shipping_time = $shippingTime;

        return $this;
    }

    /**
     * Get shipping_time
     *
     * @return string
     */
    public function getShippingTime()
    {
        return $this->shipping_time;
    }

    /**
     * Set shipping_date
     *
     * @param  \DateTime $shippingDate
     * @return Shipping
     */
    public function setShippingDate($shippingDate)
    {
        $this->shipping_date = $shippingDate;

        return $this;
    }

    /**
     * Get shipping_date
     *
     * @return \DateTime
     */
    public function getShippingDate()
    {
        return $this->shipping_date;
    }

    /**
     * Set shipping_commit_date
     *
     * @param  \DateTime $shippingCommitDate
     * @return Shipping
     */
    public function setShippingCommitDate($shippingCommitDate)
    {
        $this->shipping_commit_date = $shippingCommitDate;

        return $this;
    }

    /**
     * Get shipping_commit_date
     *
     * @return \DateTime
     */
    public function getShippingCommitDate()
    {
        return $this->shipping_commit_date;
    }

    /**
     * Set rank
     *
     * @param  integer  $rank
     * @return Shipping
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Shipping
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param  \DateTime $updateDate
     * @return Shipping
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set del_flg
     *
     * @param  integer  $delFlg
     * @return Shipping
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Add ShipmentItems
     *
     * @param  \Eccube\Entity\ShipmentItem $shipmentItems
     * @return Shipping
     */
    public function addShipmentItem(\Eccube\Entity\ShipmentItem $shipmentItems)
    {
        $this->ShipmentItems[] = $shipmentItems;

        return $this;
    }

    /**
     * Remove ShipmentItems
     *
     * @param \Eccube\Entity\ShipmentItem $shipmentItems
     */
    public function removeShipmentItem(\Eccube\Entity\ShipmentItem $shipmentItems)
    {
        $this->ShipmentItems->removeElement($shipmentItems);
    }

    /**
     * Get ShipmentItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipmentItems()
    {
        return $this->ShipmentItems;
    }

    /**
     * Set Country
     *
     * @param  \Eccube\Entity\Master\Country $country
     * @return Shipping
     */
    public function setCountry(\Eccube\Entity\Master\Country $country = null)
    {
        $this->Country = $country;

        return $this;
    }

    /**
     * Get Country
     *
     * @return \Eccube\Entity\Master\Country
     */
    public function getCountry()
    {
        return $this->Country;
    }

    /**
     * Set Pref
     *
     * @param  \Eccube\Entity\Master\Pref $pref
     * @return Shipping
     */
    public function setPref(\Eccube\Entity\Master\Pref $pref = null)
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get Pref
     *
     * @return \Eccube\Entity\Master\Pref
     */
    public function getPref()
    {
        return $this->Pref;
    }

    /**
     * Set Order
     *
     * @param  \Eccube\Entity\Order $order
     * @return Shipping
     */
    public function setOrder(\Eccube\Entity\Order $order = null)
    {
        $this->Order = $order;

        return $this;
    }

    /**
     * Get Order
     *
     * @return \Eccube\Entity\Order
     */
    public function getOrder()
    {
        return $this->Order;
    }
}

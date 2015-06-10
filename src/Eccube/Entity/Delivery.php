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
 * Delivery
 */
class Delivery extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $service_name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $confirm_url;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Master\ProductType
     */
    private $ProductType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $DeliveryFees;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $DeliveryTimes;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $PaymentOptions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->DeliveryFees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->DeliveryTimes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->PaymentOptions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setProductType(\Eccube\Entity\Master\ProductType $ProductType)
    {
        $this->ProductType = $ProductType;

        return $this;
    }

    public function getProductType()
    {
        return $this->ProductType;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Deliv
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set service_name
     *
     * @param  string $serviceName
     * @return Deliv
     */
    public function setServiceName($serviceName)
    {
        $this->service_name = $serviceName;

        return $this;
    }

    /**
     * Get service_name
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return Delivery
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set confirm_url
     *
     * @param  string $confirmUrl
     * @return Delivery
     */
    public function setConfirmUrl($confirmUrl)
    {
        $this->confirm_url = $confirmUrl;

        return $this;
    }

    /**
     * Get confirm_url
     *
     * @return string
     */
    public function getConfirmUrl()
    {
        return $this->confirm_url;
    }

    /**
     * Set rank
     *
     * @param  integer $rank
     * @return Delivery
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
     * Set del_flg
     *
     * @param  integer $delFlg
     * @return Deliv
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
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Deliv
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
     * @return Deliv
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
     * Add DeliveryFees
     *
     * @param  \Eccube\Entity\DeliveryFee $DeliveryFees
     * @return Delivery
     */
    public function addDeliveryFee(\Eccube\Entity\DeliveryFee $DeliveryFees)
    {
        $this->DeliveryFees[] = $DeliveryFees;

        return $this;
    }

    /**
     * Remove DeliveryFees
     *
     * @param \Eccube\Entity\DeliveryFee $DeliveryFees
     */
    public function removeDeliveryFee(\Eccube\Entity\DeliveryFee $DeliveryFees)
    {
        $this->DeliveryFees->removeElement($DeliveryFees);
    }

    /**
     * Get DeliveryFees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeliveryFees()
    {
        return $this->DeliveryFees;
    }

    /**
     * Add DeliveryTimes
     *
     * @param  \Eccube\Entity\DeliveryTime $DeliveryTimes
     * @return Delivery
     */
    public function addDeliveryTime(\Eccube\Entity\DeliveryTime $DeliveryTimes)
    {
        $this->DeliveryTimes[] = $DeliveryTimes;

        return $this;
    }

    /**
     * Remove DeliveryTimes
     *
     * @param \Eccube\Entity\DeliveryTime $DeliveryTimes
     */
    public function removeDeliveryTime(\Eccube\Entity\DeliveryTime $DeliveryTimes)
    {
        $this->DeliveryTimes->removeElement($DeliveryTimes);
    }

    /**
     * Get DeliveryTimes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeliveryTimes()
    {
        return $this->DeliveryTimes;
    }

    /**
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $Creator
     * @return Delivery
     */
    public function setCreator(\Eccube\Entity\Member $Creator)
    {
        $this->Creator = $Creator;

        return $this;
    }

    /**
     * Get Creator
     *
     * @return \Eccube\Entity\Member
     */
    public function getCreator()
    {
        return $this->Creator;
    }

    /**
     * Add PaymentOptions
     *
     * @param  \Eccube\Entity\PaymentOption $PaymentOption
     * @return Delivery
     */
    public function addPaymentOption(\Eccube\Entity\PaymentOption $PaymentOption)
    {
        $this->PaymentOptions[] = $PaymentOption;

        return $this;
    }

    /**
     * Remove PaymentOptions
     *
     * @param \Eccube\Entity\PaymentOption $PaymentOption
     */
    public function removePaymentOption(\Eccube\Entity\PaymentOption $PaymentOption)
    {
        $this->PaymentOptions->removeElement($PaymentOption);
    }

    /**
     * Get PaymentOptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentOptions()
    {
        return $this->PaymentOptions;
    }

}

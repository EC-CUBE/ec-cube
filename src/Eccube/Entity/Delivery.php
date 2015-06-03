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
 * Deliv
 */
class Deliv extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $product_type_id;

    /**
     * @var \Eccube\Entity\Master\ProductType
     */
    private $ProductType;

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
    private $remark;

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
    private $status;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $DelivFees;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $DelivTimes;

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
        $this->DelivFees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->DelivTimes = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set product_type_id
     *
     * @param  integer $productTypeId
     * @return Deliv
     */
    public function setProductTypeId($productTypeId)
    {
        $this->product_type_id = $productTypeId;

        return $this;
    }

    /**
     * Get product_type_id
     *
     * @return integer
     */
    public function getProductTypeId()
    {
        return $this->product_type_id;
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
     * Set remark
     *
     * @param  string $remark
     * @return Deliv
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set confirm_url
     *
     * @param  string $confirmUrl
     * @return Deliv
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
     * @return Deliv
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
     * Set status
     *
     * @param  integer $status
     * @return Deliv
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
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
     * Add DelivFees
     *
     * @param  \Eccube\Entity\DelivFee $delivFees
     * @return Deliv
     */
    public function addDelivFee(\Eccube\Entity\DelivFee $delivFees)
    {
        $this->DelivFees[] = $delivFees;

        return $this;
    }

    /**
     * Remove DelivFees
     *
     * @param \Eccube\Entity\DelivFee $delivFees
     */
    public function removeDelivFee(\Eccube\Entity\DelivFee $delivFees)
    {
        $this->DelivFees->removeElement($delivFees);
    }

    /**
     * Get DelivFees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDelivFees()
    {
        return $this->DelivFees;
    }

    /**
     * Add DelivTimes
     *
     * @param  \Eccube\Entity\DelivTime $delivTimes
     * @return Deliv
     */
    public function addDelivTime(\Eccube\Entity\DelivTime $delivTimes)
    {
        $this->DelivTimes[] = $delivTimes;

        return $this;
    }

    /**
     * Remove DelivTimes
     *
     * @param \Eccube\Entity\DelivTime $delivTimes
     */
    public function removeDelivTime(\Eccube\Entity\DelivTime $delivTimes)
    {
        $this->DelivTimes->removeElement($delivTimes);
    }

    /**
     * Get DelivTimes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDelivTimes()
    {
        return $this->DelivTimes;
    }

    /**
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $creator
     * @return Deliv
     */
    public function setCreator(\Eccube\Entity\Member $creator)
    {
        $this->Creator = $creator;

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
     * @param  \Eccube\Entity\PaymentOption $paymentOptions
     * @return Deliv
     */
    public function addPaymentOption(\Eccube\Entity\PaymentOption $paymentOptions)
    {
        $this->PaymentOptions[] = $paymentOptions;

        return $this;
    }

    /**
     * Remove PaymentOptions
     *
     * @param \Eccube\Entity\PaymentOption $paymentOptions
     */
    public function removePaymentOption(\Eccube\Entity\PaymentOption $paymentOptions)
    {
        $this->PaymentOptions->removeElement($paymentOptions);
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

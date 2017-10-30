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

use Doctrine\ORM\Mapping as ORM;

/**
 * Delivery
 *
 * @ORM\Table(name="dtb_delivery")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\DeliveryRepository")
 */
class Delivery extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
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
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="service_name", type="string", length=255, nullable=true)
     */
    private $service_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=4000, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="confirm_url", type="string", length=4000, nullable=true)
     */
    private $confirm_url;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rank", type="integer", nullable=true, options={"unsigned":true})
     */
    private $rank;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", options={"unsigned":true,"default":true})
     */
    private $visible = true;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\PaymentOption", mappedBy="Delivery", cascade={"persist","remove"})
     */
    private $PaymentOptions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\DeliveryFee", mappedBy="Delivery", cascade={"persist","remove"})
     */
    private $DeliveryFees;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\DeliveryTime", mappedBy="Delivery", cascade={"persist","remove"})
     */
    private $DeliveryTimes;

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
     * @var \Eccube\Entity\Master\ProductType
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\ProductType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_type_id", referencedColumnName="id")
     * })
     */
    private $ProductType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->PaymentOptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->DeliveryFees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->DeliveryTimes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name.
     *
     * @param string|null $name
     *
     * @return Delivery
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set serviceName.
     *
     * @param string|null $serviceName
     *
     * @return Delivery
     */
    public function setServiceName($serviceName = null)
    {
        $this->service_name = $serviceName;

        return $this;
    }

    /**
     * Get serviceName.
     *
     * @return string|null
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Get serviceName for Admin.
     * @return string
     */
    public function getServiceNameForAdmin()
    {
        if ($this->isVisible()) {
            return $this->getServiceName();
        } else {
            return $this->getServiceName().'(非表示)';
        }
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Delivery
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set confirmUrl.
     *
     * @param string|null $confirmUrl
     *
     * @return Delivery
     */
    public function setConfirmUrl($confirmUrl = null)
    {
        $this->confirm_url = $confirmUrl;

        return $this;
    }

    /**
     * Get confirmUrl.
     *
     * @return string|null
     */
    public function getConfirmUrl()
    {
        return $this->confirm_url;
    }

    /**
     * Set rank.
     *
     * @param int|null $rank
     *
     * @return Delivery
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
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return Delivery
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
     * @return Delivery
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
     * Add paymentOption.
     *
     * @param \Eccube\Entity\PaymentOption $paymentOption
     *
     * @return Delivery
     */
    public function addPaymentOption(\Eccube\Entity\PaymentOption $paymentOption)
    {
        $this->PaymentOptions[] = $paymentOption;

        return $this;
    }

    /**
     * Remove paymentOption.
     *
     * @param \Eccube\Entity\PaymentOption $paymentOption
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePaymentOption(\Eccube\Entity\PaymentOption $paymentOption)
    {
        return $this->PaymentOptions->removeElement($paymentOption);
    }

    /**
     * Get paymentOptions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentOptions()
    {
        return $this->PaymentOptions;
    }

    /**
     * Add deliveryFee.
     *
     * @param \Eccube\Entity\DeliveryFee $deliveryFee
     *
     * @return Delivery
     */
    public function addDeliveryFee(\Eccube\Entity\DeliveryFee $deliveryFee)
    {
        $this->DeliveryFees[] = $deliveryFee;

        return $this;
    }

    /**
     * Remove deliveryFee.
     *
     * @param \Eccube\Entity\DeliveryFee $deliveryFee
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDeliveryFee(\Eccube\Entity\DeliveryFee $deliveryFee)
    {
        return $this->DeliveryFees->removeElement($deliveryFee);
    }

    /**
     * Get deliveryFees.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeliveryFees()
    {
        return $this->DeliveryFees;
    }

    /**
     * Add deliveryTime.
     *
     * @param \Eccube\Entity\DeliveryTime $deliveryTime
     *
     * @return Delivery
     */
    public function addDeliveryTime(\Eccube\Entity\DeliveryTime $deliveryTime)
    {
        $this->DeliveryTimes[] = $deliveryTime;

        return $this;
    }

    /**
     * Remove deliveryTime.
     *
     * @param \Eccube\Entity\DeliveryTime $deliveryTime
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDeliveryTime(\Eccube\Entity\DeliveryTime $deliveryTime)
    {
        return $this->DeliveryTimes->removeElement($deliveryTime);
    }

    /**
     * Get deliveryTimes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeliveryTimes()
    {
        return $this->DeliveryTimes;
    }

    /**
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     * @return Delivery
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
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

    /**
     * Set productType.
     *
     * @param \Eccube\Entity\Master\ProductType|null $productType
     *
     * @return Delivery
     */
    public function setProductType(\Eccube\Entity\Master\ProductType $productType = null)
    {
        $this->ProductType = $productType;

        return $this;
    }

    /**
     * Get productType.
     *
     * @return \Eccube\Entity\Master\ProductType|null
     */
    public function getProductType()
    {
        return $this->ProductType;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Delivery
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Is the visibility visible?
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }
}

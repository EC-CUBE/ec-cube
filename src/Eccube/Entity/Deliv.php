<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deliv
 */
class Deliv extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $delivId;

    /**
     * @var integer
     */
    private $productTypeId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var string
     */
    private $remark;

    /**
     * @var string
     */
    private $confirmUrl;

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
    private $delFlg;

    /**
     * @var integer
     */
    private $creatorId;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Delivtimes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $PaymentOptions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Delivfees;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Delivtimes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->PaymentOptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Delivfees = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get delivId
     *
     * @return integer 
     */
    public function getDelivId()
    {
        return $this->delivId;
    }

    /**
     * Set productTypeId
     *
     * @param integer $productTypeId
     * @return Deliv
     */
    public function setProductTypeId($productTypeId)
    {
        $this->productTypeId = $productTypeId;

        return $this;
    }

    /**
     * Get productTypeId
     *
     * @return integer 
     */
    public function getProductTypeId()
    {
        return $this->productTypeId;
    }

    /**
     * Set name
     *
     * @param string $name
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
     * Set serviceName
     *
     * @param string $serviceName
     * @return Deliv
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get serviceName
     *
     * @return string 
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set remark
     *
     * @param string $remark
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
     * Set confirmUrl
     *
     * @param string $confirmUrl
     * @return Deliv
     */
    public function setConfirmUrl($confirmUrl)
    {
        $this->confirmUrl = $confirmUrl;

        return $this;
    }

    /**
     * Get confirmUrl
     *
     * @return string 
     */
    public function getConfirmUrl()
    {
        return $this->confirmUrl;
    }

    /**
     * Set rank
     *
     * @param integer $rank
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
     * @param integer $status
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
     * Set delFlg
     *
     * @param integer $delFlg
     * @return Deliv
     */
    public function setDelFlg($delFlg)
    {
        $this->delFlg = $delFlg;

        return $this;
    }

    /**
     * Get delFlg
     *
     * @return integer 
     */
    public function getDelFlg()
    {
        return $this->delFlg;
    }

    /**
     * Set creatorId
     *
     * @param integer $creatorId
     * @return Deliv
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    /**
     * Get creatorId
     *
     * @return integer 
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Deliv
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Deliv
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Add Delivtimes
     *
     * @param \Eccube\Entity\Delivtime $delivtimes
     * @return Deliv
     */
    public function addDelivtime(\Eccube\Entity\Delivtime $delivtimes)
    {
        $this->Delivtimes[] = $delivtimes;

        return $this;
    }

    /**
     * Remove Delivtimes
     *
     * @param \Eccube\Entity\Delivtime $delivtimes
     */
    public function removeDelivtime(\Eccube\Entity\Delivtime $delivtimes)
    {
        $this->Delivtimes->removeElement($delivtimes);
    }

    /**
     * Get Delivtimes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDelivtimes()
    {
        return $this->Delivtimes;
    }

    /**
     * Add PaymentOptions
     *
     * @param \Eccube\Entity\PaymentOption $paymentOptions
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

    /**
     * Add Delivfees
     *
     * @param \Eccube\Entity\Delivfee $delivfees
     * @return Deliv
     */
    public function addDelivfee(\Eccube\Entity\Delivfee $delivfees)
    {
        $this->Delivfees[] = $delivfees;

        return $this;
    }

    /**
     * Remove Delivfees
     *
     * @param \Eccube\Entity\Delivfee $delivfees
     */
    public function removeDelivfee(\Eccube\Entity\Delivfee $delivfees)
    {
        $this->Delivfees->removeElement($delivfees);
    }

    /**
     * Get Delivfees
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDelivfees()
    {
        return $this->Delivfees;
    }
}

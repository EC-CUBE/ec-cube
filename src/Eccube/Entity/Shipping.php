<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shipping
 */
class Shipping extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $shippingId;

    /**
     * @var integer
     */
    private $orderId;

    /**
     * @var string
     */
    private $shippingName01;

    /**
     * @var string
     */
    private $shippingName02;

    /**
     * @var string
     */
    private $shippingKana01;

    /**
     * @var string
     */
    private $shippingKana02;

    /**
     * @var string
     */
    private $shippingCompanyName;

    /**
     * @var string
     */
    private $shippingTel01;

    /**
     * @var string
     */
    private $shippingTel02;

    /**
     * @var string
     */
    private $shippingTel03;

    /**
     * @var string
     */
    private $shippingFax01;

    /**
     * @var string
     */
    private $shippingFax02;

    /**
     * @var string
     */
    private $shippingFax03;

    /**
     * @var integer
     */
    private $shippingCountryId;

    /**
     * @var integer
     */
    private $shippingPref;

    /**
     * @var string
     */
    private $shippingZip01;

    /**
     * @var string
     */
    private $shippingZip02;

    /**
     * @var string
     */
    private $shippingZipcode;

    /**
     * @var string
     */
    private $shippingAddr01;

    /**
     * @var string
     */
    private $shippingAddr02;

    /**
     * @var integer
     */
    private $timeId;

    /**
     * @var string
     */
    private $shippingTime;

    /**
     * @var \DateTime
     */
    private $shippingDate;

    /**
     * @var \DateTime
     */
    private $shippingCommitDate;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @var integer
     */
    private $delFlg;


    /**
     * Set shippingId
     *
     * @param integer $shippingId
     * @return Shipping
     */
    public function setShippingId($shippingId)
    {
        $this->shippingId = $shippingId;

        return $this;
    }

    /**
     * Get shippingId
     *
     * @return integer 
     */
    public function getShippingId()
    {
        return $this->shippingId;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return Shipping
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set shippingName01
     *
     * @param string $shippingName01
     * @return Shipping
     */
    public function setShippingName01($shippingName01)
    {
        $this->shippingName01 = $shippingName01;

        return $this;
    }

    /**
     * Get shippingName01
     *
     * @return string 
     */
    public function getShippingName01()
    {
        return $this->shippingName01;
    }

    /**
     * Set shippingName02
     *
     * @param string $shippingName02
     * @return Shipping
     */
    public function setShippingName02($shippingName02)
    {
        $this->shippingName02 = $shippingName02;

        return $this;
    }

    /**
     * Get shippingName02
     *
     * @return string 
     */
    public function getShippingName02()
    {
        return $this->shippingName02;
    }

    /**
     * Set shippingKana01
     *
     * @param string $shippingKana01
     * @return Shipping
     */
    public function setShippingKana01($shippingKana01)
    {
        $this->shippingKana01 = $shippingKana01;

        return $this;
    }

    /**
     * Get shippingKana01
     *
     * @return string 
     */
    public function getShippingKana01()
    {
        return $this->shippingKana01;
    }

    /**
     * Set shippingKana02
     *
     * @param string $shippingKana02
     * @return Shipping
     */
    public function setShippingKana02($shippingKana02)
    {
        $this->shippingKana02 = $shippingKana02;

        return $this;
    }

    /**
     * Get shippingKana02
     *
     * @return string 
     */
    public function getShippingKana02()
    {
        return $this->shippingKana02;
    }

    /**
     * Set shippingCompanyName
     *
     * @param string $shippingCompanyName
     * @return Shipping
     */
    public function setShippingCompanyName($shippingCompanyName)
    {
        $this->shippingCompanyName = $shippingCompanyName;

        return $this;
    }

    /**
     * Get shippingCompanyName
     *
     * @return string 
     */
    public function getShippingCompanyName()
    {
        return $this->shippingCompanyName;
    }

    /**
     * Set shippingTel01
     *
     * @param string $shippingTel01
     * @return Shipping
     */
    public function setShippingTel01($shippingTel01)
    {
        $this->shippingTel01 = $shippingTel01;

        return $this;
    }

    /**
     * Get shippingTel01
     *
     * @return string 
     */
    public function getShippingTel01()
    {
        return $this->shippingTel01;
    }

    /**
     * Set shippingTel02
     *
     * @param string $shippingTel02
     * @return Shipping
     */
    public function setShippingTel02($shippingTel02)
    {
        $this->shippingTel02 = $shippingTel02;

        return $this;
    }

    /**
     * Get shippingTel02
     *
     * @return string 
     */
    public function getShippingTel02()
    {
        return $this->shippingTel02;
    }

    /**
     * Set shippingTel03
     *
     * @param string $shippingTel03
     * @return Shipping
     */
    public function setShippingTel03($shippingTel03)
    {
        $this->shippingTel03 = $shippingTel03;

        return $this;
    }

    /**
     * Get shippingTel03
     *
     * @return string 
     */
    public function getShippingTel03()
    {
        return $this->shippingTel03;
    }

    /**
     * Set shippingFax01
     *
     * @param string $shippingFax01
     * @return Shipping
     */
    public function setShippingFax01($shippingFax01)
    {
        $this->shippingFax01 = $shippingFax01;

        return $this;
    }

    /**
     * Get shippingFax01
     *
     * @return string 
     */
    public function getShippingFax01()
    {
        return $this->shippingFax01;
    }

    /**
     * Set shippingFax02
     *
     * @param string $shippingFax02
     * @return Shipping
     */
    public function setShippingFax02($shippingFax02)
    {
        $this->shippingFax02 = $shippingFax02;

        return $this;
    }

    /**
     * Get shippingFax02
     *
     * @return string 
     */
    public function getShippingFax02()
    {
        return $this->shippingFax02;
    }

    /**
     * Set shippingFax03
     *
     * @param string $shippingFax03
     * @return Shipping
     */
    public function setShippingFax03($shippingFax03)
    {
        $this->shippingFax03 = $shippingFax03;

        return $this;
    }

    /**
     * Get shippingFax03
     *
     * @return string 
     */
    public function getShippingFax03()
    {
        return $this->shippingFax03;
    }

    /**
     * Set shippingCountryId
     *
     * @param integer $shippingCountryId
     * @return Shipping
     */
    public function setShippingCountryId($shippingCountryId)
    {
        $this->shippingCountryId = $shippingCountryId;

        return $this;
    }

    /**
     * Get shippingCountryId
     *
     * @return integer 
     */
    public function getShippingCountryId()
    {
        return $this->shippingCountryId;
    }

    /**
     * Set shippingPref
     *
     * @param integer $shippingPref
     * @return Shipping
     */
    public function setShippingPref($shippingPref)
    {
        $this->shippingPref = $shippingPref;

        return $this;
    }

    /**
     * Get shippingPref
     *
     * @return integer 
     */
    public function getShippingPref()
    {
        return $this->shippingPref;
    }

    /**
     * Set shippingZip01
     *
     * @param string $shippingZip01
     * @return Shipping
     */
    public function setShippingZip01($shippingZip01)
    {
        $this->shippingZip01 = $shippingZip01;

        return $this;
    }

    /**
     * Get shippingZip01
     *
     * @return string 
     */
    public function getShippingZip01()
    {
        return $this->shippingZip01;
    }

    /**
     * Set shippingZip02
     *
     * @param string $shippingZip02
     * @return Shipping
     */
    public function setShippingZip02($shippingZip02)
    {
        $this->shippingZip02 = $shippingZip02;

        return $this;
    }

    /**
     * Get shippingZip02
     *
     * @return string 
     */
    public function getShippingZip02()
    {
        return $this->shippingZip02;
    }

    /**
     * Set shippingZipcode
     *
     * @param string $shippingZipcode
     * @return Shipping
     */
    public function setShippingZipcode($shippingZipcode)
    {
        $this->shippingZipcode = $shippingZipcode;

        return $this;
    }

    /**
     * Get shippingZipcode
     *
     * @return string 
     */
    public function getShippingZipcode()
    {
        return $this->shippingZipcode;
    }

    /**
     * Set shippingAddr01
     *
     * @param string $shippingAddr01
     * @return Shipping
     */
    public function setShippingAddr01($shippingAddr01)
    {
        $this->shippingAddr01 = $shippingAddr01;

        return $this;
    }

    /**
     * Get shippingAddr01
     *
     * @return string 
     */
    public function getShippingAddr01()
    {
        return $this->shippingAddr01;
    }

    /**
     * Set shippingAddr02
     *
     * @param string $shippingAddr02
     * @return Shipping
     */
    public function setShippingAddr02($shippingAddr02)
    {
        $this->shippingAddr02 = $shippingAddr02;

        return $this;
    }

    /**
     * Get shippingAddr02
     *
     * @return string 
     */
    public function getShippingAddr02()
    {
        return $this->shippingAddr02;
    }

    /**
     * Set timeId
     *
     * @param integer $timeId
     * @return Shipping
     */
    public function setTimeId($timeId)
    {
        $this->timeId = $timeId;

        return $this;
    }

    /**
     * Get timeId
     *
     * @return integer 
     */
    public function getTimeId()
    {
        return $this->timeId;
    }

    /**
     * Set shippingTime
     *
     * @param string $shippingTime
     * @return Shipping
     */
    public function setShippingTime($shippingTime)
    {
        $this->shippingTime = $shippingTime;

        return $this;
    }

    /**
     * Get shippingTime
     *
     * @return string 
     */
    public function getShippingTime()
    {
        return $this->shippingTime;
    }

    /**
     * Set shippingDate
     *
     * @param \DateTime $shippingDate
     * @return Shipping
     */
    public function setShippingDate($shippingDate)
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    /**
     * Get shippingDate
     *
     * @return \DateTime 
     */
    public function getShippingDate()
    {
        return $this->shippingDate;
    }

    /**
     * Set shippingCommitDate
     *
     * @param \DateTime $shippingCommitDate
     * @return Shipping
     */
    public function setShippingCommitDate($shippingCommitDate)
    {
        $this->shippingCommitDate = $shippingCommitDate;

        return $this;
    }

    /**
     * Get shippingCommitDate
     *
     * @return \DateTime 
     */
    public function getShippingCommitDate()
    {
        return $this->shippingCommitDate;
    }

    /**
     * Set rank
     *
     * @param integer $rank
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
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Shipping
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
     * @return Shipping
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
     * Set delFlg
     *
     * @param integer $delFlg
     * @return Shipping
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
}

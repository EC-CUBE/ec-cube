<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaxRule
 */
class TaxRule extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $taxRuleId;

    /**
     * @var integer
     */
    private $countryId;

    /**
     * @var integer
     */
    private $prefId;

    /**
     * @var integer
     */
    private $productId;

    /**
     * @var integer
     */
    private $productClassId;

    /**
     * @var integer
     */
    private $calcRule;

    /**
     * @var string
     */
    private $taxRate;

    /**
     * @var string
     */
    private $taxAdjust;

    /**
     * @var \DateTime
     */
    private $applyDate;

    /**
     * @var integer
     */
    private $memberId;

    /**
     * @var integer
     */
    private $delFlg;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;


    /**
     * Get taxRuleId
     *
     * @return integer 
     */
    public function getTaxRuleId()
    {
        return $this->taxRuleId;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     * @return TaxRule
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set prefId
     *
     * @param integer $prefId
     * @return TaxRule
     */
    public function setPrefId($prefId)
    {
        $this->prefId = $prefId;

        return $this;
    }

    /**
     * Get prefId
     *
     * @return integer 
     */
    public function getPrefId()
    {
        return $this->prefId;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     * @return TaxRule
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set productClassId
     *
     * @param integer $productClassId
     * @return TaxRule
     */
    public function setProductClassId($productClassId)
    {
        $this->productClassId = $productClassId;

        return $this;
    }

    /**
     * Get productClassId
     *
     * @return integer 
     */
    public function getProductClassId()
    {
        return $this->productClassId;
    }

    /**
     * Set calcRule
     *
     * @param integer $calcRule
     * @return TaxRule
     */
    public function setCalcRule($calcRule)
    {
        $this->calcRule = $calcRule;

        return $this;
    }

    /**
     * Get calcRule
     *
     * @return integer 
     */
    public function getCalcRule()
    {
        return $this->calcRule;
    }

    /**
     * Set taxRate
     *
     * @param string $taxRate
     * @return TaxRule
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Get taxRate
     *
     * @return string 
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * Set taxAdjust
     *
     * @param string $taxAdjust
     * @return TaxRule
     */
    public function setTaxAdjust($taxAdjust)
    {
        $this->taxAdjust = $taxAdjust;

        return $this;
    }

    /**
     * Get taxAdjust
     *
     * @return string 
     */
    public function getTaxAdjust()
    {
        return $this->taxAdjust;
    }

    /**
     * Set applyDate
     *
     * @param \DateTime $applyDate
     * @return TaxRule
     */
    public function setApplyDate($applyDate)
    {
        $this->applyDate = $applyDate;

        return $this;
    }

    /**
     * Get applyDate
     *
     * @return \DateTime 
     */
    public function getApplyDate()
    {
        return $this->applyDate;
    }

    /**
     * Set memberId
     *
     * @param integer $memberId
     * @return TaxRule
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;

        return $this;
    }

    /**
     * Get memberId
     *
     * @return integer 
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * Set delFlg
     *
     * @param integer $delFlg
     * @return TaxRule
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
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return TaxRule
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
     * @return TaxRule
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
}

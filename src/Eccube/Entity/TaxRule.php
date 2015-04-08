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
    private $id;

    /**
     * @var integer
     */
    private $country_id;

    /**
     * @var integer
     */
    private $pref_id;

    /**
     * @var integer
     */
    private $product_id;

    /**
     * @var integer
     */
    private $product_class_id;

    /**
     * @var integer
     */
    private $calc_rule;

    /**
     * @var string
     */
    private $tax_rate;

    /**
     * @var string
     */
    private $tax_adjust;

    /**
     * @var \DateTime
     */
    private $apply_date;

    /**
     * @var integer
     */
    private $member_id;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set country_id
     *
     * @param integer $countryId
     * @return TaxRule
     */
    public function setCountryId($countryId)
    {
        $this->country_id = $countryId;

        return $this;
    }

    /**
     * Get country_id
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * Set pref_id
     *
     * @param integer $prefId
     * @return TaxRule
     */
    public function setPrefId($prefId)
    {
        $this->pref_id = $prefId;

        return $this;
    }

    /**
     * Get pref_id
     *
     * @return integer 
     */
    public function getPrefId()
    {
        return $this->pref_id;
    }

    /**
     * Set product_id
     *
     * @param integer $productId
     * @return TaxRule
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get product_id
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set product_class_id
     *
     * @param integer $productClassId
     * @return TaxRule
     */
    public function setProductClassId($productClassId)
    {
        $this->product_class_id = $productClassId;

        return $this;
    }

    /**
     * Get product_class_id
     *
     * @return integer 
     */
    public function getProductClassId()
    {
        return $this->product_class_id;
    }

    /**
     * Set calc_rule
     *
     * @param integer $calcRule
     * @return TaxRule
     */
    public function setCalcRule($calcRule)
    {
        $this->calc_rule = $calcRule;

        return $this;
    }

    /**
     * Get calc_rule
     *
     * @return integer 
     */
    public function getCalcRule()
    {
        return $this->calc_rule;
    }

    /**
     * Set tax_rate
     *
     * @param string $taxRate
     * @return TaxRule
     */
    public function setTaxRate($taxRate)
    {
        $this->tax_rate = $taxRate;

        return $this;
    }

    /**
     * Get tax_rate
     *
     * @return string 
     */
    public function getTaxRate()
    {
        return $this->tax_rate;
    }

    /**
     * Set tax_adjust
     *
     * @param string $taxAdjust
     * @return TaxRule
     */
    public function setTaxAdjust($taxAdjust)
    {
        $this->tax_adjust = $taxAdjust;

        return $this;
    }

    /**
     * Get tax_adjust
     *
     * @return string 
     */
    public function getTaxAdjust()
    {
        return $this->tax_adjust;
    }

    /**
     * Set apply_date
     *
     * @param \DateTime $applyDate
     * @return TaxRule
     */
    public function setApplyDate($applyDate)
    {
        $this->apply_date = $applyDate;

        return $this;
    }

    /**
     * Get apply_date
     *
     * @return \DateTime 
     */
    public function getApplyDate()
    {
        return $this->apply_date;
    }

    /**
     * Set member_id
     *
     * @param integer $memberId
     * @return TaxRule
     */
    public function setMemberId($memberId)
    {
        $this->member_id = $memberId;

        return $this;
    }

    /**
     * Get member_id
     *
     * @return integer 
     */
    public function getMemberId()
    {
        return $this->member_id;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return TaxRule
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
     * @param \DateTime $createDate
     * @return TaxRule
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
     * @param \DateTime $updateDate
     * @return TaxRule
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
}

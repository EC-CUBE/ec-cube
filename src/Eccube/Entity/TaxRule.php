<?php

namespace Eccube\Entity;

/**
 * TaxRule
 */
class TaxRule extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $rank;

    /**
     * Set rank
     *
     * @param  integer $rank
     * @return TaxRule
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
     * @var integer
     */
    private $id;

    /**
     * @var \Eccube\Entity\Master\Taxrule
     */
    private $Calc_rule;

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
     * @var \Eccube\Entity\Master\Country
     */
    private $Country;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $Pref;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\ProductClass
     */
    private $ProductClass;

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
     * Set Calc_rule
     *
     * @param  \Eccube\Entity\Master\Taxrule $calcRule
     * @return TaxRule
     */
    public function setCalcRule($calcRule)
    {
        $this->Calc_rule = $calcRule;

        return $this;
    }

    /**
     * Get Calc_rule
     *
     * @return integer
     */
    public function getCalcRule()
    {
        return $this->Calc_rule;
    }

    /**
     * Set tax_rate
     *
     * @param  string  $taxRate
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
     * @param  string  $taxAdjust
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
     * @param  \DateTime $applyDate
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
     * @param  integer $memberId
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
     * @param  integer $delFlg
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
     * @param  \DateTime $createDate
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
     * @param  \DateTime $updateDate
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

    /**
     * Set Country
     *
     * @param  \Eccube\Entity\Master\Country $country
     * @return TaxRule
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
     * @return TaxRule
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
     * Set Product
     *
     * @param  \Eccube\Entity\Product $product
     * @return TaxRule
     */
    public function setProduct(\Eccube\Entity\Product $product = null)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get Product
     *
     * @return \Eccube\Entity\Product
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * Set ProductClass
     *
     * @param  \Eccube\Entity\ProductClass $productClass
     * @return TaxRule
     */
    public function setProductClass(\Eccube\Entity\ProductClass $productClass = null)
    {
        $this->ProductClass = $productClass;

        return $this;
    }

    /**
     * Get ProductClass
     *
     * @return \Eccube\Entity\ProductClass
     */
    public function getProductClass()
    {
        return $this->ProductClass;
    }
}

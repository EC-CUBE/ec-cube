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
 * Payment
 */
class Payment extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMethod();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $charge;

    /**
     * @var string
     */
    private $rule_min;

    /**
     * @var string
     */
    private $rule_max;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var integer
     */
    private $fix_flg;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var string
     */
    private $payment_image;

    /**
     * @var integer
     */
    private $charge_flg;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $PaymentOptions;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set method
     *
     * @param  string  $method
     * @return Payment
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set charge
     *
     * @param  string  $charge
     * @return Payment
     */
    public function setCharge($charge)
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * Get charge
     *
     * @return string
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * Set rule_max
     *
     * @param  string  $ruleMax
     * @return Payment
     */
    public function setRuleMax($ruleMax)
    {
        $this->rule_max = $ruleMax;

        return $this;
    }

    /**
     * Get rule_max
     *
     * @return string
     */
    public function getRuleMax()
    {
        return $this->rule_max;
    }

    /**
     * Set rank
     *
     * @param  integer $rank
     * @return Payment
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
     * Set fix_flg
     *
     * @param  integer $fixFlg
     * @return Payment
     */
    public function setFixFlg($fixFlg)
    {
        $this->fix_flg = $fixFlg;

        return $this;
    }

    /**
     * Get fix_flg
     *
     * @return integer
     */
    public function getFixFlg()
    {
        return $this->fix_flg;
    }

    /**
     * Set del_flg
     *
     * @param  integer $delFlg
     * @return Payment
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
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Payment
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
     * @return Payment
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
     * Set payment_image
     *
     * @param  string  $paymentImage
     * @return Payment
     */
    public function setPaymentImage($paymentImage)
    {
        $this->payment_image = $paymentImage;

        return $this;
    }

    /**
     * Get payment_image
     *
     * @return string
     */
    public function getPaymentImage()
    {
        return $this->payment_image;
    }

    /**
     * Set charge_flg
     *
     * @param  integer $chargeFlg
     * @return Payment
     */
    public function setChargeFlg($chargeFlg)
    {
        $this->charge_flg = $chargeFlg;

        return $this;
    }

    /**
     * Get charge_flg
     *
     * @return integer
     */
    public function getChargeFlg()
    {
        return $this->charge_flg;
    }

    /**
     * Set rule_min
     *
     * @param  string  $ruleMin
     * @return Payment
     */
    public function setRuleMin($ruleMin)
    {
        $this->rule_min = $ruleMin;

        return $this;
    }

    /**
     * Get rule_min
     *
     * @return string
     */
    public function getRuleMin()
    {
        return $this->rule_min;
    }

    /**
     * Add PaymentOptions
     *
     * @param  \Eccube\Entity\PaymentOption $paymentOptions
     * @return Payment
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

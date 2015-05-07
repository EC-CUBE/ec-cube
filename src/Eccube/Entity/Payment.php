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
    private $rule_max;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var string
     */
    private $note;

    /**
     * @var integer
     */
    private $fix;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var integer
     */
    private $creator_id;

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
     * @var string
     */
    private $upper_rule;

    /**
     * @var integer
     */
    private $charge_flg;

    /**
     * @var string
     */
    private $rule_min;

    /**
     * @var string
     */
    private $upper_rule_max;

    /**
     * @var integer
     */
    private $module_id;

    /**
     * @var string
     */
    private $module_path;

    /**
     * @var string
     */
    private $memo01;

    /**
     * @var string
     */
    private $memo02;

    /**
     * @var string
     */
    private $memo03;

    /**
     * @var string
     */
    private $memo04;

    /**
     * @var string
     */
    private $memo05;

    /**
     * @var string
     */
    private $memo06;

    /**
     * @var string
     */
    private $memo07;

    /**
     * @var string
     */
    private $memo08;

    /**
     * @var string
     */
    private $memo09;

    /**
     * @var string
     */
    private $memo10;

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
     * Set note
     *
     * @param  string  $note
     * @return Payment
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set fix
     *
     * @param  integer $fix
     * @return Payment
     */
    public function setFix($fix)
    {
        $this->fix = $fix;

        return $this;
    }

    /**
     * Get fix
     *
     * @return integer
     */
    public function getFix()
    {
        return $this->fix;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return Payment
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
     * Set creator_id
     *
     * @param  integer $creatorId
     * @return Payment
     */
    public function setCreatorId($creatorId)
    {
        $this->creator_id = $creatorId;

        return $this;
    }

    /**
     * Get creator_id
     *
     * @return integer
     */
    public function getCreatorId()
    {
        return $this->creator_id;
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
     * Set upper_rule
     *
     * @param  string  $upperRule
     * @return Payment
     */
    public function setUpperRule($upperRule)
    {
        $this->upper_rule = $upperRule;

        return $this;
    }

    /**
     * Get upper_rule
     *
     * @return string
     */
    public function getUpperRule()
    {
        return $this->upper_rule;
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
     * Set upper_rule_max
     *
     * @param  string  $upperRuleMax
     * @return Payment
     */
    public function setUpperRuleMax($upperRuleMax)
    {
        $this->upper_rule_max = $upperRuleMax;

        return $this;
    }

    /**
     * Get upper_rule_max
     *
     * @return string
     */
    public function getUpperRuleMax()
    {
        return $this->upper_rule_max;
    }

    /**
     * Set module_id
     *
     * @param  integer $moduleId
     * @return Payment
     */
    public function setModuleId($moduleId)
    {
        $this->module_id = $moduleId;

        return $this;
    }

    /**
     * Get module_id
     *
     * @return integer
     */
    public function getModuleId()
    {
        return $this->module_id;
    }

    /**
     * Set module_path
     *
     * @param  string  $modulePath
     * @return Payment
     */
    public function setModulePath($modulePath)
    {
        $this->module_path = $modulePath;

        return $this;
    }

    /**
     * Get module_path
     *
     * @return string
     */
    public function getModulePath()
    {
        return $this->module_path;
    }

    /**
     * Set memo01
     *
     * @param  string  $memo01
     * @return Payment
     */
    public function setMemo01($memo01)
    {
        $this->memo01 = $memo01;

        return $this;
    }

    /**
     * Get memo01
     *
     * @return string
     */
    public function getMemo01()
    {
        return $this->memo01;
    }

    /**
     * Set memo02
     *
     * @param  string  $memo02
     * @return Payment
     */
    public function setMemo02($memo02)
    {
        $this->memo02 = $memo02;

        return $this;
    }

    /**
     * Get memo02
     *
     * @return string
     */
    public function getMemo02()
    {
        return $this->memo02;
    }

    /**
     * Set memo03
     *
     * @param  string  $memo03
     * @return Payment
     */
    public function setMemo03($memo03)
    {
        $this->memo03 = $memo03;

        return $this;
    }

    /**
     * Get memo03
     *
     * @return string
     */
    public function getMemo03()
    {
        return $this->memo03;
    }

    /**
     * Set memo04
     *
     * @param  string  $memo04
     * @return Payment
     */
    public function setMemo04($memo04)
    {
        $this->memo04 = $memo04;

        return $this;
    }

    /**
     * Get memo04
     *
     * @return string
     */
    public function getMemo04()
    {
        return $this->memo04;
    }

    /**
     * Set memo05
     *
     * @param  string  $memo05
     * @return Payment
     */
    public function setMemo05($memo05)
    {
        $this->memo05 = $memo05;

        return $this;
    }

    /**
     * Get memo05
     *
     * @return string
     */
    public function getMemo05()
    {
        return $this->memo05;
    }

    /**
     * Set memo06
     *
     * @param  string  $memo06
     * @return Payment
     */
    public function setMemo06($memo06)
    {
        $this->memo06 = $memo06;

        return $this;
    }

    /**
     * Get memo06
     *
     * @return string
     */
    public function getMemo06()
    {
        return $this->memo06;
    }

    /**
     * Set memo07
     *
     * @param  string  $memo07
     * @return Payment
     */
    public function setMemo07($memo07)
    {
        $this->memo07 = $memo07;

        return $this;
    }

    /**
     * Get memo07
     *
     * @return string
     */
    public function getMemo07()
    {
        return $this->memo07;
    }

    /**
     * Set memo08
     *
     * @param  string  $memo08
     * @return Payment
     */
    public function setMemo08($memo08)
    {
        $this->memo08 = $memo08;

        return $this;
    }

    /**
     * Get memo08
     *
     * @return string
     */
    public function getMemo08()
    {
        return $this->memo08;
    }

    /**
     * Set memo09
     *
     * @param  string  $memo09
     * @return Payment
     */
    public function setMemo09($memo09)
    {
        $this->memo09 = $memo09;

        return $this;
    }

    /**
     * Get memo09
     *
     * @return string
     */
    public function getMemo09()
    {
        return $this->memo09;
    }

    /**
     * Set memo10
     *
     * @param  string  $memo10
     * @return Payment
     */
    public function setMemo10($memo10)
    {
        $this->memo10 = $memo10;

        return $this;
    }

    /**
     * Get memo10
     *
     * @return string
     */
    public function getMemo10()
    {
        return $this->memo10;
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

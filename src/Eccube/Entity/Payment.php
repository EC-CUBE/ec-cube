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
 * Payment
 *
 * @ORM\Table(name="dtb_payment")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\PaymentRepository")
 */
class Payment extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getMethod();
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="payment_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_method", type="string", length=255, nullable=true)
     */
    private $method;

    /**
     * @var string|null
     *
     * @ORM\Column(name="charge", type="decimal", precision=12, scale=2, nullable=true, options={"unsigned":true,"default":0})
     */
    private $charge = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rule_max", type="decimal", precision=12, scale=2, nullable=true, options={"unsigned":true})
     */
    private $rule_max;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rank", type="smallint", nullable=true, options={"unsigned":true})
     */
    private $rank;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fix_flg", type="boolean", options={"default":true})
     */
    private $fix_flg = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_image", type="string", length=255, nullable=true)
     */
    private $payment_image;

    /**
     * @var boolean
     *
     * @ORM\Column(name="charge_flg", type="boolean", options={"default":true})
     */
    private $charge_flg = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rule_min", type="decimal", precision=12, scale=2, nullable=true, options={"unsigned":true})
     */
    private $rule_min;

    /**
     * @var string|null
     *
     * @ORM\Column(name="method_class", type="string", length=255, nullable=true)
     */
    private $method_class;

    /**
     * @var string|null
     *
     * @ORM\Column(name="service_class", type="string", length=255, nullable=true)
     */
    private $service_class;

    /**
     * @var int
     *
     * @ORM\Column(name="visible", type="boolean", options={"default":true})
     */
    private $visible;

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
     * @ORM\OneToMany(targetEntity="Eccube\Entity\PaymentOption", mappedBy="Payment")
     */
    private $PaymentOptions;

    /**
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="member_id")
     * })
     */
    private $Creator;

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
     * @param string $method
     *
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
     * @param string $charge
     *
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
     * Set ruleMax
     *
     * @param string $ruleMax
     *
     * @return Payment
     */
    public function setRuleMax($ruleMax)
    {
        $this->rule_max = $ruleMax;

        return $this;
    }

    /**
     * Get ruleMax
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
     * @param integer $rank
     *
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
     * Set fixFlg
     *
     * @param integer $fixFlg
     *
     * @return Payment
     */
    public function setFixFlg($fixFlg)
    {
        $this->fix_flg = $fixFlg;

        return $this;
    }

    /**
     * Get fixFlg
     *
     * @return integer
     */
    public function getFixFlg()
    {
        return $this->fix_flg;
    }

    /**
     * Set paymentImage
     *
     * @param string $paymentImage
     *
     * @return Payment
     */
    public function setPaymentImage($paymentImage)
    {
        $this->payment_image = $paymentImage;

        return $this;
    }

    /**
     * Get paymentImage
     *
     * @return string
     */
    public function getPaymentImage()
    {
        return $this->payment_image;
    }

    /**
     * Set chargeFlg
     *
     * @param integer $chargeFlg
     *
     * @return Payment
     */
    public function setChargeFlg($chargeFlg)
    {
        $this->charge_flg = $chargeFlg;

        return $this;
    }

    /**
     * Get chargeFlg
     *
     * @return integer
     */
    public function getChargeFlg()
    {
        return $this->charge_flg;
    }

    /**
     * Set ruleMin
     *
     * @param string $ruleMin
     *
     * @return Payment
     */
    public function setRuleMin($ruleMin)
    {
        $this->rule_min = $ruleMin;

        return $this;
    }

    /**
     * Get ruleMin
     *
     * @return string
     */
    public function getRuleMin()
    {
        return $this->rule_min;
    }

    /**
     * Set methodClass
     *
     * @param string $methodClass
     *
     * @return Payment
     */
    public function setMethodClass($methodClass)
    {
        $this->method_class = $methodClass;

        return $this;
    }

    /**
     * Get methodClass
     *
     * @return string
     */
    public function getMethodClass()
    {
        return $this->method_class;
    }

    /**
     * Set serviceClass
     *
     * @param string $serviceClass
     *
     * @return Payment
     */
    public function setServiceClass($serviceClass)
    {
        $this->service_class = $serviceClass;

        return $this;
    }

    /**
     * Get serviceClass
     *
     * @return string
     */
    public function getServiceClass()
    {
        return $this->service_class;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Payment
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Payment
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Payment
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Add paymentOption
     *
     * @param \Eccube\Entity\PaymentOption $paymentOption
     *
     * @return Payment
     */
    public function addPaymentOption(\Eccube\Entity\PaymentOption $paymentOption)
    {
        $this->PaymentOptions[] = $paymentOption;

        return $this;
    }

    /**
     * Remove paymentOption
     *
     * @param \Eccube\Entity\PaymentOption $paymentOption
     */
    public function removePaymentOption(\Eccube\Entity\PaymentOption $paymentOption)
    {
        $this->PaymentOptions->removeElement($paymentOption);
    }

    /**
     * Get paymentOptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentOptions()
    {
        return $this->PaymentOptions;
    }

    /**
     * Set creator
     *
     * @param \Eccube\Entity\Member $creator
     *
     * @return Payment
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Eccube\Entity\Member
     */
    public function getCreator()
    {
        return $this->Creator;
    }

    /**
     * @return string
     */
    public function getMethodForAdmin()
    {
        if ($this->isVisible()) {
            return $this->getMethod();
        }
        return $this->getMethod().'(非表示)';
    }
}

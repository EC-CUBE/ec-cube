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
        return $this->getMethod();
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
     * @ORM\Column(name="charge", type="decimal", precision=10, scale=2, nullable=true, options={"unsigned":true,"default":0})
     */
    private $charge = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rule_max", type="decimal", precision=10, scale=2, nullable=true, options={"unsigned":true})
     */
    private $rule_max;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rank", type="smallint", nullable=true, options={"unsigned":true})
     */
    private $rank;

    /**
     * @var int|null
     *
     * @ORM\Column(name="fix_flg", type="smallint", nullable=true, options={"unsigned":true,"default":1})
     */
    private $fix_flg = 1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_image", type="string", length=255, nullable=true)
     */
    private $payment_image;

    /**
     * @var int|null
     *
     * @ORM\Column(name="charge_flg", type="smallint", nullable=true, options={"unsigned":true,"default":1})
     */
    private $charge_flg = 1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rule_min", type="decimal", precision=10, scale=2, nullable=true, options={"unsigned":true})
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
     * @ORM\Column(name="del_flg", type="smallint", options={"unsigned":true,"default":0})
     */
    private $del_flg = 0;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set method.
     *
     * @param string|null $method
     *
     * @return Payment
     */
    public function setMethod($method = null)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method.
     *
     * @return string|null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set charge.
     *
     * @param string|null $charge
     *
     * @return Payment
     */
    public function setCharge($charge = null)
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * Get charge.
     *
     * @return string|null
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * Set ruleMax.
     *
     * @param string|null $ruleMax
     *
     * @return Payment
     */
    public function setRuleMax($ruleMax = null)
    {
        $this->rule_max = $ruleMax;

        return $this;
    }

    /**
     * Get ruleMax.
     *
     * @return string|null
     */
    public function getRuleMax()
    {
        return $this->rule_max;
    }

    /**
     * Set rank.
     *
     * @param int|null $rank
     *
     * @return Payment
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
     * Set fixFlg.
     *
     * @param int|null $fixFlg
     *
     * @return Payment
     */
    public function setFixFlg($fixFlg = null)
    {
        $this->fix_flg = $fixFlg;

        return $this;
    }

    /**
     * Get fixFlg.
     *
     * @return int|null
     */
    public function getFixFlg()
    {
        return $this->fix_flg;
    }

    /**
     * Set paymentImage.
     *
     * @param string|null $paymentImage
     *
     * @return Payment
     */
    public function setPaymentImage($paymentImage = null)
    {
        $this->payment_image = $paymentImage;

        return $this;
    }

    /**
     * Get paymentImage.
     *
     * @return string|null
     */
    public function getPaymentImage()
    {
        return $this->payment_image;
    }

    /**
     * Set chargeFlg.
     *
     * @param int|null $chargeFlg
     *
     * @return Payment
     */
    public function setChargeFlg($chargeFlg = null)
    {
        $this->charge_flg = $chargeFlg;

        return $this;
    }

    /**
     * Get chargeFlg.
     *
     * @return int|null
     */
    public function getChargeFlg()
    {
        return $this->charge_flg;
    }

    /**
     * Set ruleMin.
     *
     * @param string|null $ruleMin
     *
     * @return Payment
     */
    public function setRuleMin($ruleMin = null)
    {
        $this->rule_min = $ruleMin;

        return $this;
    }

    /**
     * Get ruleMin.
     *
     * @return string|null
     */
    public function getRuleMin()
    {
        return $this->rule_min;
    }

    /**
     * Set methodClass.
     *
     * @param string|null $methodClass
     *
     * @return Payment
     */
    public function setMethodClass($methodClass = null)
    {
        $this->method_class = $methodClass;

        return $this;
    }

    /**
     * Get methodClass.
     *
     * @return string|null
     */
    public function getMethodClass()
    {
        return $this->method_class;
    }

    /**
     * Set serviceClass.
     *
     * @param string|null $serviceClass
     *
     * @return Payment
     */
    public function setServiceClass($serviceClass = null)
    {
        $this->service_class = $serviceClass;

        return $this;
    }

    /**
     * Get serviceClass.
     *
     * @return string|null
     */
    public function getServiceClass()
    {
        return $this->service_class;
    }

    /**
     * Set delFlg.
     *
     * @param int $delFlg
     *
     * @return Payment
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get delFlg.
     *
     * @return int
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set createDate.
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
     * @return Payment
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
     * @return Payment
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
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     * @return Payment
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
}

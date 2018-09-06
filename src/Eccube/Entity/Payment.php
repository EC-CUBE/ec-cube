<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\Payment')) {
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
            return (string) $this->getMethod();
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
         * @ORM\Column(name="sort_no", type="smallint", nullable=true, options={"unsigned":true})
         */
        private $sort_no;

        /**
         * @var boolean
         *
         * @ORM\Column(name="fixed", type="boolean", options={"default":true})
         */
        private $fixed = true;

        /**
         * @var string|null
         *
         * @ORM\Column(name="payment_image", type="string", length=255, nullable=true)
         */
        private $payment_image;

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
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
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
         * Set sortNo.
         *
         * @param int|null $sortNo
         *
         * @return Payment
         */
        public function setSortNo($sortNo = null)
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int|null
         */
        public function getSortNo()
        {
            return $this->sort_no;
        }

        /**
         * Set fixed.
         *
         * @param boolean $fixed
         *
         * @return Payment
         */
        public function setFixed($fixed)
        {
            $this->fixed = $fixed;

            return $this;
        }

        /**
         * Get fixed.
         *
         * @return boolean
         */
        public function isFixed()
        {
            return $this->fixed;
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
         * @return integer
         */
        public function isVisible()
        {
            return $this->visible;
        }

        /**
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

        /**
         * @return string
         *
         * @deprecated
         */
        public function getMethodForAdmin()
        {
            if ($this->isVisible()) {
                return $this->getMethod();
            }

            return $this->getMethod().'(非表示)';
        }
    }
}

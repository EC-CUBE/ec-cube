<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\PaymentOption')) {
    /**
     * PaymentOption
     *
     * @ORM\Table(name="dtb_payment_option")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\PaymentOptionRepository")
     */
    class PaymentOption extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="delivery_id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="NONE")
         */
        private $delivery_id;

        /**
         * @var int
         *
         * @ORM\Column(name="payment_id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="NONE")
         */
        private $payment_id;

        /**
         * @var \Eccube\Entity\Delivery
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Delivery", inversedBy="PaymentOptions")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="delivery_id", referencedColumnName="id")
         * })
         */
        private $Delivery;

        /**
         * @var \Eccube\Entity\Payment
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Payment", inversedBy="PaymentOptions")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
         * })
         */
        private $Payment;

        /**
         * Set deliveryId.
         *
         * @param int $deliveryId
         *
         * @return PaymentOption
         */
        public function setDeliveryId($deliveryId)
        {
            $this->delivery_id = $deliveryId;

            return $this;
        }

        /**
         * Get deliveryId.
         *
         * @return int
         */
        public function getDeliveryId()
        {
            return $this->delivery_id;
        }

        /**
         * Set paymentId.
         *
         * @param int $paymentId
         *
         * @return PaymentOption
         */
        public function setPaymentId($paymentId)
        {
            $this->payment_id = $paymentId;

            return $this;
        }

        /**
         * Get paymentId.
         *
         * @return int
         */
        public function getPaymentId()
        {
            return $this->payment_id;
        }

        /**
         * Set delivery.
         *
         * @param \Eccube\Entity\Delivery|null $delivery
         *
         * @return PaymentOption
         */
        public function setDelivery(Delivery $delivery = null)
        {
            $this->Delivery = $delivery;

            return $this;
        }

        /**
         * Get delivery.
         *
         * @return \Eccube\Entity\Delivery|null
         */
        public function getDelivery()
        {
            return $this->Delivery;
        }

        /**
         * Set payment.
         *
         * @param \Eccube\Entity\Payment|null $payment
         *
         * @return PaymentOption
         */
        public function setPayment(Payment $payment = null)
        {
            $this->Payment = $payment;

            return $this;
        }

        /**
         * Get payment.
         *
         * @return \Eccube\Entity\Payment|null
         */
        public function getPayment()
        {
            return $this->Payment;
        }
    }
}

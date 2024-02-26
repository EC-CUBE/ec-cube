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

if (!class_exists('\Eccube\Entity\DeliveryFee')) {
    /**
     * DeliveryFee
     *
     * @ORM\Table(name="dtb_delivery_fee")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\DeliveryFeeRepository")
     */
    class DeliveryFee extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="fee", type="decimal", precision=12, scale=2, options={"unsigned":true})
         */
        private $fee;

        /**
         * @var \Eccube\Entity\Delivery
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Delivery", inversedBy="DeliveryFees")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="delivery_id", referencedColumnName="id")
         * })
         */
        private $Delivery;

        /**
         * @var \Eccube\Entity\Master\Pref
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="pref_id", referencedColumnName="id")
         * })
         */
        private $Pref;

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
         * Set fee.
         *
         * @param string $fee
         *
         * @return DeliveryFee
         */
        public function setFee($fee)
        {
            $this->fee = $fee;

            return $this;
        }

        /**
         * Get fee.
         *
         * @return string
         */
        public function getFee()
        {
            return $this->fee;
        }

        /**
         * Set delivery.
         *
         * @param \Eccube\Entity\Delivery|null $delivery
         *
         * @return DeliveryFee
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
         * Set pref.
         *
         * @param \Eccube\Entity\Master\Pref|null $pref
         *
         * @return DeliveryFee
         */
        public function setPref(Master\Pref $pref = null)
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return \Eccube\Entity\Master\Pref|null
         */
        public function getPref()
        {
            return $this->Pref;
        }
    }
}

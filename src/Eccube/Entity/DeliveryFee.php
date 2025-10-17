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
use Eccube\Entity\Master\Pref;
use Eccube\Repository\DeliveryFeeRepository;

if (!class_exists(DeliveryFee::class)) {
    /**
     * DeliveryFee
     */
    #[ORM\Table(name: 'dtb_delivery_fee')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: DeliveryFeeRepository::class)]
    class DeliveryFee extends AbstractEntity
    {
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'fee', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true])]
        private $fee;

        /**
         * @var Delivery|null
         */
        #[ORM\ManyToOne(targetEntity: Delivery::class, inversedBy: 'DeliveryFees')]
        #[ORM\JoinColumn(name: 'delivery_id', referencedColumnName: 'id', nullable: false)]
        private $Delivery;

        /**
         * @var Pref|null
         */
        #[ORM\ManyToOne(targetEntity: Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private $Pref;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId(): ?int
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
        public function setFee($fee): DeliveryFee
        {
            $this->fee = $fee;

            return $this;
        }

        /**
         * Get fee.
         *
         * @return string|null
         */
        public function getFee(): ?string
        {
            return $this->fee;
        }

        /**
         * Set delivery.
         *
         * @param Delivery|null $delivery
         *
         * @return DeliveryFee
         */
        public function setDelivery(?Delivery $delivery = null): DeliveryFee
        {
            $this->Delivery = $delivery;

            return $this;
        }

        /**
         * Get delivery.
         *
         * @return Delivery|null
         */
        public function getDelivery(): ?Delivery
        {
            return $this->Delivery;
        }

        /**
         * Set pref.
         *
         * @param Pref|null $pref
         *
         * @return DeliveryFee
         */
        public function setPref(?Pref $pref = null): DeliveryFee
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return Pref|null
         */
        public function getPref(): ?Pref
        {
            return $this->Pref;
        }
    }
}

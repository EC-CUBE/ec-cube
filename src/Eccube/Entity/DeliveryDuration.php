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

if (!class_exists('\Eccube\Entity\DeliveryDuration')) {
    /**
     * DeliveryDuration
     *
     * @ORM\Table(name="dtb_delivery_duration")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\DeliveryDurationRepository")
     */
    class DeliveryDuration extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @return string
         */
        public function __toString()
        {
            return $this->getName();
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
         * @ORM\Column(name="name", type="string", length=255, nullable=true)
         */
        private $name;

        /**
         * @var int
         *
         * @ORM\Column(name="duration", type="smallint", options={"default":0})
         */
        private $duration = 0;

        /**
         * @var int
         *
         * @ORM\Column(name="sort_no", type="integer", options={"unsigned":true})
         */
        private $sort_no;

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
         * Set name.
         *
         * @param string|null $name
         *
         * @return DeliveryDuration
         */
        public function setName($name = null)
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string|null
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Set duration.
         *
         * @param int $duration
         *
         * @return DeliveryDuration
         */
        public function setDuration($duration)
        {
            $this->duration = $duration;

            return $this;
        }

        /**
         * Get duration.
         *
         * @return int
         */
        public function getDuration()
        {
            return $this->duration;
        }

        /**
         * Set sortNo.
         *
         * @param int $sortNo
         *
         * @return DeliveryDuration
         */
        public function setSortNo($sortNo)
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int
         */
        public function getSortNo()
        {
            return $this->sort_no;
        }
    }
}

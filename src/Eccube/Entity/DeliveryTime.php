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

if (!class_exists('\Eccube\Entity\DeliveryTime')) {
    /**
     * DeliveryTime
     *
     * @ORM\Table(name="dtb_delivery_time")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\DeliveryTimeRepository")
     */
    class DeliveryTime extends \Eccube\Entity\AbstractEntity
    {
        public function __toString()
        {
            return (string) $this->delivery_time;
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
         * @var string
         *
         * @ORM\Column(name="delivery_time", type="string", length=255)
         */
        private $delivery_time;

        /**
         * @var \Eccube\Entity\Delivery
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Delivery", inversedBy="DeliveryTimes")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="delivery_id", referencedColumnName="id")
         * })
         */
        private $Delivery;

        /**
         * @var int
         *
         * @ORM\Column(name="sort_no", type="smallint", options={"unsigned":true})
         */
        protected $sort_no;

        /**
         * @var boolean
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
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set deliveryTime.
         *
         * @param string $deliveryTime
         *
         * @return DeliveryTime
         */
        public function setDeliveryTime($deliveryTime)
        {
            $this->delivery_time = $deliveryTime;

            return $this;
        }

        /**
         * Get deliveryTime.
         *
         * @return string
         */
        public function getDeliveryTime()
        {
            return $this->delivery_time;
        }

        /**
         * Set delivery.
         *
         * @param \Eccube\Entity\Delivery|null $delivery
         *
         * @return DeliveryTime
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
         * Set sort_no.
         *
         * @param int $sort_no
         *
         * @return $this
         */
        public function setSortNo($sort_no)
        {
            $this->sort_no = $sort_no;

            return $this;
        }

        /**
         * Get sort_no.
         *
         * @return int
         */
        public function getSortNo()
        {
            return $this->sort_no;
        }

        /**
         * Set visible
         *
         * @param boolean $visible
         *
         * @return DeliveryTime
         */
        public function setVisible($visible)
        {
            $this->visible = $visible;

            return $this;
        }

        /**
         * Is the visibility visible?
         *
         * @return boolean
         */
        public function isVisible()
        {
            return $this->visible;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return DeliveryTime
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
         * @return DeliveryTime
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
    }
}

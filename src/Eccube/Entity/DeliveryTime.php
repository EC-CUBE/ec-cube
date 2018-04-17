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
    public function setDelivery(\Eccube\Entity\Delivery $delivery = null)
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
}

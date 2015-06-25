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
 * DeliveryFee
 */
class DeliveryFee extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $fee;

    /**
     * @var \Eccube\Entity\Delivery
     */
    private $Delivery;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $Pref;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set fee
     *
     * @param  string   $fee
     * @return DeliveryFee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return string
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set Delivery
     *
     * @param  \Eccube\Entity\Delivery $Delivery
     * @return DeliveryFee
     */
    public function setDelivery(\Eccube\Entity\Delivery $Delivery = null)
    {
        $this->Delivery = $Delivery;

        return $this;
    }

    /**
     * Get Delivery
     *
     * @return \Eccube\Entity\Delivery
     */
    public function getDelivery()
    {
        return $this->Delivery;
    }

    /**
     * Set Pref
     *
     * @param  \Eccube\Entity\Master\Pref $pref
     * @return DeliveryFee
     */
    public function setPref(\Eccube\Entity\Master\Pref $pref)
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get Pref
     *
     * @return \Eccube\Entity\Master\Pref
     */
    public function getPref()
    {
        return $this->Pref;
    }
}

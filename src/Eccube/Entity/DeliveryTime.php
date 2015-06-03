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
 * DelivTime
 */
class DelivTime extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $deliv_id;

    /**
     * @var integer
     */
    private $time_id;

    /**
     * @var string
     */
    private $deliv_time;

    /**
     * @var \Eccube\Entity\Deliv
     */
    private $Deliv;

    /**
     * Set deliv_id
     *
     * @param  integer   $delivId
     * @return DelivTime
     */
    public function setDelivId($delivId)
    {
        $this->deliv_id = $delivId;

        return $this;
    }

    /**
     * Get deliv_id
     *
     * @return integer
     */
    public function getDelivId()
    {
        return $this->deliv_id;
    }

    /**
     * Set time_id
     *
     * @param  integer   $timeId
     * @return DelivTime
     */
    public function setTimeId($timeId)
    {
        $this->time_id = $timeId;

        return $this;
    }

    /**
     * Get time_id
     *
     * @return integer
     */
    public function getTimeId()
    {
        return $this->time_id;
    }

    /**
     * Set deliv_time
     *
     * @param  string    $delivTime
     * @return DelivTime
     */
    public function setDelivTime($delivTime)
    {
        $this->deliv_time = $delivTime;

        return $this;
    }

    /**
     * Get deliv_time
     *
     * @return string
     */
    public function getDelivTime()
    {
        return $this->deliv_time;
    }

    /**
     * Set Deliv
     *
     * @param  \Eccube\Entity\Deliv $deliv
     * @return DelivTime
     */
    public function setDeliv(\Eccube\Entity\Deliv $deliv = null)
    {
        $this->Deliv = $deliv;

        return $this;
    }

    /**
     * Get Deliv
     *
     * @return \Eccube\Entity\Deliv
     */
    public function getDeliv()
    {
        return $this->Deliv;
    }
}

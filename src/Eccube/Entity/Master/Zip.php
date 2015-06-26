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


namespace Eccube\Entity\Master;

/**
 * Zip
 */
class Zip extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getState() . $this->getCity() . $this->getTown();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $zipcode;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $town;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set zipcode
     *
     * @param  string $zipcode
     * @return Zip
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set state
     *
     * @param  string $state
     * @return Zip
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set city
     *
     * @param  string $city
     * @return Zip
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set town
     *
     * @param  string $town
     * @return Zip
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Zip
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}

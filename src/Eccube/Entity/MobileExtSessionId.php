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
 * MobileExtSessionId
 */
class MobileExtSessionId extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $param_key;

    /**
     * @var string
     */
    private $param_value;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set param_key
     *
     * @param  string             $paramKey
     * @return MobileExtSessionId
     */
    public function setParamKey($paramKey)
    {
        $this->param_key = $paramKey;

        return $this;
    }

    /**
     * Get param_key
     *
     * @return string
     */
    public function getParamKey()
    {
        return $this->param_key;
    }

    /**
     * Set param_value
     *
     * @param  string             $paramValue
     * @return MobileExtSessionId
     */
    public function setParamValue($paramValue)
    {
        $this->param_value = $paramValue;

        return $this;
    }

    /**
     * Get param_value
     *
     * @return string
     */
    public function getParamValue()
    {
        return $this->param_value;
    }

    /**
     * Set url
     *
     * @param  string             $url
     * @return MobileExtSessionId
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime          $createDate
     * @return MobileExtSessionId
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set id
     *
     * @param  string             $id
     * @return MobileExtSessionId
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}

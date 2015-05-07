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
 * SendCustomer
 */
class SendCustomer extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $send_id;

    /**
     * @var integer
     */
    private $customer_id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $send_flag;

    /**
     * Set send_id
     *
     * @param  integer      $sendId
     * @return SendCustomer
     */
    public function setSendId($sendId)
    {
        $this->send_id = $sendId;

        return $this;
    }

    /**
     * Get send_id
     *
     * @return integer
     */
    public function getSendId()
    {
        return $this->send_id;
    }

    /**
     * Set customer_id
     *
     * @param  integer      $customerId
     * @return SendCustomer
     */
    public function setCustomerId($customerId)
    {
        $this->customer_id = $customerId;

        return $this;
    }

    /**
     * Get customer_id
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Set email
     *
     * @param  string       $email
     * @return SendCustomer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param  string       $name
     * @return SendCustomer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set send_flag
     *
     * @param  integer      $sendFlag
     * @return SendCustomer
     */
    public function setSendFlag($sendFlag)
    {
        $this->send_flag = $sendFlag;

        return $this;
    }

    /**
     * Get send_flag
     *
     * @return integer
     */
    public function getSendFlag()
    {
        return $this->send_flag;
    }
}

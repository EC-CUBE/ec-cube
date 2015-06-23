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
 * MailHistory
 */
class MailHistory extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSubject();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $send_date;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $mail_body;

    /**
     * @var \Eccube\Entity\Order
     */
    private $Order;

    /**
     * @var \Eccube\Entity\MailTemplate
     */
    private $MailTemplate;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;

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
     * Set send_date
     *
     * @param  \DateTime   $sendDate
     * @return MailHistory
     */
    public function setSendDate($sendDate)
    {
        $this->send_date = $sendDate;

        return $this;
    }

    /**
     * Get send_date
     *
     * @return \DateTime
     */
    public function getSendDate()
    {
        return $this->send_date;
    }

    /**
     * Set subject
     *
     * @param  string      $subject
     * @return MailHistory
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set mail_body
     *
     * @param  string      $mailBody
     * @return MailHistory
     */
    public function setMailBody($mailBody)
    {
        $this->mail_body = $mailBody;

        return $this;
    }

    /**
     * Get mail_body
     *
     * @return string
     */
    public function getMailBody()
    {
        return $this->mail_body;
    }

    /**
     * Set Order
     *
     * @param  \Eccube\Entity\Order $order
     * @return MailHistory
     */
    public function setOrder(\Eccube\Entity\Order $order)
    {
        $this->Order = $order;

        return $this;
    }

    /**
     * Get Order
     *
     * @return \Eccube\Entity\Order
     */
    public function getOrder()
    {
        return $this->Order;
    }

    /**
     * Set MailTemplate
     *
     * @param  \Eccube\Entity\MailTemplate $mailTemplate
     * @return MailHistory
     */
    public function setMailTemplate(\Eccube\Entity\MailTemplate $mailTemplate = null)
    {
        $this->MailTemplate = $mailTemplate;

        return $this;
    }

    /**
     * Get MailTemplate
     *
     * @return \Eccube\Entity\MailTemplate
     */
    public function getMailTemplate()
    {
        return $this->MailTemplate;
    }

    /**
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $creator
     * @return MailHistory
     */
    public function setCreator(\Eccube\Entity\Member $creator)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get Creator
     *
     * @return \Eccube\Entity\Member
     */
    public function getCreator()
    {
        return $this->Creator;
    }
}

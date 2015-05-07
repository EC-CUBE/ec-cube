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
 * SendHistory
 */
class SendHistory extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $mail_method;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @var integer
     */
    private $send_count;

    /**
     * @var integer
     */
    private $complete_count;

    /**
     * @var \DateTime
     */
    private $start_date;

    /**
     * @var \DateTime
     */
    private $end_date;

    /**
     * @var string
     */
    private $search_data;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

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
     * Set mail_method
     *
     * @param  integer     $mailMethod
     * @return SendHistory
     */
    public function setMailMethod($mailMethod)
    {
        $this->mail_method = $mailMethod;

        return $this;
    }

    /**
     * Get mail_method
     *
     * @return integer
     */
    public function getMailMethod()
    {
        return $this->mail_method;
    }

    /**
     * Set subject
     *
     * @param  string      $subject
     * @return SendHistory
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
     * Set body
     *
     * @param  string      $body
     * @return SendHistory
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set send_count
     *
     * @param  integer     $sendCount
     * @return SendHistory
     */
    public function setSendCount($sendCount)
    {
        $this->send_count = $sendCount;

        return $this;
    }

    /**
     * Get send_count
     *
     * @return integer
     */
    public function getSendCount()
    {
        return $this->send_count;
    }

    /**
     * Set complete_count
     *
     * @param  integer     $completeCount
     * @return SendHistory
     */
    public function setCompleteCount($completeCount)
    {
        $this->complete_count = $completeCount;

        return $this;
    }

    /**
     * Get complete_count
     *
     * @return integer
     */
    public function getCompleteCount()
    {
        return $this->complete_count;
    }

    /**
     * Set start_date
     *
     * @param  \DateTime   $startDate
     * @return SendHistory
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param  \DateTime   $endDate
     * @return SendHistory
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set search_data
     *
     * @param  string      $searchData
     * @return SendHistory
     */
    public function setSearchData($searchData)
    {
        $this->search_data = $searchData;

        return $this;
    }

    /**
     * Get search_data
     *
     * @return string
     */
    public function getSearchData()
    {
        return $this->search_data;
    }

    /**
     * Set del_flg
     *
     * @param  integer     $delFlg
     * @return SendHistory
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime   $createDate
     * @return SendHistory
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
     * Set update_date
     *
     * @param  \DateTime   $updateDate
     * @return SendHistory
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $creator
     * @return SendHistory
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
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

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
 * News
 *
 * @ORM\Table(name="dtb_news")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\NewsRepository")
 */
class News extends AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTitle();
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="news_date", type="datetimetz", nullable=true)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_no", type="smallint", options={"unsigned":true})
     */
    private $rank;

    /**
     * @var string
     *
     * @ORM\Column(name="news_title", type="string", length=255)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="news_comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="news_url", type="string", length=4000, nullable=true)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="news_select", type="smallint", options={"unsigned":true,"default":0})
     */
    private $select = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="link_method", type="boolean", options={"default":false})
     */
    private $link_method = false;

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
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     */
    private $Creator;


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
     * Set date.
     *
     * @param \DateTime|null $date
     *
     * @return News
     */
    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return News
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return News
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return News
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return News
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set select.
     *
     * @param int $select
     *
     * @return News
     */
    public function setSelect($select)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * Get select.
     *
     * @return int
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * Set linkMethod.
     *
     * @param boolean $linkMethod
     *
     * @return News
     */
    public function setLinkMethod($linkMethod)
    {
        $this->link_method = $linkMethod;

        return $this;
    }

    /**
     * Get linkMethod.
     *
     * @return boolean
     */
    public function isLinkMethod()
    {
        return $this->link_method;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return News
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
     * @return News
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

    /**
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     * @return News
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     *
     * @return \Eccube\Entity\Member|null
     */
    public function getCreator()
    {
        return $this->Creator;
    }
}

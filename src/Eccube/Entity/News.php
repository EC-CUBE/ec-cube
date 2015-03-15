<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * News
 */
class News
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $news_date;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var string
     */
    private $news_title;

    /**
     * @var string
     */
    private $news_comment;

    /**
     * @var string
     */
    private $news_url;

    /**
     * @var integer
     */
    private $news_select;

    /**
     * @var string
     */
    private $link_method;

    /**
     * @var integer
     */
    private $creator_id;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var integer
     */
    private $del_flg;

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
     * Set news_date
     *
     * @param \DateTime $newsDate
     * @return News
     */
    public function setNewsDate($newsDate)
    {
        $this->news_date = $newsDate;

        return $this;
    }

    /**
     * Get news_date
     *
     * @return \DateTime 
     */
    public function getNewsDate()
    {
        return $this->news_date;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return News
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set news_title
     *
     * @param string $newsTitle
     * @return News
     */
    public function setNewsTitle($newsTitle)
    {
        $this->news_title = $newsTitle;

        return $this;
    }

    /**
     * Get news_title
     *
     * @return string 
     */
    public function getNewsTitle()
    {
        return $this->news_title;
    }

    /**
     * Set news_comment
     *
     * @param string $newsComment
     * @return News
     */
    public function setNewsComment($newsComment)
    {
        $this->news_comment = $newsComment;

        return $this;
    }

    /**
     * Get news_comment
     *
     * @return string 
     */
    public function getNewsComment()
    {
        return $this->news_comment;
    }

    /**
     * Set news_url
     *
     * @param string $newsUrl
     * @return News
     */
    public function setNewsUrl($newsUrl)
    {
        $this->news_url = $newsUrl;

        return $this;
    }

    /**
     * Get news_url
     *
     * @return string 
     */
    public function getNewsUrl()
    {
        return $this->news_url;
    }

    /**
     * Set news_select
     *
     * @param integer $newsSelect
     * @return News
     */
    public function setNewsSelect($newsSelect)
    {
        $this->news_select = $newsSelect;

        return $this;
    }

    /**
     * Get news_select
     *
     * @return integer 
     */
    public function getNewsSelect()
    {
        return $this->news_select;
    }

    /**
     * Set link_method
     *
     * @param string $linkMethod
     * @return News
     */
    public function setLinkMethod($linkMethod)
    {
        $this->link_method = $linkMethod;

        return $this;
    }

    /**
     * Get link_method
     *
     * @return string 
     */
    public function getLinkMethod()
    {
        return $this->link_method;
    }

    /**
     * Set creator_id
     *
     * @param integer $creatorId
     * @return News
     */
    public function setCreatorId($creatorId)
    {
        $this->creator_id = $creatorId;

        return $this;
    }

    /**
     * Get creator_id
     *
     * @return integer 
     */
    public function getCreatorId()
    {
        return $this->creator_id;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return News
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
     * @param \DateTime $updateDate
     * @return News
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
     * Set del_flg
     *
     * @param integer $delFlg
     * @return News
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
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
     * @return News
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
    /**
     * @ORM\PrePersist
     */
    public function setCreateDateAuto()
    {
        // Add your code here
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDateAuto()
    {
        // Add your code here
    }
}

<?php

namespace Eccube\Entity;

/**
 * Review
 */
class Review extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $reviewer_name;

    /**
     * @var string
     */
    private $reviewer_url;

    /**
     * @var integer
     */
    private $recommend_level;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var integer
     */
    private $status;

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
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\Master\Sex
     */
    private $Sex;

    /**
     * @var \Eccube\Entity\Customer
     */
    private $Customer;

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
     * Set reviewer_name
     *
     * @param  string $reviewerName
     * @return Review
     */
    public function setReviewerName($reviewerName)
    {
        $this->reviewer_name = $reviewerName;

        return $this;
    }

    /**
     * Get reviewer_name
     *
     * @return string
     */
    public function getReviewerName()
    {
        return $this->reviewer_name;
    }

    /**
     * Set reviewer_url
     *
     * @param  string $reviewerUrl
     * @return Review
     */
    public function setReviewerUrl($reviewerUrl)
    {
        $this->reviewer_url = $reviewerUrl;

        return $this;
    }

    /**
     * Get reviewer_url
     *
     * @return string
     */
    public function getReviewerUrl()
    {
        return $this->reviewer_url;
    }

    /**
     * Set recommend_level
     *
     * @param  integer $recommendLevel
     * @return Review
     */
    public function setRecommendLevel($recommendLevel)
    {
        $this->recommend_level = $recommendLevel;

        return $this;
    }

    /**
     * Get recommend_level
     *
     * @return integer
     */
    public function getRecommendLevel()
    {
        return $this->recommend_level;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Review
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set comment
     *
     * @param  string $comment
     * @return Review
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return Review
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Review
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
     * @param  \DateTime $updateDate
     * @return Review
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
     * @param  integer $delFlg
     * @return Review
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
     * Set Product
     *
     * @param  \Eccube\Entity\Product $product
     * @return Review
     */
    public function setProduct(\Eccube\Entity\Product $product)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get Product
     *
     * @return \Eccube\Entity\Product
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * Set Sex
     *
     * @param  \Eccube\Entity\Master\Sex $sex
     * @return Review
     */
    public function setSex(\Eccube\Entity\Master\Sex $sex = null)
    {
        $this->Sex = $sex;

        return $this;
    }

    /**
     * Get Sex
     *
     * @return \Eccube\Entity\Master\Sex
     */
    public function getSex()
    {
        return $this->Sex;
    }

    /**
     * Set Customer
     *
     * @param  \Eccube\Entity\Customer $customer
     * @return Review
     */
    public function setCustomer(\Eccube\Entity\Customer $customer = null)
    {
        $this->Customer = $customer;

        return $this;
    }

    /**
     * Get Customer
     *
     * @return \Eccube\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $creator
     * @return Review
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

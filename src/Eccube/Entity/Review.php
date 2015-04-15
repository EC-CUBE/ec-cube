<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Review
 */
class Review
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $product_id;

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
    private $sex_id;

    /**
     * @var integer
     */
    private $customer_id;

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
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\Customer
     */
    private $Customer;

    /**
     * @var \Eccube\Entity\Sex
     */
    private $Sex;

    /**
     * @var \Eccube\Entity\Recommend
     */
    private $Recommend;

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
     * Set product_id
     *
     * @param integer $productId
     * @return Review
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get product_id
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set reviewer_name
     *
     * @param string $reviewerName
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
     * @param string $reviewerUrl
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
     * Set sex_id
     *
     * @param integer $sexId
     * @return Review
     */
    public function setSexId($sexId)
    {
        $this->sex_id = $sexId;

        return $this;
    }

    /**
     * Get sex_id
     *
     * @return integer 
     */
    public function getSexId()
    {
        return $this->sex_id;
    }

    /**
     * Set customer_id
     *
     * @param integer $customerId
     * @return Review
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
     * Set recommend_level
     *
     * @param integer $recommendLevel
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
     * @param string $title
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
     * @param string $comment
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
     * @param integer $status
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
     * Set creator_id
     *
     * @param integer $creatorId
     * @return Review
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
     * @param \DateTime $updateDate
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
     * @param integer $delFlg
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
     * @param \Eccube\Entity\Product $product
     * @return Review
     */
    public function setProduct(\Eccube\Entity\Product $product = null)
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
     * Set Customer
     *
     * @param \Eccube\Entity\Customer $customer
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
     * Set Sex
     *
     * @param \Eccube\Entity\Sex $sex
     * @return Review
     */
    public function setSex(\Eccube\Entity\Sex $sex = null)
    {
        $this->Sex = $sex;

        return $this;
    }

    /**
     * Get Sex
     *
     * @return \Eccube\Entity\Sex 
     */
    public function getSex()
    {
        return $this->Sex;
    }

    /**
     * Set Recommend
     *
     * @param \Eccube\Entity\Recommend $recommend
     * @return Review
     */
    public function setRecommend(\Eccube\Entity\Recommend $recommend = null)
    {
        $this->Recommend = $recommend;

        return $this;
    }

    /**
     * Get Recommend
     *
     * @return \Eccube\Entity\Recommend 
     */
    public function getRecommend()
    {
        return $this->Recommend;
    }

    /**
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
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

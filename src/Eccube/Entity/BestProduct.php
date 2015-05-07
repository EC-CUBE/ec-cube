<?php

namespace Eccube\Entity;

/**
 * BestProduct
 */
class BestProduct extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $category_id;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $comment;

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
     * Set category_id
     *
     * @param  integer     $categoryId
     * @return BestProduct
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;

        return $this;
    }

    /**
     * Get category_id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Set rank
     *
     * @param  integer     $rank
     * @return BestProduct
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
     * Set title
     *
     * @param  string      $title
     * @return BestProduct
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
     * @param  string      $comment
     * @return BestProduct
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
     * Set create_date
     *
     * @param  \DateTime   $createDate
     * @return BestProduct
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
     * @return BestProduct
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
     * @param  integer     $delFlg
     * @return BestProduct
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
     * @return BestProduct
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
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $creator
     * @return BestProduct
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

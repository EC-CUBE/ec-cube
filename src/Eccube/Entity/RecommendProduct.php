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
 * RecommendProduct
 */
class RecommendProduct extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $product_id;

    /**
     * @var integer
     */
    private $recommend_product_id;

    /**
     * @var integer
     */
    private $rank;

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
     * @var \Eccube\Entity\Product
     */
    private $RecommendedProduct;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;

    /**
     * Set product_id
     *
     * @param  integer          $productId
     * @return RecommendProduct
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
     * Set recommend_product_id
     *
     * @param  integer          $recommendProductId
     * @return RecommendProduct
     */
    public function setRecommendProductId($recommendProductId)
    {
        $this->recommend_product_id = $recommendProductId;

        return $this;
    }

    /**
     * Get recommend_product_id
     *
     * @return integer
     */
    public function getRecommendProductId()
    {
        return $this->recommend_product_id;
    }

    /**
     * Set rank
     *
     * @param  integer          $rank
     * @return RecommendProduct
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
     * Set comment
     *
     * @param  string           $comment
     * @return RecommendProduct
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
     * @param  integer          $status
     * @return RecommendProduct
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
     * @param  \DateTime        $createDate
     * @return RecommendProduct
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
     * @param  \DateTime        $updateDate
     * @return RecommendProduct
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
     * Set RecommendedProduct
     *
     * @param  \Eccube\Entity\Product $recommendedProduct
     * @return RecommendProduct
     */
    public function setRecommendedProduct(\Eccube\Entity\Product $recommendedProduct)
    {
        $this->RecommendedProduct = $recommendedProduct;

        return $this;
    }

    /**
     * Get RecommendedProduct
     *
     * @return \Eccube\Entity\Product
     */
    public function getRecommendedProduct()
    {
        return $this->RecommendedProduct;
    }

    /**
     * Set Product
     *
     * @param  \Eccube\Entity\Product $product
     * @return RecommendProduct
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
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $creator
     * @return RecommendProduct
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

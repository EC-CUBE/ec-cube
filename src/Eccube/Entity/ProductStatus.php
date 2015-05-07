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
 * ProductStatus
 */
class ProductStatus extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $product_status_id;

    /**
     * @var integer
     */
    private $product_id;

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
     * Set product_status_id
     *
     * @param  integer       $productStatusId
     * @return ProductStatus
     */
    public function setProductStatusId($productStatusId)
    {
        $this->product_status_id = $productStatusId;

        return $this;
    }

    /**
     * Get product_status_id
     *
     * @return integer
     */
    public function getProductStatusId()
    {
        return $this->product_status_id;
    }

    /**
     * Set product_id
     *
     * @param  integer       $productId
     * @return ProductStatus
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
     * Set create_date
     *
     * @param  \DateTime     $createDate
     * @return ProductStatus
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
     * @param  \DateTime     $updateDate
     * @return ProductStatus
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
     * @param  integer       $delFlg
     * @return ProductStatus
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
     * @param  \Eccube\Entity\Member $creator
     * @return ProductStatus
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
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\Master\Status
     */
    private $Status;

    /**
     * @var \Eccube\Entity\Master\StatusImage
     */
    private $StatusImage;

    /**
     * Set Product
     *
     * @param  \Eccube\Entity\Product $product
     * @return ProductStatus
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
     * Set Status
     *
     * @param  \Eccube\Entity\Master\Status $status
     * @return ProductStatus
     */
    public function setStatus(\Eccube\Entity\Master\Status $status)
    {
        $this->Status = $status;

        return $this;
    }

    /**
     * Get Status
     *
     * @return \Eccube\Entity\Master\Status
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * Set StatusImage
     *
     * @param  \Eccube\Entity\Master\StatusImage $statusImage
     * @return ProductStatus
     */
    public function setStatusImage(\Eccube\Entity\Master\StatusImage $statusImage)
    {
        $this->StatusImage = $statusImage;

        return $this;
    }

    /**
     * Get StatusImage
     *
     * @return \Eccube\Entity\Master\StatusImage
     */
    public function getStatusImage()
    {
        return $this->StatusImage;
    }
}

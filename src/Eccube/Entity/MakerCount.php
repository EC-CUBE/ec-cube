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
 * MakerCount
 */
class MakerCount extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $maker_id;

    /**
     * @var integer
     */
    private $product_count;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \Eccube\Entity\Maker
     */
    private $Maker;

    /**
     * Get maker_id
     *
     * @return integer
     */
    public function getMakerId()
    {
        return $this->maker_id;
    }

    /**
     * Set product_count
     *
     * @param  integer    $productCount
     * @return MakerCount
     */
    public function setProductCount($productCount)
    {
        $this->product_count = $productCount;

        return $this;
    }

    /**
     * Get product_count
     *
     * @return integer
     */
    public function getProductCount()
    {
        return $this->product_count;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime  $createDate
     * @return MakerCount
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
     * Set Maker
     *
     * @param  \Eccube\Entity\Maker $maker
     * @return MakerCount
     */
    public function setMaker(\Eccube\Entity\Maker $maker = null)
    {
        $this->Maker = $maker;

        return $this;
    }

    /**
     * Get Maker
     *
     * @return \Eccube\Entity\Maker
     */
    public function getMaker()
    {
        return $this->Maker;
    }
}

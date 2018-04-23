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


namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItemType
 *
 * @ORM\Table(name="mtb_order_item_type")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\OrderItemTypeRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class OrderItemType extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /**
     * 商品.
     *
     * @var integer
     */
    const PRODUCT = 1;

    /**
     * 送料.
     *
     * @var integer
     */
    const DELIVERY_FEE = 2;

    /**
     * 手数料.
     *
     * @var integer
     */
    const CHARGE = 3;

    /**
     * 値引き.
     *
     * @var integer
     */
    const DISCOUNT = 4;

    /**
     * 税.
     *
     * @var integer
     */
    const TAX = 5;


    /**
     * 商品かどうか
     *
     * @return bool
     */
    public function isProduct()
    {
        if ($this->id == self::PRODUCT) {
            return true;
        }

        return false;
    }
}

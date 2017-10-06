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
 * ProductStatus
 *
 * @ORM\Table(name="mtb_product_status")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\ProductStatusRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class ProductStatus extends \Eccube\Entity\Master\AbstractMasterEntity
{

    /**
     * 公開
     * @var integer
     */
    const DISPLAY_SHOW = 1;

    /**
     * 非公開
     * @var integer
     */
    const DISPLAY_HIDE = 2;

    /**
     * 廃止
     * @var integer
     */
    const DISPLAY_ABOLISHED = 3;
}

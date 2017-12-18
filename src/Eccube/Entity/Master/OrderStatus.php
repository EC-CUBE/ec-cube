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
 * OrderStatus
 *
 * @ORM\Table(name="mtb_order_status")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\OrderStatusRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class OrderStatus extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /** 新規受付. */
    const NEW = 1;
    /** 入金待ち. */
    const PAY_WAIT = 2;
    /** キャンセル. */
    const CANCEL = 3;
    /** 取り寄せ中. */
    const BACK_ORDER = 4;
    /** 発送済み. */
    const DELIVERED = 5;
    /** 入金済み. */
    const PAID = 6;
    /** 決済処理中. */
    const PENDING = 7;
    /** 購入処理中. */
    const PROCESSING = 8;
}

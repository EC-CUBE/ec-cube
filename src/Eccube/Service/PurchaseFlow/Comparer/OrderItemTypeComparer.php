<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Service\PurchaseFlow\Comparer;

use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\ShipmentItem;

class OrderItemTypeComparer implements ItemComparer
{
    /**
     * {@inheritdoc}
     */
    public function compare(ItemInterface $Item1, ItemInterface $Item2)
    {
        /** @var OrderItemType $OrderItemType1 */
        /** @var OrderItemType $OrderItemType2 */
        $OrderItemType1 = $Item1->getOrderItemType();
        $OrderItemType2 = $Item2->getOrderItemType();

        $order_item_type_id1 = $OrderItemType1 ? (string)$OrderItemType1->getId() : null;
        $order_item_type_id2 = $OrderItemType2 ? (string)$OrderItemType2->getId() : null;

        // FIXME 暫定的に、商品以外は全て別商品とする
        return
            $order_item_type_id1 === (string)OrderItemType::PRODUCT &&
            $order_item_type_id2 === (string)OrderItemType::PRODUCT;
    }
}

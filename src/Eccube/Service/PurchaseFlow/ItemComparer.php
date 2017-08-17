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

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;

abstract class ItemComparer implements ItemProcessor
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @return ProcessResult
     */
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        try {
            $this->validate($item, $context);
            return ProcessResult::success();

        } catch (ItemAlreadyExistsException $e) {

            if ($item instanceof CartItem) {
                $this->handleCartItem($item, $context);
            }

            return ProcessResult::warn($e->getMessage(), $e->getMessageArgs());
        }
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     * @throws ItemAlreadyExistsException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        foreach ($context->getOriginHolder()->getItems() as $originItem) {
            if ($this->areSameItems($item, $originItem)) {
                throw new ItemAlreadyExistsException('cart.item.exists');
            }
        }
    }

    /**
     * @param ItemInterface $item1
     * @param ItemInterface $item2
     * @return bool
     */
    protected function areSameItems(ItemInterface $item1, ItemInterface $item2)
    {
        return false;
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     */
    protected function handleCartItem(ItemInterface $item, PurchaseContext $context)
    {
        foreach ($context->getOriginHolder()->getItems() as $originItem) {
            if ($this->areSameItems($item, $originItem)) {
                $originItem->setQuantity($originItem->getQuantity() + $item->getQuantity());
                break;
            }
        }
    }
}

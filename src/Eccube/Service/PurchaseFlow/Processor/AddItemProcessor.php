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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\Comparer\ItemComparer;
use Eccube\Service\PurchaseFlow\ItemProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

class AddItemProcessor implements ItemProcessor
{
    private $comparer;

    /**
     * @param ItemComparer $comparer
     */
    public function __construct(ItemComparer $comparer)
    {
        $this->comparer = $comparer;
    }

    /**
     * @return ItemComparer
     */
    public function getComparer()
    {
        return $this->comparer;
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @return ProcessResult
     */
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        $holder = $context->getOriginHolder();

        foreach ($context->getOriginHolder()->getItems() as $originItem) {
            if ($this->getComparer()->compare($originItem, $item)) {
                $originItem->setQuantity($originItem->getQuantity() + $item->getQuantity());
                return ProcessResult::warn('cart.item.exists');
            }
        }

        $holder->addItem($item);
        return ProcessResult::success();
    }
}

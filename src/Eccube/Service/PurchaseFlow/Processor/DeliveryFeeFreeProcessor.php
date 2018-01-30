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

use Eccube\Entity\BaseInfo;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\ItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 送料無料条件.
 */
class DeliveryFeeFreeProcessor implements ItemHolderProcessor
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * DeliveryFeeProcessor constructor.
     *
     * @param BaseInfo $BaseInfo
     */
    public function __construct(BaseInfo $BaseInfo)
    {
        $this->BaseInfo = $BaseInfo;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext     $context
     *
     * @return ProcessResult
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $isDeliveryFree = false;

        if ($this->BaseInfo->getDeliveryFreeAmount()) {
            if ($this->BaseInfo->getDeliveryFreeAmount() <= $itemHolder->getTotal()) {
                // 送料無料（金額）を超えている
                $isDeliveryFree = true;
            }
        }

        if ($this->BaseInfo->getDeliveryFreeQuantity()) {
            if ($this->BaseInfo->getDeliveryFreeQuantity() <= $itemHolder->getQuantity()) {
                // 送料無料（個数）を超えている
                $isDeliveryFree = true;
            }
        }

        // 送料無料条件に合致した場合は、送料明細の個数を0に設定
        if ($isDeliveryFree) {
            $items = $itemHolder->getItems();
            foreach ($items as $item) {
                if ($item->isDeliveryFee()) {
                    $item->setQuantity(0);
                }
            }
        }

        return ProcessResult::success();
    }
}

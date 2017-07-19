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


use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\ItemValidateException;
use Eccube\Service\PurchaseFlow\ValidatableItemHolderProcessor;

/**
 * 購入金額上限チェック.
 */
class PaymentTotalLimitValidator extends ValidatableItemHolderProcessor
{
    /**
     * @var int
     */
    private $maxTotalFee;

    /**
     * PaymentTotalLimitValidator constructor.
     * @param $maxTotalFee
     */
    public function __construct($maxTotalFee)
    {
        $this->maxTotalFee = $maxTotalFee;
    }

    protected function validate(ItemHolderInterface $item, PurchaseContext $context)
    {
        $totalPrice = $item->getTotal();
        if ($totalPrice > $this->maxTotalFee) {
            throw new ItemValidateException('cart.over.price_limit');
        }
    }
}
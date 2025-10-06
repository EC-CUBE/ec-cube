<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 合計金額のマイナスチェック.
 */
class PaymentTotalNegativeValidator extends ItemHolderPostValidator
{
    /**
     * @param ItemHolderInterface $itemHolder カート or 注文
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @return void
     *
     * @throws InvalidItemException 合計金額がマイナスの場合
     */
    #[\Override]
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        if ($itemHolder->getTotal() < 0) {
            $this->throwInvalidItemException(trans('front.shopping.payment_total_invalid'));
        }
    }
}

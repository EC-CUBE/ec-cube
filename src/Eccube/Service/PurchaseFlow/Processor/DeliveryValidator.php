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
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 配送方法に一貫性がない商品がないか確認してください。
 */
class DeliveryValidator extends ItemHolderValidator
{
    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // 明細の個数が1以下の場合はOK
        if (count($itemHolder->getItems()) <= 1) {
            return;
        }

        if ($itemHolder instanceof Order) {
            if (null === $itemHolder->getShippings()) {
                return;
            }
        }

        /** @var Shipping $Shipping */
        foreach ($itemHolder->getShippings() as $Shipping) {
            if (false === $Shipping->getDelivery()->isVisible()) {
                $this->throwInvalidItemException('front.shopping.not_available_delivery_method');
            }
        }
    }
}

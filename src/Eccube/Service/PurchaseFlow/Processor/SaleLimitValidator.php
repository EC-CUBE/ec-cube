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

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 販売制限数チェック.
 */
class SaleLimitValidator extends ItemValidator
{
    /**
     * @param ItemInterface $item 商品
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @return void
     *
     * @throws InvalidItemException 販売制限数を超えている場合
     */
    #[\Override]
    protected function validate(ItemInterface $item, PurchaseContext $context): void
    {
        if (!$item->isProduct()) {
            return;
        }

        $limit = $item->getProductClass()->getSaleLimit();
        if (is_null($limit)) {
            return;
        }

        $quantity = $item->getQuantity();
        if ($limit < $quantity) {
            $this->throwInvalidItemException('front.shopping.over_sale_limit', $item->getProductClass());
        }
    }

    /**
     * @param ItemInterface $item 商品
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @return void
     */
    #[\Override]
    protected function handle(ItemInterface $item, PurchaseContext $context): void
    {
        $limit = $item->getProductClass()->getSaleLimit();
        $item->setQuantity($limit);
    }
}

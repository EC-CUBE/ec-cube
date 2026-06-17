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
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 注文確定時に、商品の受注管理用メモを商品明細(OrderItem)へコピーする.
 *
 * 確定フロー(flow_type: shopping)でのみ実行することで、確定時点のメモを
 * 受注明細にスナップショットとして保持する(確定後に商品側メモを変更しても
 * 既存受注には影響しない)。
 */
class OrderMemoPreprocessor implements ItemHolderPreprocessor
{
    /**
     * @param ItemHolderInterface $itemHolder 受注 or カート
     * @param PurchaseContext $context 購入フローのコンテキスト
     */
    #[\Override]
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        foreach ($itemHolder->getOrderItems() as $OrderItem) {
            // 商品明細のみ対象(送料・手数料・値引き等は対象外)
            if (!$OrderItem->isProduct()) {
                continue;
            }

            $Product = $OrderItem->getProduct();
            $OrderItem->setOrderMemo($Product?->getOrderMemo());
        }
    }
}

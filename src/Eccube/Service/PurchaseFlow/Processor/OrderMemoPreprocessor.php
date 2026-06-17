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
 * 受注の作成・編集時に、商品の受注管理用メモを商品明細(OrderItem)のメモへ追記する.
 *
 * 確定フロー(shopping)・受注フロー(order)の双方で実行し、商品明細のメモに
 * 商品側メモの文言がまだ含まれていない場合のみ追記する(冪等)。既存のメモは
 * 消さずに追記するため、複数回フローが走っても・受注を編集し直しても重複しない。
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

            $productMemo = $OrderItem->getProduct()?->getOrderMemo();
            if ($productMemo === null || $productMemo === '') {
                continue;
            }

            $current = $OrderItem->getOrderMemo();
            // 同一文言が既に含まれていれば追記しない(冪等)
            if ($current !== null && str_contains($current, $productMemo)) {
                continue;
            }

            $OrderItem->setOrderMemo(
                ($current === null || $current === '') ? $productMemo : $current."\n".$productMemo
            );
        }
    }
}

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
 * 注文確定時(shopping フロー)に、商品の受注管理用メモを商品明細(OrderItem)へ
 * スナップショットとしてコピーする.
 *
 * 確定時点の商品メモで常に上書きするため、確定後に商品側メモを変更しても既存受注
 * 明細のメモは変わらない(Issue #6821 §2/§3.2/§8)。コピーは確定フロー(shopping)
 * でのみ行い、確定後の受注(order フロー)では再コピーしない。
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
            if ($Product === null) {
                continue;
            }

            // 確定時点の商品メモを明細へスナップショットとしてコピー(常に上書き).
            // メモが未設定(null)の商品では明細メモも null になる.
            $OrderItem->setOrderMemo($Product->getOrderMemo());
        }
    }
}

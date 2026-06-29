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
use Eccube\Util\StringUtil;

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

            // 入力経路差(フォーム保存は trim なし / CSV 取込は trimAll)による行不一致で
            // 重複追記されるのを防ぐため、商品メモは CSV と同じ trimAll で正規化してから判定する.
            $productMemo = (string) StringUtil::trimAll((string) ($OrderItem->getProduct()?->getOrderMemo() ?? ''));
            if ($productMemo === '') {
                continue;
            }

            $current = $OrderItem->getOrderMemo();
            $existingLines = ($current === null || $current === '') ? [] : explode("\n", $current);

            // 商品メモの各行のうち, 既存メモに「行」として未だ含まれていないものだけを追記する(冪等).
            // 商品メモをブロック単位で照合すると, 複数行の商品メモの一部行のみが先に含まれる場合に
            // ブロック全体を再追記し行が重複する(例: 既存="A", 商品メモ="A\nB" → "A\nA\nB").
            // 行単位で差分追記することで, 何度フローが走っても・商品メモを編集し直しても重複しない.
            $appendLines = [];
            foreach (explode("\n", $productMemo) as $line) {
                if (!in_array($line, $existingLines, true)) {
                    $appendLines[] = $line;
                }
            }

            if ($appendLines === []) {
                continue;
            }

            $OrderItem->setOrderMemo(implode("\n", array_merge($existingLines, $appendLines)));
        }
    }
}

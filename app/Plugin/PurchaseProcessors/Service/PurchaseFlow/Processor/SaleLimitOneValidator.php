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

namespace Plugin\PurchaseProcessors\Service\PurchaseFlow\Processor;

use Eccube\Annotation\CartFlow;
use Eccube\Annotation\OrderFlow;
use Eccube\Annotation\ShoppingFlow;
use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ItemValidator;

/**
 * 商品を１個のみしか購入できないようにするサンプル
 *
 * # 使い方
 * PurchaseFlowに新しいProcessorを追加する
 *
 * ## 追加できるプロセッサ
 * 以下のクラスまたはインタフェースを継承または実装している必要がある
 * * ItemPreprocessor
 * * ItemValidator
 * * ItemHolderPreprocessor
 * * ItemHolderValidator
 * * PurchaseProcessor
 *
 * ## 追加対象のフローの指定方法
 * * カートのPurchaseFlowにProcessorを追加する場合はCartFlowアノテーションを追加
 * * 購入フローのPurchaseFlowにProcessorを追加する場合はShoppingFlowアノテーションを追加
 * * 管理画面でのPurchaseFlowにProcessorを追加する場合はOrderFlowアノテーションを追加
 *
 * @CartFlow
 * @ShoppingFlow
 * @OrderFlow
 */
class SaleLimitOneValidator extends ItemValidator
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item->isProduct()) {
            return;
        }

        $quantity = $item->getQuantity();
        if (1 < $quantity) {
            $this->throwInvalidItemException('商品は１個しか購入できません。');
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $item->setQuantity(1);
    }
}

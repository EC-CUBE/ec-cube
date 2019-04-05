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

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\ItemHolderInterface;

/**
 * 値引きを制御するプロセッサ.
 *
 * 値引きを扱う場合, 合計金額以上の値引きを追加しないように留意する必要があります.
 *
 * PurchaseFlowは, DiscountProcessor::removeDiscountItemを最初に呼びだし,
 * 値引き明細がすべてクリアされた状態でDiscountProcessor::addDiscountItemを呼び出します.
 *
 * addDiscountItemが呼ばれるごとにPurchaseFlowは合計金額を集計します.
 * addDiscountItemでは, 合計金額のチェックを行い, 追加できる範囲で値引き明細を追加するようにしてください.
 */
interface DiscountProcessor
{
    /**
     * 値引き明細の削除処理を実装します.
     *
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     */
    public function removeDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context);

    /**
     * 値引き明細の追加処理を実装します.
     *
     * かならず合計金額等のチェックを行い, 超える場合は利用できる金額まで丸めるか、もしくは明細の追加処理をスキップしてください.
     * 正常に追加できない場合は, ProcessResult::warnを返却してください.
     *
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @return ProcessResult|null
     */
    public function addDiscountItem(ItemHolderInterface $itemHolder, PurchaseContext $context);
}

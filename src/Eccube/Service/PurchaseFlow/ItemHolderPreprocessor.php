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
 * 受注データのバリデーションを行う前に、受注データの調整を行います。
 *
 * Interface ItemHolderPreprocessor
 */
interface ItemHolderPreprocessor
{
    /**
     * 受注データ調整処理。
     *
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext     $context
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context);
}

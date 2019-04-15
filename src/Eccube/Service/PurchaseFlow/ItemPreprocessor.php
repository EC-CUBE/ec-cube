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

use Eccube\Entity\ItemInterface;

/**
 * 明細単位の前処理行うインターフェス.
 */
interface ItemPreprocessor
{
    /**
     * @param ItemInterface   $item
     * @param PurchaseContext $context
     */
    public function process(ItemInterface $item, PurchaseContext $context);
}

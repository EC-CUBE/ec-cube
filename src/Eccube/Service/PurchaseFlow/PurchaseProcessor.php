<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\ItemHolderInterface;

interface PurchaseProcessor
{
    /**
     * @param ItemHolderInterface $target
     * @param PurchaseContext     $context
     *
     * @throws PurchaseException
     */
    public function process(ItemHolderInterface $target, PurchaseContext $context);
}

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

use Eccube\Entity\ProductClass;

trait ValidatorTrait
{
    /**
     * @param ProductClass $ProductClass
     * @param $errorCode
     *
     * @throws InvalidItemException
     */
    protected function throwInvalidItemException($errorCode, ProductClass $ProductClass = null, $warning = false)
    {
        if ($ProductClass) {
            $productName = $ProductClass->getProduct()->getName();
            if ($ProductClass->hasClassCategory1()) {
                $productName .= ' - '.$ProductClass->getClassCategory1()->getName();
            }
            if ($ProductClass->hasClassCategory2()) {
                $productName .= ' - '.$ProductClass->getClassCategory2()->getName();
            }

            throw new InvalidItemException(trans($errorCode, ['%product%' => $productName]), null, $warning);
        }
        throw new InvalidItemException(trans($errorCode), null, $warning);
    }
}

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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 購入金額上限チェック.
 */
class PaymentTotalLimitValidator extends ItemHolderPostValidator
{
    /**
     * @var int
     */
    private $maxTotalFee;

    /**
     * PaymentTotalLimitValidator constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->maxTotalFee = $eccubeConfig['eccube_max_total_fee'];
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws \Eccube\Service\PurchaseFlow\InvalidItemException
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $totalPrice = $itemHolder->getTotal();
        if ($totalPrice > $this->maxTotalFee) {
            $this->throwInvalidItemException('front.shopping.over_price_limit');
        }
    }
}

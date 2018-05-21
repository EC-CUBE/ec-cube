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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\ValidatableItemHolderProcessor;

/**
 * 購入金額上限チェック.
 */
class PaymentTotalLimitValidator extends ValidatableItemHolderProcessor
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
     * @param ItemHolderInterface $item
     * @param PurchaseContext $context
     *
     * @throws \Eccube\Service\PurchaseFlow\InvalidItemException
     */
    protected function validate(ItemHolderInterface $item, PurchaseContext $context)
    {
        $totalPrice = $item->getTotal();
        if ($totalPrice > $this->maxTotalFee) {
            $this->throwInvalidItemException('cart.over.price_limit');
        }
    }
}

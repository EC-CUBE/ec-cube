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
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PointHelper;

/**
 * ポイント利用状況やポイント利用設定の確認
 */
class PointSettingChangeValidator extends ItemHolderValidator
{
    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */

    /**
     * @var PointHelper
     */
    protected $pointHelper;

    /**
     * PointSettingChangeValidator constructor.
     *
     * @param PointHelper $pointHelper
     */
    public function __construct(PointHelper $pointHelper)
    {
        $this->pointHelper = $pointHelper;
    }

    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        /** @var Order $originHolder */
        $originHolder = $context->getOriginHolder();

        // 受注の生成直後はチェックしない
        if (!$originHolder->getOrderNo()) {
            return;
        }

        //「ポイント機能」の設定と使用中のポイントを確認する
        if (!$this->pointHelper->isPointEnabled() && $itemHolder->getUsePoint() > 0) {
            $this->throwInvalidItemException('front.shopping.point_setting_change');
        }
    }
}

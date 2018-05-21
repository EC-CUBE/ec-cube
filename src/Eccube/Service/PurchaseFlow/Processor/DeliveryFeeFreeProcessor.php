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

use Eccube\Entity\BaseInfo;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\ItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 送料無料条件.
 */
class DeliveryFeeFreeProcessor implements ItemHolderProcessor
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * DeliveryFeeProcessor constructor.
     *
     * @param BaseInfo $BaseInfo
     */
    public function __construct(BaseInfo $BaseInfo)
    {
        $this->BaseInfo = $BaseInfo;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext     $context
     *
     * @return ProcessResult
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $isDeliveryFree = false;

        if ($this->BaseInfo->getDeliveryFreeAmount()) {
            if ($this->BaseInfo->getDeliveryFreeAmount() <= $itemHolder->getTotal()) {
                // 送料無料（金額）を超えている
                $isDeliveryFree = true;
            }
        }

        if ($this->BaseInfo->getDeliveryFreeQuantity()) {
            if ($this->BaseInfo->getDeliveryFreeQuantity() <= $itemHolder->getQuantity()) {
                // 送料無料（個数）を超えている
                $isDeliveryFree = true;
            }
        }

        // 送料無料条件に合致した場合は、送料明細の個数を0に設定
        if ($isDeliveryFree) {
            $items = $itemHolder->getItems();
            foreach ($items as $item) {
                if ($item->isDeliveryFee()) {
                    $item->setQuantity(0);
                }
            }
        }

        return ProcessResult::success();
    }
}

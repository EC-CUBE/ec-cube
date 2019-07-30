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

use Eccube\Entity\BaseInfo;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 送料無料条件を適用します.
 */
class DeliveryFeeFreePreprocessor implements ItemHolderPreprocessor
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * DeliveryFeeProcessor constructor.
     *
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(BaseInfoRepository $baseInfoRepository)
    {
        $this->BaseInfo = $baseInfoRepository->get();
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext     $context
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
    }
}

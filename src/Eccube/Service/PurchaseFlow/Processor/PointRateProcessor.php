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
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 購入フローで、明細に対してポイント付与率を設定する
 * ポイント明細の追加後、ポイント加算の計算前に実行する必要がある
 */
class PointRateProcessor extends ItemHolderPostValidator
{
    /**
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    public function __construct(BaseInfoRepository $baseInfoRepository)
    {
        $this->baseInfoRepository = $baseInfoRepository;
    }

    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        foreach ($itemHolder->getOrderItems() as $item) {
            if ($item->isProduct() && $item->getProductClass()->getPointRate()) {
                $item->setPointRate($item->getProductClass()->getPointRate());
            } else {
                $item->setPointRate($this->baseInfoRepository->get()->getBasicPointRate());
            }
        }
    }
}

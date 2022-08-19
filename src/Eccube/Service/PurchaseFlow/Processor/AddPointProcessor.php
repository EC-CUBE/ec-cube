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
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 加算ポイント.
 */
class AddPointProcessor extends ItemHolderPostValidator
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * AddPointProcessor constructor.
     *
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(BaseInfoRepository $baseInfoRepository)
    {
        $this->BaseInfo = $baseInfoRepository->get();
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     */
    public function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$this->supports($itemHolder)) {
            return;
        }

        // 付与ポイントを計算
        $addPoint = $this->calculateAddPoint($itemHolder);
        $itemHolder->setAddPoint($addPoint);
    }

    /**
     * 付与ポイントを計算.
     *
     * @param ItemHolderInterface $itemHolder
     *
     * @return int
     */
    private function calculateAddPoint(ItemHolderInterface $itemHolder)
    {
        $basicPointRate = $this->BaseInfo->getBasicPointRate();

        // 明細ごとのポイントを集計
        $totalPoint = array_reduce($itemHolder->getItems()->toArray(),
            function ($carry, ItemInterface $item) use ($basicPointRate) {

                $pointRate = $item->getPointRate() ? $item->getPointRate() : null;

                if ($pointRate === null) {
                    $pointRate = $basicPointRate;
                }

                // TODO: ポイントは税抜き分しか割引されない、ポイント明細は税抜きのままでいいのか？
                $point = 0;
                if ($item->isPoint()) {
                    $point = round($item->getPrice() * ($pointRate / 100)) * $item->getQuantity();
                // Only calc point on product
                } elseif ($item->isProduct()) {
                    // ポイント = 単価 * ポイント付与率 * 数量
                    $point = round($item->getPrice() * ($pointRate / 100)) * $item->getQuantity();
                } elseif ($item->isDiscount()) {
                    $point = round($item->getPrice() * ($pointRate / 100)) * $item->getQuantity();
                }

                return $carry + $point;
            }, 0);

        return $totalPoint < 0 ? 0 : $totalPoint;
    }

    /**
     * Processorが実行出来るかどうかを返す.
     *
     * 以下を満たす場合に実行できる.
     *
     * - ポイント設定が有効であること.
     * - $itemHolderがOrderエンティティであること.
     * - 会員のOrderであること.
     *
     * @param ItemHolderInterface $itemHolder
     *
     * @return bool
     */
    private function supports(ItemHolderInterface $itemHolder)
    {
        if (!$this->BaseInfo->isOptionPoint()) {
            return false;
        }

        if (!$itemHolder instanceof Order) {
            return false;
        }

        if (!$itemHolder->getCustomer()) {
            return false;
        }

        return true;
    }
}

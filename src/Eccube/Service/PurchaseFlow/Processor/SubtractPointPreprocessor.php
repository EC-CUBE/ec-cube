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
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 利用ポイントに応じてポイントを減算する.
 *
 * 例) ポイント付与率10%で、1000円分購入したとき
 * ポイント利用なし -> 1000円 * 10% = 100ポイント付与
 * 500ポイント利用して購入 -> (1000円 - 500p) * 10% = 50ポイント付与
 */
class SubtractPointPreprocessor implements ItemHolderPreprocessor
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * SubstractPointProcessor constructor.
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
        /** @var Order $Order */
        $Order = $itemHolder;
        if ($Order->getUsePoint() > 0) {
            $Order->setAddPoint($this->subtract($Order->getAddPoint(), $Order->getUsePoint(), $this->BaseInfo->getBasicPointRate()));
        }

        return ProcessResult::success();
    }

    /**
     * Subtract point.
     *
     * @param integer $totalPoint 合計ポイント
     * @param integer $usePoint 利用ポイント
     * @param integer $pointRate ポイント付与率(%)
     *
     * @return integer Point after subtraction
     */
    protected function subtract($totalPoint, $usePoint, $pointRate)
    {
        $add_point = $totalPoint - intval($usePoint * ($pointRate / 100));

        return $add_point < 0 ? 0 : $add_point;
    }
}

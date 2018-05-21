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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\ItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 加算ポイント.
 */
class AddPointProcessor implements ItemHolderProcessor
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * AddPointProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param BaseInfo $BaseInfo
     */
    public function __construct(EntityManagerInterface $entityManager, BaseInfo $BaseInfo)
    {
        $this->entityManager = $entityManager;
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
        $addPoint = 0;
        foreach ($itemHolder->getItems() as $item) {
            $rate = $item->getPointRate();
            if ($rate === null) {
                $rate = $this->BaseInfo->getBasicPointRate();
            }
            $addPoint += $this->priceToAddPoint($rate, $item->getPriceIncTax(), $item->getQuantity());
        }
        $itemHolder->setAddPoint($addPoint);

        return ProcessResult::success();
    }

    /**
     * 単価と数量から加算ポイントに換算する.
     *
     * @param integer $pointRate ポイント付与率(%)
     * @param integer $price 単価
     * @param integer $quantity 数量
     *
     * @return integer additional point
     */
    protected function priceToAddPoint($pointRate, $price, $quantity)
    {
        return round($price * ($pointRate / 100)) * $quantity;
    }
}

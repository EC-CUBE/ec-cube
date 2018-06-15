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

use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ProductClass;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\PurchaseFlow\ItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

class StockMultipleValidator implements ItemHolderProcessor
{
    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * StockProcessor constructor.
     *
     * @param ProductClassRepository $productClassRepository
     */
    public function __construct(ProductClassRepository $productClassRepository)
    {
        $this->productClassRepository = $productClassRepository;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @return ProcessResult
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $OrderItemsByProductClass = [];
        foreach ($itemHolder->getItems() as $Item) {
            if ($Item->isProduct()) {
                $id = $Item->getProductClass()->getId();
                $OrderItemsByProductClass[$id][] = $Item;
            }
        }

        foreach ($OrderItemsByProductClass as $id => $Items) {
            $ProductClass = $this->productClassRepository->find($id);
            if ($ProductClass->isStockUnlimited()) {
                continue;
            }
            $stock = $ProductClass->getStock();
            if ($stock === 0) {
                return ProcessResult::error(trans('cart.zero.stock',
                    ['%product%' => $this->formatProductName($ProductClass)]));
            }
            $total = 0;
            foreach ($Items as $Item) {
                $total += $Item->getQuantity();
                if ($stock < $total) {
                    return ProcessResult::warn(trans('cart.over.stock',
                        ['%product%' => $this->formatProductName($ProductClass)]));
                }
            }
        }

        return ProcessResult::success();
    }

    protected function formatProductName(ProductClass $ProductClass)
    {
        $productName = $ProductClass->getProduct()->getName();
        if ($ProductClass->hasClassCategory1()) {
            $productName .= ' - '.$ProductClass->getClassCategory1()->getName();
        }
        if ($ProductClass->hasClassCategory2()) {
            $productName .= ' - '.$ProductClass->getClassCategory2()->getName();
        }

        return $productName;
    }
}

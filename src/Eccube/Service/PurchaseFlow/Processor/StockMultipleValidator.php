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
use Eccube\Entity\Shipping;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

class StockMultipleValidator extends ItemHolderValidator
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
     * @throws \Eccube\Service\PurchaseFlow\InvalidItemException
     */
    public function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $OrderItemsByProductClass = [];
        /** @var Shipping $Shipping */
        foreach ($itemHolder->getShippings() as $Shipping) {
            foreach ($Shipping->getOrderItems() as $Item) {
                if ($Item->isProduct()) {
                    $id = $Item->getProductClass()->getId();
                    $OrderItemsByProductClass[$id][] = $Item;
                }
            }
        }

        foreach ($OrderItemsByProductClass as $id => $Items) {
            /** @var ProductClass $ProductClass */
            $ProductClass = $this->productClassRepository->find($id);
            if ($ProductClass->isStockUnlimited()) {
                continue;
            }
            $stock = $ProductClass->getStock();

            if ($stock == 0) {
                foreach ($Items as $Item) {
                    $Item->setQuantity(0);
                }
                $this->throwInvalidItemException('front.shopping.out_of_stock_zero', $ProductClass, true);
            }
            $isOver = false;
            foreach ($Items as $Item) {
                if ($stock - $Item->getQuantity() >= 0) {
                    $stock = $stock - $Item->getQuantity();
                } else {
                    $Item->setQuantity($stock);
                    $stock = 0;
                    $isOver = true;
                }
            }
            if ($isOver) {
                $this->throwInvalidItemException('front.shopping.out_of_stock', $ProductClass, true);
            }
        }
    }
}

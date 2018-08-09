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
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

class SaleLimitMultipleValidator extends ItemHolderValidator
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
        foreach ($itemHolder->getItems() as $Item) {
            if ($Item->isProduct()) {
                $id = $Item->getProductClass()->getId();
                $OrderItemsByProductClass[$id][] = $Item;
            }
        }

        foreach ($OrderItemsByProductClass as $id => $Items) {
            $ProductClass = $this->productClassRepository->find($id);
            $limit = $ProductClass->getSaleLimit();
            if (null === $limit) {
                continue;
            }
            $total = 0;
            foreach ($Items as $Item) {
                $total += $Item->getQuantity();
                if ($limit < $total) {
                    $this->throwInvalidItemException('cart.over.sale_limit', $ProductClass, true);
                }
            }
        }
    }
}

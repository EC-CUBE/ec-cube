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
            $isOver = false;
            foreach ($Items as $Item) {
                if ($limit - $Item->getQuantity() >= 0) {
                    $limit = $limit - $Item->getQuantity();
                } else {
                    $Item->setQuantity($limit);
                    $limit = 0;
                    $isOver = true;
                }
            }
            if ($isOver) {
                $this->throwInvalidItemException('front.shopping.over_sale_limit', $ProductClass, true);
            }
        }
    }
}

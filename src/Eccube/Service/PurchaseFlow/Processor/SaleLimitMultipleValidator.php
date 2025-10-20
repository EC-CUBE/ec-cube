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
use Eccube\Entity\ProductClass;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\PurchaseFlow\InvalidItemException;
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
     * @param ItemHolderInterface $itemHolder 商品
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @return void
     *
     * @throws InvalidItemException 商品の購入数が在庫数を超えている場合
     */
    #[\Override]
    public function validate(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        $OrderItemsByProductClass = [];
        foreach ($itemHolder->getItems() as $Item) {
            if ($Item->isProduct()) {
                $id = $Item->getProductClass()->getId();
                $OrderItemsByProductClass[$id][] = $Item;
            }
        }

        foreach ($OrderItemsByProductClass as $id => $Items) {
            /** @var ProductClass $ProductClass */
            $ProductClass = $this->productClassRepository->find($id);
            $limit = $ProductClass->getSaleLimit();
            if (null === $limit) {
                continue;
            }
            $isOver = false;
            foreach ($Items as $Item) {
                if (bcsub($limit, $Item->getQuantity()) >= 0) {
                    $limit = bcsub($limit, $Item->getQuantity());
                } else {
                    $Item->setQuantity($limit);
                    $limit = '0';
                    $isOver = true;
                }
            }
            if ($isOver) {
                $this->throwInvalidItemException('front.shopping.over_sale_limit', $ProductClass, true);
            }
        }
    }
}

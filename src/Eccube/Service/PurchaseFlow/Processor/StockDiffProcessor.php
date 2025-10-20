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
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;

/**
 * 編集前/編集後の個数の差分にもとづいて在庫を更新します.
 */
class StockDiffProcessor extends ItemHolderValidator implements PurchaseProcessor
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
     * @return void
     *
     * @throws InvalidItemException
     */
    #[\Override]
    public function validate(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        if (is_null($context->getOriginHolder())) {
            return;
        }

        /** @var Order $From */
        $From = $context->getOriginHolder();
        /** @var Order $To */
        $To = $itemHolder;
        $diff = $this->getDiffOfQuantities($From, $To);

        foreach ($diff as $id => $quantity) {
            /** @var ProductClass $ProductClass */
            $ProductClass = $this->productClassRepository->find($id);
            if ($ProductClass->isStockUnlimited()) {
                continue;
            }

            $stock = $ProductClass->getStock();
            $Items = $To->getProductOrderItems();
            $Items = array_filter($Items, function ($Item) use ($id) {
                return $Item->getProductClass()->getId() == $id;
            });
            $toQuantity = array_reduce($Items, function ($quantity, $Item) {
                return $quantity = bcadd($quantity, $Item->getQuantity());
            }, 0);

            // ステータスをキャンセルに変更した場合
            if ($To->getOrderStatus() && $To->getOrderStatus()->getId() == OrderStatus::CANCEL
                && $From->getOrderStatus() && $From->getOrderStatus()->getId() != OrderStatus::CANCEL) {
                if (bcadd((string) $stock, $toQuantity) < 0) {
                    $this->throwInvalidItemException(trans('purchase_flow.over_stock', ['%name%' => $ProductClass->formattedProductName()]));
                }
            // ステータスをキャンセルから対応中に変更した場合
            } elseif ($To->getOrderStatus() && $To->getOrderStatus()->getId() == OrderStatus::IN_PROGRESS
                && $From->getOrderStatus() && $From->getOrderStatus()->getId() == OrderStatus::CANCEL) {
                if (bcsub((string) $stock, $toQuantity) < 0) {
                    $this->throwInvalidItemException(trans('purchase_flow.over_stock', ['%name%' => $ProductClass->formattedProductName()]));
                }
            } else {
                if (bcsub((string) $stock, (string) $quantity) < 0) {
                    $this->throwInvalidItemException(trans('purchase_flow.over_stock', ['%name%' => $ProductClass->formattedProductName()]));
                }
            }
        }
    }

    /**
     * @param ItemHolderInterface $From
     * @param ItemHolderInterface $To
     *
     * @return array<int, string> 商品クラスIDをキーとした商品の数量の差分
     */
    protected function getDiffOfQuantities(ItemHolderInterface $From, ItemHolderInterface $To): array
    {
        $FromItems = $this->getQuantityByProductClass($From);
        $ToItems = $this->getQuantityByProductClass($To);
        $ids = array_unique(array_merge(array_keys($FromItems), array_keys($ToItems)));

        $diff = [];
        foreach ($ids as $id) {
            // 更新された明細
            if (isset($FromItems[$id]) && isset($ToItems[$id])) {
                // 2 -> 3 = +1
                // 2 -> 1 = -1
                $diff[$id] = (string) ($ToItems[$id] - $FromItems[$id]);
            } // 削除された明細
            elseif (isset($FromItems[$id]) && empty($ToItems[$id])) {
                // 2 -> 0 = -2
                $diff[$id] = (string) ($FromItems[$id] * -1);
            } // 追加された明細
            elseif (!isset($FromItems[$id]) && isset($ToItems[$id])) {
                // 0 -> 2 = +2
                $diff[$id] = (string) $ToItems[$id];
            }
        }

        return $diff;
    }

    /**
     * @param ItemHolderInterface $ItemHolder 受注 or カート
     *
     * @return array<int, int> 商品クラスIDをキーとした商品の数量
     */
    protected function getQuantityByProductClass(ItemHolderInterface $ItemHolder): array
    {
        $ItemsByProductClass = [];
        foreach ($ItemHolder->getItems() as $Item) {
            if ($Item->isProduct()) {
                $id = $Item->getProductClass()->getId();
                if (isset($ItemsByProductClass[$id])) {
                    $ItemsByProductClass[$id] += $Item->getQuantity();
                } else {
                    $ItemsByProductClass[$id] = $Item->getQuantity();
                }
            }
        }

        return $ItemsByProductClass;
    }

    /**
     * 受注の仮確定処理を行います。
     *
     * @param ItemHolderInterface $target 受注 or カート
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @return void
     */
    #[\Override]
    public function prepare(ItemHolderInterface $target, PurchaseContext $context): void
    {
        if (is_null($context->getOriginHolder())) {
            return;
        }

        $diff = $this->getDiffOfQuantities($context->getOriginHolder(), $target);

        foreach ($diff as $id => $quantity) {
            /** @var ProductClass $ProductClass */
            $ProductClass = $this->productClassRepository->find($id);
            if ($ProductClass->isStockUnlimited()) {
                continue;
            }

            $stock = $ProductClass->getStock() !== null ? bcsub((string) $ProductClass->getStock(), $quantity) : null;
            $ProductStock = $ProductClass->getProductStock();
            if (!$ProductStock) {
                $ProductStock = new ProductStock();
                $ProductStock->setProductClass($ProductClass);
                $ProductClass->setProductStock($ProductStock);
            }
            $ProductClass->setStock($stock);
            $ProductStock->setStock($stock);
        }
    }

    /**
     * 受注の確定処理を行います。
     *
     * @param ItemHolderInterface $target 受注 or カート
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @return void
     */
    #[\Override]
    public function commit(ItemHolderInterface $target, PurchaseContext $context): void
    {
    }

    /**
     * 仮確定した受注データの取り消し処理を行います。
     *
     * @param ItemHolderInterface $itemHolder 受注 or カート
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @return void
     */
    #[\Override]
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
    }
}

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

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PessimisticLockException;
use Doctrine\ORM\OptimisticLockException;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Exception\ShoppingException;
use Eccube\Repository\ProductStockRepository;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 在庫制御.
 */
class StockReduceProcessor extends AbstractPurchaseProcessor
{
    /**
     * @var ProductStockRepository
     */
    protected $productStockRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * StockReduceProcessor constructor.
     *
     * @param ProductStockRepository $productStockRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ProductStockRepository $productStockRepository, EntityManagerInterface $entityManager)
    {
        $this->productStockRepository = $productStockRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $this->setStockForEachProductClass($itemHolder, function ($currentStock, $itemQuantity) {
            return $currentStock - $itemQuantity;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        $this->setStockForEachProductClass($itemHolder, function ($currentStock, $itemQuantity) {
            return $currentStock + $itemQuantity;
        });
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param callable $callback
     * @throws OptimisticLockException
     * @throws PessimisticLockException
     * @throws ShoppingException
     */
    private function setStockForEachProductClass(ItemHolderInterface $itemHolder, callable $callback)
    {
        // Order以外の場合は何もしない
        if (!$itemHolder instanceof Order) {
            return;
        }

        $productClasses = $this->createProductClassesForEachId($itemHolder);

        foreach ($productClasses as $productClassId => $quantity) {
            $ProductClass = $this->entityManager->find(ProductClass::class, $productClassId);
            $productStock = $ProductClass->getProductStock();
            $stock = $callback($productStock->getStock(), $quantity);
            if ($stock < 0) {
                throw new ShoppingException(trans('purchase_flow.over_stock', ['%name%' => $ProductClass->formattedProductName()]));
            }
            $productStock->setStock($stock);
            $ProductClass->setStock($stock);
        }
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @return array
     * @throws OptimisticLockException
     * @throws PessimisticLockException
     */
    private function createProductClassesForEachId(ItemHolderInterface $itemHolder)
    {
        $productClasses = [];

        foreach ($itemHolder->getProductOrderItems() as $item) {
            // 在庫が無制限かチェックし、制限ありなら在庫数をチェック
            if (!$item->getProductClass()->isStockUnlimited()) {
                // 在庫チェックあり
                /* @var ProductStock $productStock */
                $productStock = $item->getProductClass()->getProductStock();
                // 在庫に対してロックを実行
                $this->entityManager->lock($productStock, LockMode::PESSIMISTIC_WRITE);
                $this->entityManager->refresh($productStock);
                $ProductClass = $item->getProductClass();
                if (!array_key_exists($ProductClass->getId(), $productClasses)) {
                    $productClasses[$ProductClass->getId()] = 0;
                }
                $productClasses[$ProductClass->getId()] += (int) $item->getQuantity();
            }
        }

        return $productClasses;
    }
}

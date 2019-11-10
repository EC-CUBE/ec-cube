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
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Entity\ProductStock;
use Eccube\Repository\ProductStockRepository;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseProcessor;

/**
 * 在庫制御.
 */
class StockReduceProcessor extends ItemHolderValidator implements PurchaseProcessor
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

    /*
     * ItemHolderValidator
     */

    /**
     * {@inheritdoc}
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // 何もしない
    }

    /*
     * PurchaseProcessor
     */

    /**
     * {@inheritdoc}
     */
    public function prepare(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // 在庫を減らす
        $this->eachProductOrderItems($itemHolder, function ($currentStock, $itemQuantity) {
            return $currentStock - $itemQuantity;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        // 何もしない
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // 在庫を戻す
        $this->eachProductOrderItems($itemHolder, function ($currentStock, $itemQuantity) {
            return $currentStock + $itemQuantity;
        });
    }

    private function eachProductOrderItems(ItemHolderInterface $itemHolder, callable $callback)
    {
        // Order以外の場合は何もしない
        if (!$itemHolder instanceof Order) {
            return;
        }

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
                $stock = $callback($productStock->getStock(), $item->getQuantity());
                if ($stock < 0) {
                    $this->throwInvalidItemException(trans('purchase_flow.over_stock', ['%name%' => $ProductClass->formattedProductName()]));
                }
                $productStock->setStock($stock);
                $ProductClass->setStock($stock);
            }
        }
    }
}

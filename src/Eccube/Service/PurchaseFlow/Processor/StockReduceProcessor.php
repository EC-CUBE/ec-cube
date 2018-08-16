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

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Entity\ProductStock;
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
        // 在庫を減らす
        $this->eachProductOrderItems($itemHolder, function ($currentStock, $itemQuantity) {
            return $currentStock - $itemQuantity;
        });
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

    /**
     * Update product class stock
     *
     * @param ItemHolderInterface $itemHolder
     * @param callable $callback
     */
    private function eachProductOrderItems(ItemHolderInterface $itemHolder, callable $callback)
    {
        // Order以外の場合は何もしない
        if (!$itemHolder instanceof Order) {
            return;
        }

        $isUpdate = false;
        foreach ($itemHolder->getProductOrderItems() as $item) {
            // 在庫が無制限かチェックし、制限ありなら在庫数をチェック
            if (!$item->getProductClass()->isStockUnlimited()) {
                // 在庫チェックあり
                // 在庫に対してロック(select ... for update)を実行
                /* @var ProductStock $productStock */
                $productStock = $this->productStockRepository->find(
                    $item->getProductClass()->getProductStock()->getId(), LockMode::PESSIMISTIC_WRITE
                );

                $stock = $callback($productStock->getStock(), $item->getQuantity());
                $productStock->setStock($stock);
                $item->getProductClass()->setStock($stock);
                $this->entityManager->persist($productStock);
                $this->entityManager->persist($item);
                $isUpdate = true;
            }
        }
        if ($isUpdate) {
            $this->entityManager->flush();
        }
    }
}

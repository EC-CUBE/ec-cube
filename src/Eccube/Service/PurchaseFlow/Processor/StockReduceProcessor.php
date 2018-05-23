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
use Eccube\Entity\ItemInterface;
use Eccube\Entity\OrderItem;
use Eccube\Repository\ProductStockRepository;
use Eccube\Service\PurchaseFlow\ItemProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 在庫制御.
 */
class StockReduceProcessor implements ItemProcessor
{
    /**
     * @var ProductStockRepository
     */
    protected $productStockRepository;

    /**
     * StockReduceProcessor constructor.
     *
     * @param ProductStockRepository $productStockRepository
     */
    public function __construct(ProductStockRepository $productStockRepository)
    {
        $this->productStockRepository = $productStockRepository;
    }

    /**
     * @param ItemInterface   $item
     * @param PurchaseContext $context
     *
     * @return ProcessResult
     *
     * @internal param ItemHolderInterface $itemHolder
     */
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item instanceof OrderItem) {
            // OrderItem 以外の場合は何もしない
            return ProcessResult::success();
        }

        // 在庫処理を実装
        // warning も作りたい
        if (!$item->isProduct()) {
            // FIXME 配送明細を考慮する必要がある
            return ProcessResult::success();
        }
        // 在庫が無制限かチェックし、制限ありなら在庫数をチェック
        if (!$item->getProductClass()->isStockUnlimited()) {
            // 在庫チェックあり
            // 在庫に対してロック(select ... for update)を実行
            $productStock = $this->productStockRepository->find(
                $item->getProductClass()->getProductStock()->getId(), LockMode::PESSIMISTIC_WRITE
            );
            // 購入数量と在庫数をチェックして在庫がなければエラー
            if ($productStock->getStock() < 1) {
                return ProcessResult::fail(trans('stockreduceprocessor.text.error.stock'));
            } elseif ($item->getQuantity() > $productStock->getStock()) {
                return ProcessResult::fail(trans('stockreduceprocessor.text.error.stock'));
            }
        }

        return ProcessResult::success();
    }
}

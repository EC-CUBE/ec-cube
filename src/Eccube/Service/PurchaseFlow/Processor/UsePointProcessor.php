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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\ItemHolderProcessor;
use Eccube\Service\PurchaseFlow\ProcessResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 使用ポイント値引明細追加.
 */
class UsePointProcessor implements ItemHolderProcessor
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * UsePointProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param BaseInfo $BaseInfo
     */
    public function __construct(EntityManagerInterface $entityManager, BaseInfo $BaseInfo)
    {
        $this->entityManager = $entityManager;
        $this->BaseInfo = $BaseInfo;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext     $context
     *
     * @return ProcessResult
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if ($itemHolder->getUsePoint() > 0) {
            if ($itemHolder->getUsePoint() > $context->getUser()->getPoint()) {
                // TODO カートに戻さないように修正する
                return ProcessResult::error('利用ポイントが所有ポイントを上回っています.');
            }

            // XXX delete/insert ではなく, update/insert の方がいいかも
            $this->removePointDiscountItem($itemHolder);

            return $this->addPointDiscountItem($itemHolder);
        }

        return ProcessResult::success();
    }

    /**
     * 明細追加処理
     *
     * @param ItemHolderInterface $itemHolder
     */
    protected function addPointDiscountItem(ItemHolderInterface $itemHolder)
    {
        $DiscountType = $this->entityManager
            ->find(OrderItemType::class, OrderItemType::DISCOUNT);
        // TODO
        $TaxInclude = $this->entityManager
            ->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxion = $this->entityManager
            ->find(TaxType::class, TaxType::TAXATION);

        /** @var Order $Order */
        $Order = $itemHolder;

        $priceOfUsePoint = $this->usePointToPrice($Order->getUsePoint());
        if (($itemHolder->getTotal() + $priceOfUsePoint) < 0) {
            // TODO カートに戻さないように修正する
            // TODO 送料・手数料も考慮する
            return ProcessResult::error('利用ポイントがお支払い金額を上回っています.');
        }
        $OrderItem = new OrderItem();
        $OrderItem->setProductName('ポイント値引')
            ->setPrice($priceOfUsePoint)
            ->setPriceIncTax($priceOfUsePoint)
            ->setTaxRate(8)
            ->setQuantity(1)
            ->setOrderItemType($DiscountType)
            ->setTaxDisplayType($TaxInclude)
            ->setTaxType($Taxion)
            ->setOrder($itemHolder);
        $itemHolder->addItem($OrderItem);

        return ProcessResult::success();
    }

    /**
     * 既存のポイント明細を削除する.
     *
     * @param ItemHolderInterface $itemHolder
     */
    protected function removePointDiscountItem(ItemHolderInterface $itemHolder)
    {
        foreach ($itemHolder->getItems() as $item) {
            if ($item->isDiscount() && $item->getProductName() == 'ポイント値引') {
                $itemHolder->removeOrderItem($item);
                $this->entityManager->remove($item);
            }
        }
    }

    /**
     * 利用ポイントを単価に換算する.
     *
     * @param integer $usePoint 利用ポイント
     *
     * @return integer
     */
    protected function usePointToPrice($usePoint)
    {
        return ($usePoint * $this->BaseInfo->getPointConversionRate()) * -1;
    }
}

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

use Eccube\Entity\DeliveryFee;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 送料明細の金額とdtb_delivery_feeに登録されている送料の差異を検知するバリデータ.
 */
class DeliveryFeeChangeValidator extends ItemHolderValidator
{
    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * DeliveryFeeChangeValidator constructor.
     *
     * @param DeliveryFeeRepository $deliveryFeeRepository
     */
    public function __construct(DeliveryFeeRepository $deliveryFeeRepository)
    {
        $this->deliveryFeeRepository = $deliveryFeeRepository;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        // 送料明細がない場合はスキップする.
        if (!$this->hasDeliveryFeeItems($itemHolder)) {
            return;
        }

        // 送料無料条件により送料合計が0の場合はスキップする.
        if (0 == $itemHolder->getDeliveryFeeTotal()) {
            return;
        }

        $changed = false;

        /** @var Order $itemHolder */
        foreach ($itemHolder->getOrderItems() as $item) {
            if ($item->isDeliveryFee()) {
                $Fee = $this->getFee($item);

                // 送料の変更を確認
                if ($Fee->getFee() != $item->getPrice()) {
                    $changed = true;

                    // 現在の送料に丸める.
                    $item->setPrice($Fee->getFee());
                }
            }
        }

        if ($changed) {
            $this->throwInvalidItemException('送料が変更されました.', null, true);
        }
    }

    private function hasDeliveryFeeItems(Order $Order)
    {
        foreach ($Order->getOrderItems() as $Item) {
            if ($Item->isDeliveryFee()) {
                return true;
            }
        }

        return false;
    }

    /**
     * 送料テーブル(dtb_delivery_fee)から送料を取得する.
     *
     * @param OrderItem $newItem
     *
     * @return null|DeliveryFee
     */
    protected function getFee(OrderItem $newItem)
    {
        $Delivery = $newItem->getShipping()->getDelivery();
        $Pref = $newItem->getShipping()->getPref();

        return $this->deliveryFeeRepository->findOneBy(['Delivery' => $Delivery, 'Pref' => $Pref]);
    }
}

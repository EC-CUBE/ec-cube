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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

class EmptyItemsValidator extends ItemHolderValidator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * EmptyItemsProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        foreach ($itemHolder->getItems() as $item) {
            if ($item->isProduct() && $item->getQuantity() <= 0) {
                if ($itemHolder instanceof Order) {
                    foreach ($itemHolder->getShippings() as $Shipping) {
                        $Shipping->removeOrderItem($item);
                    }
                    $itemHolder->removeOrderItem($item);
                } else {
                    $itemHolder->removeItem($item);
                }
                $this->entityManager->remove($item);
            }
        }

        if (!$itemHolder instanceof Order) {
            return;
        }

        // 受注の場合は, Shippingに紐づく商品明細がない場合はShippingも削除する.
        foreach ($itemHolder->getShippings() as $Shipping) {
            $hasProductItem = false;
            foreach ($Shipping->getOrderItems() as $item) {
                if ($item->isProduct()) {
                    $hasProductItem = true;
                    break;
                }
            }

            if (!$hasProductItem) {
                foreach ($Shipping->getOrderItems() as $item) {
                    $this->entityManager->remove($item);
                }
                $itemHolder->removeShipping($Shipping);
                $this->entityManager->remove($Shipping);
            }
        }

        // Shippingがなくなれば購入エラー.
        if (count($itemHolder->getShippings()) < 1) {
            $this->throwInvalidItemException('front.shopping.empty_items_error');
        }
    }
}

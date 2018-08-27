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
        if (!$itemHolder instanceof Order) {
            return;
        }

        foreach ($itemHolder->getItems() as $item) {
            if ($item->isProduct() && $item->getQuantity() == 0) {
                foreach ($itemHolder->getShippings() as $Shipping) {
                    $Shipping->removeOrderItem($item);
                }
                $itemHolder->removeOrderItem($item);
                $this->entityManager->remove($item);
            }
        }

        foreach ($itemHolder->getShippings() as $Shipping) {
            $hasProductItem = false;
            foreach ($Shipping->getOrderItems() as $item) {
                if ($item->isProduct()) {
                    $hasProductItem = true;
                }
            }

            if (!$hasProductItem) {
                $itemHolder->removeShipping($Shipping);
                $this->entityManager->remove($Shipping);
            }
        }

        if (count($itemHolder->getShippings()) < 1) {
            $this->throwInvalidItemException('ご注文手続き中にエラーが発生しました。大変お手数ですが再度ご注文手続きをお願いします。');
        }
    }
}

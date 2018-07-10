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

use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\ShoppingService;

/**
 * 受注情報更新処理.
 */
class OrderUpdateProcessor extends AbstractPurchaseProcessor
{
    /**
     * @var ShoppingService
     */
    private $shoppingService;

    /**
     * OrderUpdateProcessor constructor.
     *
     * @param ShoppingService $shoppingService
     */
    public function __construct(ShoppingService $shoppingService)
    {
        $this->shoppingService = $shoppingService;
    }

    public function commit(ItemHolderInterface $target, PurchaseContext $context)
    {
        if (!$target instanceof Order) {
            return;
        }
        $this->shoppingService->setOrderUpdateData($target);
    }
}

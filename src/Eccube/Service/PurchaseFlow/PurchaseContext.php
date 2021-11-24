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

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\Customer;
use Eccube\Entity\ItemHolderInterface;

/**
 * PurchaseFlowの実行中コンテキスト.
 */
class PurchaseContext extends \SplObjectStorage
{
    private $user;

    private $originHolder;

    private $flowType;

    const ORDER_FLOW = 'order';

    const SHOPPING_FLOW = 'shopping';

    const CART_FLOW = 'cart';

    public function __construct(ItemHolderInterface $originHolder = null, ?Customer $user = null)
    {
        $this->originHolder = $originHolder;
        $this->user = $user;
    }

    /**
     * PurchaseFlow実行前の{@link ItemHolderInterface}を取得.
     *
     * @return ItemHolderInterface
     */
    public function getOriginHolder()
    {
        return $this->originHolder;
    }

    /**
     * 会員情報を取得.
     *
     * @return Customer
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setFlowType($flowType)
    {
        $this->flowType = $flowType;
    }

    public function isOrderFlow()
    {
        return $this->flowType === self::ORDER_FLOW;
    }

    public function isShoppingFlow()
    {
        return $this->flowType === self::SHOPPING_FLOW;
    }

    public function isCartFlow()
    {
        return $this->flowType === self::CART_FLOW;
    }
}

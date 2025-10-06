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
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * PurchaseFlowの実行中コンテキスト.
 *
 * @extends \SplObjectStorage<ItemHolderInterface, mixed>
 */
class PurchaseContext extends \SplObjectStorage
{
    /**
     * @var UserInterface|Customer|null 会員情報
     */
    private $user;

    /**
     * @var ItemHolderInterface|null PurchaseFlow実行前の{@link ItemHolderInterface}
     */
    private $originHolder;

    /**
     * @var string
     */
    private $flowType;

    public const ORDER_FLOW = 'order';

    public const SHOPPING_FLOW = 'shopping';

    public const CART_FLOW = 'cart';

    /**
     * @param ItemHolderInterface|null $originHolder
     * @param UserInterface|Customer|null $user
     */
    public function __construct(?ItemHolderInterface $originHolder = null, UserInterface|Customer|null $user = null)
    {
        $this->originHolder = $originHolder;
        $this->user = $user;
    }

    /**
     * PurchaseFlow実行前の{@link ItemHolderInterface}を取得.
     *
     * @return ItemHolderInterface|null
     */
    public function getOriginHolder(): ?ItemHolderInterface
    {
        return $this->originHolder;
    }

    /**
     * 会員情報を取得.
     *
     * @return Customer|UserInterface|null
     */
    public function getUser(): Customer|UserInterface|null
    {
        return $this->user;
    }

    /**
     * @param string $flowType
     *
     * @return void
     */
    public function setFlowType($flowType): void
    {
        $this->flowType = $flowType;
    }

    /**
     * @return bool
     */
    public function isOrderFlow(): bool
    {
        return $this->flowType === self::ORDER_FLOW;
    }

    /**
     * @return bool
     */
    public function isShoppingFlow(): bool
    {
        return $this->flowType === self::SHOPPING_FLOW;
    }

    /**
     * @return bool
     */
    public function isCartFlow(): bool
    {
        return $this->flowType === self::CART_FLOW;
    }
}

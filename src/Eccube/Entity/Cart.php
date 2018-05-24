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

namespace Eccube\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemCollection;

class Cart extends AbstractEntity implements PurchaseInterface, ItemHolderInterface
{
    use PointTrait;

    /**
     * @var bool
     */
    private $lock = false;

    /**
     * @var ArrayCollection
     */
    private $CartItems;

    /**
     * @var string
     */
    private $pre_order_id = null;

    /**
     * @var integer
     */
    private $total_price;

    /**
     * @var integer
     */
    private $delivery_fee_total;

    /**
     * @var InvalidItemException[]
     */
    private $errors = [];

    public function __wakeup()
    {
        $this->errors = [];
    }

    public function __construct()
    {
        $this->CartItems = new ArrayCollection();
    }

    /**
     * @return bool
     *
     * @deprecated 使用しないので削除予定
     */
    public function getLock()
    {
        return $this->lock;
    }

    /**
     * @param  bool                $lock
     *
     * @return \Eccube\Entity\Cart
     *
     * @deprecated 使用しないので削除予定
     */
    public function setLock($lock)
    {
        $this->lock = $lock;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPreOrderId()
    {
        return $this->pre_order_id;
    }

    /**
     * @param  integer             $pre_order_id
     *
     * @return \Eccube\Entity\Cart
     */
    public function setPreOrderId($pre_order_id)
    {
        $this->pre_order_id = $pre_order_id;

        return $this;
    }

    /**
     * @param  CartItem            $CartItem
     *
     * @return \Eccube\Entity\Cart
     */
    public function addCartItem(CartItem $CartItem)
    {
        $this->CartItems[] = $CartItem;

        return $this;
    }

    /**
     * @return \Eccube\Entity\Cart
     */
    public function clearCartItems()
    {
        $this->CartItems->clear();

        return $this;
    }

    /**
     * @return CartItem[]
     */
    public function getCartItems()
    {
        return $this->CartItems;
    }

    /**
     * Alias of getCartItems()
     */
    public function getItems()
    {
        return (new ItemCollection($this->getCartItems()))->sort();
    }

    /**
     * @param  CartItem[]          $CartItems
     *
     * @return \Eccube\Entity\Cart
     */
    public function setCartItems($CartItems)
    {
        $this->CartItems = $CartItems;

        return $this;
    }

    /**
     * Set total.
     *
     * @param integer $total_price
     *
     * @return Cart
     */
    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * Alias of setTotalPrice.
     */
    public function setTotal($total)
    {
        return $this->setTotalPrice($total);
    }

    /**
     * Alias of getTotalPrice
     */
    public function getTotal()
    {
        return $this->getTotalPrice();
    }

    /**
     * @return integer
     */
    public function getTotalQuantity()
    {
        $totalQuantity = 0;
        foreach ($this->CartItems as $CartItem) {
            $totalQuantity += $CartItem->getQuantity();
        }

        return $totalQuantity;
    }

    /**
     * @param ItemInterface $item
     */
    public function addItem(ItemInterface $item)
    {
        $this->CartItems->add($item);
    }

    /**
     * 個数の合計を返します。
     *
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->getTotalQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryFeeTotal($total)
    {
        $this->delivery_fee_total = $total;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryFeeTotal()
    {
        return $this->delivery_fee_total;
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscount($total)
    {
        // TODO quiet
    }

    /**
     * {@inheritdoc}
     */
    public function setCharge($total)
    {
        // TODO quiet
    }

    /**
     * {@inheritdoc}
     */
    public function setTax($total)
    {
        // TODO quiet
    }
}

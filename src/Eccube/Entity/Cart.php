<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Entity;

class Cart extends \Eccube\Entity\AbstractEntity implements PurchaseInterface
{
    /**
     * @var bool
     */
    private $lock = false;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
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
     * @var int
     */
    private $current_cart_no = 0;

    /**
     * @var array
     */
    private $Payments = array();

    public function __construct()
    {
        $this->CartItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return bool
     */
    public function getLock()
    {
        return $this->lock;
    }

    /**
     * @param  bool                $lock
     * @return \Eccube\Entity\Cart
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
     * @return \Eccube\Entity\Cart
     */
    public function setPreOrderId($pre_order_id)
    {
        $this->pre_order_id = $pre_order_id;

        return $this;
    }

    /**
     * @param  \Eccube\Entity\CartItem $AddCartItem カート商品インスタンス
     * インスタンスがカート内に
     *      - 存在する場合：何もしない
     *      - 存在せず、かつCartCompareServiceで同一と判定されたカート商品が
     *          - 存在する場合：そのインスタンスの数量を変更する
     *          - 存在しない場合：新しいカート商品を追加する
     * @param  \Eccube\Service\CartCompareService $compareService
     * @return \Eccube\Entity\Cart
     */
    public function setCartItem(\Eccube\Entity\CartItem $AddCartItem, $compareService)
    {
        if (!$this->CartItems->contains($AddCartItem)) {
            $ExistsCartItem = $compareService->getExistsCartItem($AddCartItem);
            if ($ExistsCartItem) {
                $ExistsCartItem
                    ->setPrice($AddCartItem->getPrice())
                    ->setQuantity($AddCartItem->getQuantity());
            } else {
                $this->addCartItem($AddCartItem);
            }
        }

        return $this;
    }

    /**
     * @param  CartItem            $CartItem
     * @return \Eccube\Entity\Cart
     */
    public function addCartItem(CartItem $CartItem)
    {
        $CartItem->setCartNo($this->getNextCartNo());
        $this->CartItems[] = $CartItem;

        return $this;
    }

    /**
     * @param  string                  $class_name
     * @param  string                  $class_id
     * @return \Eccube\Entity\CartItem
     */
    public function getCartItemByIdentifier($class_name, $class_id)
    {
        foreach ($this->CartItems as $CartItem) {
            if ($CartItem->getClassName() === $class_name && $CartItem->getClassId() === $class_id) {
                return $CartItem;
            }
        }

        return null;
    }

    /**
     * @param int $cart_no
     * @return CartItem|null
     */
    public function getCartItemByCartNo($cart_no)
    {
        $CartItem = $this->CartItems->filter(function ($CartItem) use ($cart_no) {
            return $cart_no == $CartItem->getCartNo();
        })->first();
        // 配列が空白の場合falseを返すため、明示的にnullを返す
        return $CartItem ?
            $CartItem :
            null;
    }

    public function removeCartItemByIdentifier($class_name, $class_id)
    {
        foreach ($this->CartItems as $CartItem) {
            if ($CartItem->getClassName() === $class_name && $CartItem->getClassId() === $class_id) {
                $this->CartItems->removeElement($CartItem);
            }
        }

        return $this;
    }

    /**
     * カート商品をcart_noから削除する
     *
     * @param int $cart_no
     * @return $this
     */
    public function removeCartItemByCartNo($cart_no)
    {
        $this->CartItems->map(function ($CartItem) use ($cart_no) {
            if ($CartItem->getCartNo() == $cart_no) {
                $this->CartItems->removeElement($CartItem);
            }
        });

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
        return $this->getCartItems();
    }

    /**
     * @param  CartItem[]          $CartItems
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
     * Get Payments
     *
     * @return array
     */
    public function getPayments()
    {
        return $this->Payments;
    }

    /**
     * Set Payments
     *
     * @param $payments
     * @return Cart
     */
    public function setPayments($payments)
    {
        $this->Payments = $payments;

        return $this;
    }

    public function getNextCartNo()
    {
        return $this->current_cart_no++;
    }
}

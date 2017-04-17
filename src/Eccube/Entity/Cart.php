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

class Cart extends \Eccube\Entity\AbstractEntity
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
     * @param  \Eccube\Entity\CartItem $AddCartItem
     * @param  \Eccube\Service\CartCompareService $compareService
     * @return \Eccube\Entity\Cart
     */
    public function setCartItem(\Eccube\Entity\CartItem $AddCartItem, $compareService)
    {
        $ExistsCartItem = $compareService->getExistsCartItem($AddCartItem);
        if ($ExistsCartItem) {
            $ExistsCartItem
                ->setPrice($AddCartItem->getPrice())
                ->setQuantity($AddCartItem->getQuantity());
        } else {
            $this->addCartItem($AddCartItem);
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
     * @param  CartItem[]          $CartItems
     * @return \Eccube\Entity\Cart
     */
    public function setCartItems($CartItems)
    {
        $this->CartItems = $CartItems;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->CartItems as $CartItem) {
            $totalPrice += $CartItem->getTotalPrice();
        }

        return $totalPrice;
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

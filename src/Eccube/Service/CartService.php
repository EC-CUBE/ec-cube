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


namespace Eccube\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Service;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ProductClass;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\Cart\CartItemComparator;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Service
 */
class CartService
{
    /**
     * @var Session
     * @Inject("session")
     */
    protected $session;

    /**
     * @var EntityManager
     * @Inject("orm.em")
     */
    protected $em;

    /**
     * @var ItemHolderInterface
     */
    protected $cart;

    /**
     * @var ProductClassRepository
     * @Inject(ProductClassRepository::class)
     */
    protected $productClassRepository;

    /**
     * @var CartItemComparator
     * @Inject(CartItemComparator::class)
     */
    protected $cartItemComparator;

    /**
     * @return ItemHolderInterface|Cart
     */
    public function getCart()
    {
        if (is_null($this->cart)) {
            $this->cart = $this->session->get('cart', new Cart());
            $this->loadItems();
        }

        return $this->cart;
    }

    protected function loadItems()
    {
        /** @var CartItem $item */
        foreach ($this->cart->getItems() as $item) {
            $ProductClass = $this->productClassRepository->find($item->getProductClassId());
            $item->setProductClass($ProductClass);
        }
    }

    /**
     * カートに商品を追加します.
     * @param $ProductClass ProductClass 商品規格
     * @param $quantity int 数量
     * @return bool 商品を追加できた場合はtrue
     */
    public function addProduct($ProductClass, $quantity = 1)
    {
        if (!$ProductClass instanceof ProductClass) {
            $ProductClassId = $ProductClass;
            $ProductClass = $this->em
                ->getRepository(ProductClass::class)
                ->find($ProductClassId);
            if (is_null($ProductClass)) {
                return false;
            }
        }

        $ClassCategory1 = $ProductClass->getClassCategory1();
        if ($ClassCategory1 && !$ClassCategory1->isVisible()) {
            return false;
        }
        $ClassCategory2 = $ProductClass->getClassCategory2();
        if ($ClassCategory2 && !$ClassCategory2->isVisible()) {
            return false;
        }

        $newItem = new CartItem();
        $newItem->setQuantity($quantity);
        $newItem->setPrice($ProductClass->getPrice01IncTax());
        $newItem->setProductClass($ProductClass);

        $itemExists = false;

        $cart = $this->getCart();
        foreach ($cart->getCartItems() as $item) {
            if ($this->cartItemComparator->compare($newItem, $item)) {
                $item->setQuantity($item->getQuantity() + $newItem->getQuantity());
                $itemExists = true;
                break;
            }
        }
        if (!$itemExists) {
            $cart->addItem($newItem);
        }

        return true;
    }

    public function removeProduct($ProductClass)
    {
        if (!$ProductClass instanceof ProductClass) {
            $ProductClassId = $ProductClass;
            $ProductClass = $this->em
                ->getRepository(ProductClass::class)
                ->find($ProductClassId);
            if (is_null($ProductClass)) {
                return false;
            }
        }

        /** @var Cart $cart */
        $cart = $this->getCart();

        $removeItem = new CartItem();
        $removeItem->setPrice($ProductClass->getPrice01IncTax());
        $removeItem->setProductClass($ProductClass);

        /* @var ArrayCollection $cartItems */
        $cartItems = $cart->getCartItems();
        foreach ($cartItems as $item) {
            if ($this->cartItemComparator->compare($item, $removeItem)) {
                $cartItems->removeElement($item);
                break;
            }
        }

        return true;
    }

    public function save()
    {
        return $this->session->set('cart', $this->getCart());
    }

    public function unlock()
    {
        $this->getCart()
            ->setLock(false)
            ->setPreOrderId(null);
    }

    public function lock()
    {
        $this->getCart()
            ->setLock(true)
            ->setPreOrderId(null);
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->getCart()->getLock();
    }

    /**
     * @param  string $pre_order_id
     * @return \Eccube\Service\CartService
     */
    public function setPreOrderId($pre_order_id)
    {
        $this->getCart()->setPreOrderId($pre_order_id);

        return $this;
    }

    /**
     * @return string
     */
    public function getPreOrderId()
    {
        return $this->getCart()->getPreOrderId();
    }

    /**
     * @return \Eccube\Service\CartService
     */
    public function clear()
    {
        $this->getCart()
            ->setPreOrderId(null)
            ->setLock(false)
            ->setTotalPrice(0)
            ->clearCartItems();

        return $this;
    }

    /**
     * @param CartItemComparator $cartItemComparator
     */
    public function setCartItemComparator($cartItemComparator)
    {
        $this->cartItemComparator = $cartItemComparator;
    }
}

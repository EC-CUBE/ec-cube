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

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ProductClass;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\Cart\CartItemAllocator;
use Eccube\Service\Cart\CartItemComparator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    /**
     * @var Cart[]
     */
    protected $carts;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ItemHolderInterface
     *
     * @deprecated
     */
    protected $cart;

    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var CartItemComparator
     */
    protected $cartItemComparator;

    /**
     * @var CartItemAllocator
     */
    protected $cartItemAllocator;

    /**
     * CartService constructor.
     *
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     * @param ProductClassRepository $productClassRepository
     * @param CartItemComparator $cartItemComparator
     * @param CartItemAllocator $cartItemAllocator
     */
    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        ProductClassRepository $productClassRepository,
        CartItemComparator $cartItemComparator,
        CartItemAllocator $cartItemAllocator
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->productClassRepository = $productClassRepository;
        $this->cartItemComparator = $cartItemComparator;
        $this->cartItemAllocator = $cartItemAllocator;
    }

    public function getCarts()
    {
        if (is_null($this->carts)) {
            $this->carts = $this->session->get('carts', []);
            $this->loadItems();
        }

        return $this->carts;
    }

    /**
     * @return ItemHolderInterface|Cart
     */
    public function getCart()
    {
        $Carts = $this->getCarts();
        if (!$Carts) {
            if (!$this->cart) {
                $this->cart = new Cart();
            }

            return $this->cart;
        }

        return current($this->getCarts());
    }

    protected function loadItems()
    {
        foreach ($this->getCarts() as $Cart) {
            /** @var CartItem $item */
            foreach ($Cart->getItems() as $item) {
                /** @var ProductClass $ProductClass */
                $ProductClass = $this->productClassRepository->find($item->getProductClassId());
                $item->setProductClass($ProductClass);
            }
        }
    }

    /**
     * @param CartItem[] $cartItems
     *
     * @return CartItem[]
     */
    protected function mergeAllCartItems($cartItems = [])
    {
        /** @var CartItem[] $allCartItems */
        $allCartItems = [];

        foreach ($this->getCarts() as $Cart) {
            $allCartItems = $this->mergeCartitems($Cart->getCartItems(), $allCartItems);
        }

        return $this->mergeCartitems($cartItems, $allCartItems);
    }

    /**
     * @param $cartItems
     * @param $allCartItems
     *
     * @return array
     */
    protected function mergeCartitems($cartItems, $allCartItems)
    {
        foreach ($cartItems as $item) {
            $itemExists = false;
            foreach ($allCartItems as $itemInArray) {
                // 同じ明細があればマージする
                if ($this->cartItemComparator->compare($item, $itemInArray)) {
                    $itemInArray->setQuantity($itemInArray->getQuantity() + $item->getQuantity());
                    $itemExists = true;
                    break;
                }
            }
            if (!$itemExists) {
                $allCartItems[] = $item;
            }
        }

        return $allCartItems;
    }

    protected function restoreCarts($cartItems)
    {
        /** @var Cart $Carts */
        $Carts = [];

        foreach ($cartItems as $item) {
            $cartId = $this->cartItemAllocator->allocate($item);
            if (isset($Carts[$cartId])) {
                $Carts[$cartId]->addCartItem($item);
            } else {
                $Cart = new Cart();
                $Cart->addCartItem($item);
                $Carts[$cartId] = $Cart;
            }
        }

        $this->session->set('carts', $Carts);
        // 配列のkeyを0からにする
        $this->carts = array_values($Carts);
    }

    /**
     * カートに商品を追加します.
     *
     * @param $ProductClass ProductClass 商品規格
     * @param $quantity int 数量
     *
     * @return bool 商品を追加できた場合はtrue
     */
    public function addProduct($ProductClass, $quantity = 1)
    {
        if (!$ProductClass instanceof ProductClass) {
            $ProductClassId = $ProductClass;
            $ProductClass = $this->entityManager
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
        $newItem->setPrice($ProductClass->getPrice02IncTax());
        $newItem->setProductClass($ProductClass);

        $allCartItems = $this->mergeAllCartItems([$newItem]);
        $this->restoreCarts($allCartItems);

        return true;
    }

    public function removeProduct($ProductClass)
    {
        if (!$ProductClass instanceof ProductClass) {
            $ProductClassId = $ProductClass;
            $ProductClass = $this->entityManager
                ->getRepository(ProductClass::class)
                ->find($ProductClassId);
            if (is_null($ProductClass)) {
                return false;
            }
        }

        $removeItem = new CartItem();
        $removeItem->setPrice($ProductClass->getPrice02IncTax());
        $removeItem->setProductClass($ProductClass);

        $allCartItems = $this->mergeAllCartItems();
        $foundIndex = -1;
        foreach ($allCartItems as $index => $itemInCart) {
            if ($this->cartItemComparator->compare($itemInCart, $removeItem)) {
                $foundIndex = $index;
                break;
            }
        }
        array_splice($allCartItems, $foundIndex, 1);
        $this->restoreCarts($allCartItems);

        return true;
    }

    public function save()
    {
        return $this->session->set('carts', $this->carts);
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
     *
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
        $Carts = $this->getCarts();
        $removed = array_splice($Carts, 0, 1);
        if (!empty($removed)) {
            $removedCart = $removed[0];
            $removedCart->setPreOrderId(null)
                ->setLock(false)
                ->setTotalPrice(0)
                ->clearCartItems();
        }
        $this->carts = $Carts;

        return $this;
    }

    /**
     * @param CartItemComparator $cartItemComparator
     */
    public function setCartItemComparator($cartItemComparator)
    {
        $this->cartItemComparator = $cartItemComparator;
    }

    /**
     * 指定したインデックスにあるカートを優先にする
     *
     * @param int $index カートのインデックス
     */
    public function setPrimary($index = 0)
    {
        $Carts = $this->getCarts();
        $primary = $Carts[$index];
        $prev = $Carts[0];
        array_splice($Carts, 0, 1, [$primary]);
        array_splice($Carts, $index, 1, [$prev]);
        $this->carts = $Carts;
        $this->save();
    }
}

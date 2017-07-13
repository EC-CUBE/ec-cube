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

use Doctrine\ORM\EntityManager;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ProductClass;
use Symfony\Component\HttpFoundation\Session\Session;

class CartService
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ItemHolderInterface
     */
    protected $cart;

    public function __construct(Session $session, EntityManager $em)
    {
        $this->session = $session;
        $this->cart = $session->get('cart', new Cart());
        $this->em = $em;

        $this->loadItems();
    }

    public function getCart()
    {
        return $this->cart;
    }

    protected function loadItems()
    {
        /** @var CartItem $item */
        foreach ($this->cart->getItems() as $item) {
            $id = $item->getClassId();
            $class = $item->getClassName();
            $entity = $this->em->getRepository($class)->find($id);
            $item->setObject($entity);
        }
    }

    public function addProduct($ProductClass, $quantity)
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
        $cart = $this->cart;
        $exists = $cart->getCartItemByIdentifier(ProductClass::class, $ProductClass->getId());

        if ($exists) {
            $exists->setQuantity($exists->getQuantity() + $quantity);
        } else {
            $item = new CartItem();
            $item->setQuantity($quantity);
            $item->setPrice($ProductClass->getPrice01IncTax());
            $item->setClassId($ProductClass->getId());
            $item->setClassName(ProductClass::class);
            $item->setObject($ProductClass);
            $cart->addItem($item);
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
        $cart = $this->cart;
        $cart->removeCartItemByIdentifier(ProductClass::class, $ProductClass->getId());

        return true;
    }

    public function save()
    {
        return $this->session->set('cart', $this->cart);
    }

    public function unlock()
    {
        $this->cart
            ->setLock(false)
            ->setPreOrderId(null);
    }

    public function lock()
    {
        $this->cart
            ->setLock(true)
            ->setPreOrderId(null);
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->cart->getLock();
    }

    /**
     * @param  string $pre_order_id
     * @return \Eccube\Service\CartService
     */
    public function setPreOrderId($pre_order_id)
    {
        $this->cart->setPreOrderId($pre_order_id);

        return $this;
    }

    /**
     * @return string
     */
    public function getPreOrderId()
    {
        return $this->cart->getPreOrderId();
    }

    /**
     * @return \Eccube\Service\CartService
     */
    public function clear()
    {
        $this->cart
            ->setPreOrderId(null)
            ->setLock(false)
            ->clearCartItems();

        return $this;
    }
}

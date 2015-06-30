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
use Eccube\Exception\CartException;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

class CartService
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Eccube\Entity\Cart
     */
    private $cart;

    /**
     * @var array
     */
    private $errors = array();

    private $ProductType = null;

    /**
     * @var array
     */
    private $messages = array();

    public function __construct(Session $session, EntityManager $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;

        if ($this->session->has('cart')) {
            $this->cart = $this->session->get('cart');
        } else {
            $this->cart = new \Eccube\Entity\Cart();
        }

        foreach ($this->cart->getCartItems() as $CartItem) {
            $ProductClass = $this
                ->entityManager
                ->getRepository($CartItem->getClassName())
                ->find($CartItem->getClassId());
            $this->setCanAddProductType($ProductClass->getProductType());
        }

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
     * @param  string                      $pre_order_id
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

    public function getCart()
    {
        foreach ($this->cart->getCartItems() as $CartItem) {
            $ProductClass = $this
                ->entityManager
                ->getRepository($CartItem->getClassName())
                ->find($CartItem->getClassId());
            $CartItem->setObject($ProductClass);
        }

        return $this->cart;
    }

    /**
     * @param  string  $productClassId
     * @return boolean
     */
    public function canAddProduct($productClassId)
    {
        $ProductClass = $this
            ->entityManager
            ->getRepository('\Eccube\Entity\ProductClass')
            ->find($productClassId);
        $ProductType = $ProductClass->getProductType();

        return $this->ProductType == $ProductType;
    }

    public function setCanAddProductType(\Eccube\Entity\Master\ProductType $ProductType)
    {
        if (is_null($this->ProductType)) {
            $this->ProductType = $ProductType;
        }

        return $this;
    }

    public function getCanAddProductType()
    {
        return $this->ProductType;
    }

    /**
     *
     * @param  string                      $productClassId
     * @param  integer                     $quantity
     * @return \Eccube\Service\CartService
     */
    public function addProduct($productClassId, $quantity = 1)
    {
        $quantity += $this->getProductQuantity($productClassId);
        $this->setProductQuantity($productClassId, $quantity);

        return $this;
    }

    /**
     * @param  string  $productClassId
     * @return integer
     */
    public function getProductQuantity($productClassId)
    {
        $CartItem = $this->cart->getCartItemByIdentifier('Eccube\Entity\ProductClass', (string) $productClassId);
        if ($CartItem) {
            return $CartItem->getQuantity();
        } else {
            return 0;
        }
    }

    /**
     * @param  string                      $productClassId
     * @return \Eccube\Service\CartService
     */
    public function upProductQuantity($productClassId)
    {
        $quantity = $this->getProductQuantity($productClassId) + 1;
        $this->setProductQuantity($productClassId, $quantity);

        return $this;
    }

    /**
     * @param  string                      $productClassId
     * @return \Eccube\Service\CartService
     */
    public function downProductQuantity($productClassId)
    {
        $quantity = $this->getProductQuantity($productClassId) - 1;

        if ($quantity > 0) {
            $this->setProductQuantity($productClassId, $quantity);
        } else {
            $this->removeProduct($productClassId);
        }

        return $this;
    }

    /**
     * @param  \Eccube\Entity\ProductClass|integer $ProductClass
     * @param  integer                             $quantity
     * @return \Eccube\Service\CartService
     * @throws CartException
     */
    public function setProductQuantity($ProductClass, $quantity)
    {
        if (!$ProductClass instanceof \Eccube\Entity\ProductClass) {
            $ProductClass = $this->entityManager
                ->getRepository('Eccube\Entity\ProductClass')
                ->find($ProductClass);
        }
        if (!$ProductClass || $ProductClass->getProduct()->getStatus()->getId() !== 1) {
            throw new CartException('cart.product.type.kind');
        }

        $this->setCanAddProductType($ProductClass->getProductType());

        if (!$this->canAddProduct($ProductClass->getId())) {
            throw new CartException('cart.product.type.kind');
        }

        if (!$ProductClass->getStockUnlimited() && $quantity > $ProductClass->getStock()) {
            $quantity = $ProductClass->getStock();
            $this->addError('cart.over.stock');
        } elseif ($ProductClass->getSaleLimit() && $quantity > $ProductClass->getSaleLimit()) {
            $quantity = $ProductClass->getSaleLimit();
            $this->addError('cart.over.sale_limit');
        }

        $CartItem = new CartItem();
        $CartItem
            ->setClassName('Eccube\Entity\ProductClass')
            ->setClassId((string) $ProductClass->getId())
            ->setPrice($ProductClass->getPrice02IncTax())
            ->setQuantity($quantity);

        $this->cart->setCartItem($CartItem);

        return $this;
    }

    /**
     * @param  string                      $productClassId
     * @return \Eccube\Service\CartService
     */
    public function removeProduct($productClassId)
    {
        $this->cart->removeCartItemByIdentifier('Eccube\Entity\ProductClass', (string) $productClassId);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param  string                      $error
     * @return \Eccube\Service\CartService
     */
    public function addError($error = null)
    {
        $this->errors[] = $error;
        $this->session->getFlashBag()->add('eccube.front.cart.error', $error);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param  string                      $message
     * @return \Eccube\Service\CartService
     */
    public function setMessage($message)
    {
        $this->messages[] = $message;

        return $this;
    }
}

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

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ProductClass;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\Cart\CartItemAllocator;
use Eccube\Service\Cart\CartItemComparator;
use Eccube\Service\OrderHelper;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    /**
     * @var array
     */
    protected $preOrderIds;

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
     * @deprecated
     */
    protected $cart;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

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
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * CartService constructor.
     *
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     * @param ProductClassRepository $productClassRepository
     * @param CartItemComparator $cartItemComparator
     * @param CartItemAllocator $cartItemAllocator
     * @param OrderHelper $orderHelper
     */
    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        ProductClassRepository $productClassRepository,
        OrderRepository $orderRepository,
        CartItemComparator $cartItemComparator,
        CartItemAllocator $cartItemAllocator,
        OrderHelper $orderHelper
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->productClassRepository = $productClassRepository;
        $this->orderRepository = $orderRepository;
        $this->cartItemComparator = $cartItemComparator;
        $this->cartItemAllocator = $cartItemAllocator;
        $this->orderHelper = $orderHelper;
    }

    public function getCarts()
    {
        $this->preOrderIds = $this->session->get('preOrderIds', []);
        if (!$this->preOrderIds) {
            $this->carts = [];
        }
        if (empty($this->carts)) {
            $Orders = $this->orderRepository->findBy(['pre_order_id' => $this->preOrderIds], ['id' => 'ASC']);
            $this->carts = $Orders;
        }
        return $this->carts;
    }

    /**
     * @return ItemHolderInterface|Cart
     */
    public function getCart()
    {
        return current($this->getCarts());
    }

    /**
     * @param CartItem[] $cartItems
     * @return CartItem[]
     */
    protected function mergeAllCartItems($cartItems = [])
    {
        /** @var CartItem[] $allCartItems */
        $allCartItems = [];

        foreach ($this->getCarts() as $Cart) {
            $allCartItems = $this->mergeCartitems($Cart->getItems(), $allCartItems);
        }

        return $this->mergeCartitems($cartItems, $allCartItems);
    }

    /**
     * @param $cartItems
     * @param $allCartItems
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
                $Carts[$cartId]->addOrderItem($item);
            } else {
                $Cart = $this->getCart();
                if (!$Cart) {
                    $Cart = $this->orderHelper->createOrderInCart();
                }
                $this->carts[] = $Cart;
                $this->setPreOrderId($Cart->getPreOrderId());
                $Cart->addOrderItem($item);
                $Carts[$cartId] = $Cart;
            }
            $this->entityManager->flush($Carts[$cartId]);

            $item->setOrder($Carts[$cartId]);
            $this->entityManager->persist($item);
            $this->entityManager->flush($item);
        }

        // 配列のkeyを0からにする
        $this->carts = array_values($Carts);
    }

    /**
     * カートに商品を追加します.
     * @param $ProductClass ProductClass 商品規格
     * @param $quantity int 数量
     * @param Customer $Customer
     * @return bool 商品を追加できた場合はtrue
     */
    public function addProduct($ProductClass, $quantity = 1, Customer $Customer = null)
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

        $ProductItemType = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        // TODO
        $TaxExclude = $this->entityManager->getRepository(TaxDisplayType::class)->find(TaxDisplayType::EXCLUDED);
        $Taxion = $this->entityManager->getRepository(TaxType::class)->find(TaxType::TAXATION);
        $newItem = new OrderItem();
        $newItem->setQuantity($quantity)
            ->setPrice($ProductClass->getPrice02IncTax())
            ->setProductClass($ProductClass)
            ->setProductName($ProductClass->getProduct()->getName())
            ->setOrderItemType($ProductItemType)
            ->setTaxDisplayType($TaxExclude)
            ->setTaxType($Taxion);

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

        $removeItem = new OrderItem();
        $removeItem->setPrice($ProductClass->getPrice02IncTax());
        $removeItem->setProductClass($ProductClass);

        $allCartItems = $this->mergeAllCartItems();
        $foundIndex = -1;
        foreach ($allCartItems as $index=>$itemInCart) {
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
        $OrderStatus = $this->entityManager->find(OrderStatus::class, OrderStatus::CART);
        $this->getCart()
            ->setOrderStatus($OrderStatus);
    }

    public function lock()
    {
        $OrderStatus = $this->entityManager->find(OrderStatus::class, OrderStatus::PROCESSING);
        $this->getCart()
            ->setOrderStatus($OrderStatus);
        $this->entityManager->flush();
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->getCart()->getOrderStatus()->getId() === OrderStatus::PROCESSING;
    }

    /**
     * @param  string $pre_order_id
     * @return \Eccube\Service\CartService
     */
    public function setPreOrderId($pre_order_id)
    {
        $this->preOrderIds = $this->session->get('preOrderIds', []);
        $this->preOrderIds[] = $pre_order_id;
        $this->session->set('preOrderIds', $this->preOrderIds);
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
            $this->entityManager->remove($removedCart);
            $this->entityManager->flush($removedCart);
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

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

namespace Eccube\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Customer;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ProductClass;
use Eccube\Repository\CartRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\Cart\CartItemAllocator;
use Eccube\Service\Cart\CartItemComparator;
use Eccube\Util\StringUtil;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
     * @var CartRepository
     */
    protected $cartRepository;

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
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * CartService constructor.
     *
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     * @param ProductClassRepository $productClassRepository
     * @param CartItemComparator $cartItemComparator
     * @param CartItemAllocator $cartItemAllocator
     * @param OrderHelper $orderHelper
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        ProductClassRepository $productClassRepository,
        CartRepository $cartRepository,
        CartItemComparator $cartItemComparator,
        CartItemAllocator $cartItemAllocator,
        OrderHelper $orderHelper,
        OrderRepository $orderRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->productClassRepository = $productClassRepository;
        $this->cartRepository = $cartRepository;
        $this->cartItemComparator = $cartItemComparator;
        $this->cartItemAllocator = $cartItemAllocator;
        $this->orderHelper = $orderHelper;
        $this->orderRepository = $orderRepository;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getCarts()
    {
        if (!empty($this->carts)) {
            return $this->carts;
        }

        $cartKeys = $this->session->get('cart_keys', []);
        $this->carts = $this->cartRepository->findBy(['cart_key' => $cartKeys], ['id' => 'DESC']);

        return $this->carts;
    }

    /**
     * 会員が保持する永続化されたカートと、非会員時のカートをマージする.
     *
     * @param Customer $Customer
     */
    public function mergeFromPersistenceCart(Customer $Customer)
    {
        $Carts = $this->cartRepository->findBy(['Customer' => $Customer]);

        $CartItems = [];
        foreach ($Carts as $Cart) {
            $CartItems = $this->mergeCartItems($Cart->getCartItems(), $CartItems);
        }

        // セッションにある非会員カートとDBから取得した会員カートをマージする.
        $CartItems = $this->mergeAllCartItems($CartItems);
        $this->restoreCarts($CartItems);
    }

    /**
     * @return ItemHolderInterface|Cart
     */
    public function getCart()
    {
        $Carts = $this->getCarts();

        if (empty($Carts)) {
            return null;
        }

        return current($Carts);
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
            $allCartItems = $this->mergeCartItems($Cart->getCartItems(), $allCartItems);
        }

        return $this->mergeCartItems($cartItems, $allCartItems);
    }

    /**
     * @param $cartItems
     * @param $allCartItems
     *
     * @return array
     */
    protected function mergeCartItems($cartItems, $allCartItems)
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
        if (empty($cartItems)) {
            foreach ($this->getCarts() as $Cart) {
                $this->entityManager->remove($Cart);
                $this->entityManager->flush($Cart);
            }
            $this->carts = [];
        }

        /** @var Cart[] $Carts */
        $Carts = [];

        foreach ($cartItems as $item) {
            $allocatedId = $this->cartItemAllocator->allocate($item);
            $cartKey = $this->createCartKey($allocatedId, $this->getUser());

            if (isset($Carts[$cartKey])) {
                $Cart = $Carts[$cartKey];
                $Cart->addCartItem($item);
                $item->setCart($Cart);
            } else {
                /** @var Cart $Cart */
                $Cart = $this->cartRepository->findOneBy(['cart_key' => $cartKey]);
                if ($Cart) {
                    $this->entityManager->remove($Cart);
                }
                $Cart = new Cart();
                $Cart->setCartKey($cartKey);
                $Cart->addCartItem($item);
                $item->setCart($Cart);
                $Carts[$cartKey] = $Cart;
            }
        }

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
        $cartKeys = [];
        foreach ($this->carts as $Cart) {
            $Cart->setCustomer($this->getUser());
            $this->entityManager->persist($Cart);
            foreach ($Cart->getCartItems() as $item) {
                $this->entityManager->persist($item);
                $this->entityManager->flush($item);
            }
            $this->entityManager->flush($Cart);
            $cartKeys[] = $Cart->getCartKey();
        }

        $this->session->set('cart_keys', $cartKeys);

        return;
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
            $this->entityManager->remove($removed[0]);
            $this->entityManager->flush($removed);
        }
        $this->carts = $Carts;

        $cartKeys = [];
        foreach ($Carts as $Cart) {
            $cartKeys[] = $Cart->getCartKey();
        }

        $this->session->set('cart_keys', $cartKeys);

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

    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }

    protected function createCartKey($allocatedId, Customer $Customer = null)
    {
        if ($Customer instanceof Customer) {
            return $Customer->getId().'_'.$allocatedId;
        }

        do {
            $random = StringUtil::random(32);
            $cartKey = $random.'_'.$allocatedId;
            $Cart = $this->cartRepository->findOneBy(['cart_key' => $cartKey]);
        } while ($Cart);

        return $cartKey;
    }
}

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

namespace Eccube\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Customer;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ProductClass;
use Eccube\Repository\CartRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\Cart\CartItemAllocator;
use Eccube\Service\Cart\CartItemComparator;
use Eccube\Session\Session;
use Eccube\Util\StringUtil;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CartService
{
    /**
     * @var Cart[]
     */
    protected $carts;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var EntityManagerInterface
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
     */
    public function __construct(
        Session $session,
        EntityManagerInterface $entityManager,
        ProductClassRepository $productClassRepository,
        CartRepository $cartRepository,
        CartItemComparator $cartItemComparator,
        CartItemAllocator $cartItemAllocator,
        OrderRepository $orderRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->productClassRepository = $productClassRepository;
        $this->cartRepository = $cartRepository;
        $this->cartItemComparator = $cartItemComparator;
        $this->cartItemAllocator = $cartItemAllocator;
        $this->orderRepository = $orderRepository;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * 現在のカートの配列を取得する.
     *
     * 本サービスのインスタンスのメンバーが空の場合は、DBまたはセッションからカートを取得する
     *
     * @param bool $empty_delete true の場合、商品明細が空のカートが存在した場合は削除する
     *
     * @return Cart[]
     */
    public function getCarts($empty_delete = false): array
    {
        if (null !== $this->carts) {
            if ($empty_delete) {
                $cartKeys = [];
                foreach (array_keys($this->carts) as $index) {
                    $Cart = $this->carts[$index];
                    if ($Cart->getItems()->count() > 0) {
                        $cartKeys[] = $Cart->getCartKey();
                    } else {
                        $this->entityManager->remove($this->carts[$index]);
                        $this->entityManager->flush();
                        unset($this->carts[$index]);
                    }
                }

                $this->session->set('cart_keys', $cartKeys);
            }

            return $this->carts;
        }

        if ($this->getUser()) {
            $this->carts = $this->getPersistedCarts();
        } else {
            $this->carts = $this->getSessionCarts();
        }

        return $this->carts;
    }

    /**
     * 永続化されたカートを返す
     *
     * @return Cart[]
     */
    public function getPersistedCarts(): array
    {
        return $this->cartRepository->findBy(['Customer' => $this->getUser()]);
    }

    /**
     * セッションにあるカートを返す
     *
     * @return Cart[]
     */
    public function getSessionCarts(): array
    {
        $cartKeys = $this->session->get('cart_keys', []);

        if (empty($cartKeys)) {
            return [];
        }

        return $this->cartRepository->findBy(['cart_key' => $cartKeys], ['id' => 'ASC']);
    }

    /**
     * 会員が保持する永続化されたカートと、非会員時のカートをマージする.
     *
     * @return void
     */
    public function mergeFromPersistedCart(): void
    {
        $persistedCarts = $this->getPersistedCarts();
        $sessionCarts = $this->getSessionCarts();

        $CartItems = [];

        // 永続化されたカートとセッションのカートが同一の場合はマージしない #4574
        $cartKeys = $this->session->get('cart_keys', []);
        if ((count($persistedCarts) > 0) && !in_array($persistedCarts[0]->getCartKey(), $cartKeys, true)) {
            foreach ($persistedCarts as $Cart) {
                $CartItems = $this->mergeCartItems($Cart->getCartItems(), $CartItems);
            }
        }

        // セッションにある非会員カートとDBから取得した会員カートをマージする.
        foreach ($sessionCarts as $Cart) {
            $CartItems = $this->mergeCartItems($Cart->getCartItems(), $CartItems);
        }

        $this->restoreCarts($CartItems);
    }

    /**
     * @return Cart|null
     */
    public function getCart(): ?Cart
    {
        $Carts = $this->getCarts();

        if (empty($Carts)) {
            return null;
        }

        $cartKeys = $this->session->get('cart_keys', []);
        $Cart = null;
        if (count($cartKeys) > 0) {
            foreach ($Carts as $cart) {
                if ($cart->getCartKey() === current($cartKeys)) {
                    $Cart = $cart;
                    break;
                }
            }
        } else {
            $Cart = $Carts[0];
        }

        return $Cart;
    }

    /**
     * @param array<int, CartItem>|Collection<int, CartItem> $cartItems
     *
     * @return CartItem[]
     */
    protected function mergeAllCartItems($cartItems = []): array
    {
        /** @var CartItem[] $allCartItems */
        $allCartItems = [];

        foreach ($this->getCarts() as $Cart) {
            $allCartItems = $this->mergeCartItems($Cart->getCartItems(), $allCartItems);
        }

        return $this->mergeCartItems($cartItems, $allCartItems);
    }

    /**
     * @param array<int, CartItem>|Collection<int, CartItem> $cartItems
     * @param array<int, CartItem>|Collection<int, CartItem> $allCartItems
     *
     * @return array<mixed>
     */
    protected function mergeCartItems($cartItems, $allCartItems): array
    {
        foreach ($cartItems as $item) {
            $itemExists = false;
            foreach ($allCartItems as $itemInArray) {
                // 同じ明細があればマージする
                if ($this->cartItemComparator->compare($item, $itemInArray)) {
                    $itemInArray->setQuantity(bcadd($itemInArray->getQuantity(), $item->getQuantity()));
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

    /**
     * @param array<int, CartItem> $cartItems
     *
     * @return void
     */
    protected function restoreCarts($cartItems): void
    {
        foreach ($this->getCarts() as $Cart) {
            foreach ($Cart->getCartItems() as $i) {
                $this->entityManager->remove($i);
                $this->entityManager->flush();
            }
            $this->entityManager->remove($Cart);
            $this->entityManager->flush();
        }
        $this->carts = [];

        /** @var Cart[] $Carts */
        $Carts = [];

        foreach ($cartItems as $item) {
            $allocatedId = $this->cartItemAllocator->allocate($item);
            /** @var Customer $Customer */
            $Customer = $this->getUser();
            $cartKey = $this->createCartKey($allocatedId, $Customer);

            if (isset($Carts[$cartKey])) {
                $Cart = $Carts[$cartKey];
                $Cart->addCartItem($item);
                $item->setCart($Cart);
            } else {
                /** @var Cart|null $Cart */
                $Cart = $this->cartRepository->findOneBy(['cart_key' => $cartKey]);
                if ($Cart) {
                    foreach ($Cart->getCartItems() as $i) {
                        $this->entityManager->remove($i);
                        $this->entityManager->flush();
                    }
                    $this->entityManager->remove($Cart);
                    $this->entityManager->flush();
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
     * @param ProductClass|int $ProductClass  商品規格
     * @param string $quantity 数量
     *
     * @return bool 商品を追加できた場合はtrue
     */
    public function addProduct($ProductClass, $quantity = '1'): bool
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

    /**
     * @param int|ProductClass $ProductClass
     *
     * @return bool
     */
    public function removeProduct($ProductClass): bool
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

    /**
     * @return void
     */
    public function save(): void
    {
        $cartKeys = [];
        foreach ($this->carts as $Cart) {
            /** @var Customer $Customer */
            $Customer = $this->getUser();
            $Cart->setCustomer($Customer);
            $this->entityManager->persist($Cart);
            foreach ($Cart->getCartItems() as $item) {
                $this->entityManager->persist($item);
            }
            $this->entityManager->flush();
            $cartKeys[] = $Cart->getCartKey();
        }

        $this->session->set('cart_keys', $cartKeys);
    }

    /**
     * @param  string|null $pre_order_id
     *
     * @return CartService
     */
    public function setPreOrderId($pre_order_id): CartService
    {
        $this->getCart()->setPreOrderId($pre_order_id);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPreOrderId(): ?string
    {
        $Cart = $this->getCart();
        if (!empty($Cart)) {
            return $Cart->getPreOrderId();
        }

        return null;
    }

    /**
     * @return CartService
     */
    public function clear(): CartService
    {
        $Carts = $this->getCarts();
        if (!empty($Carts)) {
            $removed = $this->getCart();
            if ($removed && UnitOfWork::STATE_MANAGED === $this->entityManager->getUnitOfWork()->getEntityState($removed)) {
                $this->entityManager->remove($removed);
                $this->entityManager->flush();

                $cartKeys = [];
                foreach ($Carts as $key => $Cart) {
                    // テーブルから削除されたカートを除外する
                    if ($Cart == $removed) {
                        unset($Carts[$key]);
                    }
                    $cartKeys[] = $Cart->getCartKey();
                }
                $this->session->set('cart_keys', $cartKeys);
                // 注文完了のカートキーをセッションから削除する
                $this->session->remove('cart_key');
                $this->carts = $this->cartRepository->findBy(['cart_key' => $cartKeys], ['id' => 'ASC']);
            }
        }

        return $this;
    }

    /**
     * @param CartItemComparator $cartItemComparator
     *
     * @return void
     */
    public function setCartItemComparator($cartItemComparator): void
    {
        $this->cartItemComparator = $cartItemComparator;
    }

    /**
     * カートキーで指定したインデックスにあるカートを優先にする
     *
     * @param string $cartKey カートキー
     *
     * @return void
     */
    public function setPrimary($cartKey): void
    {
        $Carts = $this->getCarts();
        $primary = $Carts[0];
        $index = 0;
        foreach ($Carts as $key => $Cart) {
            if ($Cart->getCartKey() === $cartKey) {
                $index = $key;
                $primary = $Carts[$index];
                break;
            }
        }
        $prev = $Carts[0];
        array_splice($Carts, 0, 1, [$primary]);
        array_splice($Carts, $index, 1, [$prev]);
        $this->carts = $Carts;
        $this->save();
    }

    /**
     * @return UserInterface|null
     */
    protected function getUser(): ?UserInterface
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    /**
     * @param string $allocatedId
     * @param Customer|null $Customer
     *
     * @return string
     */
    protected function createCartKey($allocatedId, ?Customer $Customer = null): string
    {
        if ($Customer instanceof Customer) {
            return $Customer->getId().'_'.$allocatedId;
        }

        if ($this->session->has('cart_key_prefix')) {
            return $this->session->get('cart_key_prefix').'_'.$allocatedId;
        }

        do {
            $random = StringUtil::random(32);
            $cartKey = $random.'_'.$allocatedId;
            $Cart = $this->cartRepository->findOneBy(['cart_key' => $cartKey]);
        } while ($Cart);

        $this->session->set('cart_key_prefix', $random);

        return $cartKey;
    }
}

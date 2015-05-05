<?php

namespace Eccube\Service;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

class CartService
{
    const PRODUCT_TYPE_NORMAL = 1;

    const PRODUCT_TYPE_DOWNLOAD = 2;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $entityManagaer;

    /**
     * @var \Eccube\Entity\Cart
     */
    private $cart;

    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var array
     */
    private $messages = array();

    public function __construct(Session $session, EntityManager $entityManagaer)
    {
        $this->session = $session;
        $this->entityManagaer = $entityManagaer;

        if ($this->session->has('cart')) {
            $this->cart = $this->session->get('cart');
        } else {
            $this->cart = new \Eccube\Entity\Cart();
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
        $this->cart->setLock(true);
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
            $ProductClass = $this->entityManagaer->getRepository($CartItem->getClassName())->find($CartItem->getClassId());
            $CartItem->setObject($ProductClass);
        }

        return $this->cart;
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
     * @throws \Exception
     */
    public function setProductQuantity($ProductClass, $quantity)
    {
        if (!$ProductClass instanceof \Eccube\Entity\ProductClass) {
            $ProductClass = $this->entityManagaer
                ->getRepository('Eccube\Entity\ProductClass')
                ->find($ProductClass);
        }
        if (!$ProductClass || $ProductClass->getProduct()->getStatus()->getId() !== 1) {
            throw new \Exception();
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
        $this->session->getFlashBag()->add('errors', $error);

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

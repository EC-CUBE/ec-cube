<?php

namespace Eccube\Service;

use Eccube\Application;

class CartService
{
    private $app;

    private $cart;

    private $errors;

    private $messages;

    const PRODUCT_TYPE_NORMAL = 1;

    const PRODUCT_TYPE_DOWNLOAD = 2;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->cart = $app['eccube.entity.cart'];

        $this->errors = array();

        $this->messages = array();
    }

    public function unlock()
    {
        $this->cart
            ->setPreOrderId(null)
            ->setLock(false);
    }

    public function lock()
    {
        $this->cart->setLock(true);
    }

    public function isLocked()
    {
        return $this->cart->lock;
    }

    public function clear()
    {
        $this->cart
            ->setPreOrderId(null)
            ->setLock(false)
            ->clearProducts();

        return $this;
    }

    public function getProducts()
    {
        $products = $this->cart->getProducts();

        if (count($products) > 0) {
            foreach ($products as $productClassId => $product) {
                $productClassData = $this->app['orm.em']
                    ->getRepository('\Eccube\Entity\ProductClass')
                    ->find($productClassId);
                $productData = $this->app['orm.em']
                    ->getRepository('\Eccube\Entity\Product')
                    ->find($productClassData->getProduct()->getId());

                $products[$productClassId] = array(
                    // 'tax_rate' => $product['tax_rate'],
                    'quantity' => $product['quantity'],
                    'Product' => $productData,
                    'ProductClass' => $productClassData,
                );
            }
            return $products;
        } 

        return array();    
    }

    public function addProduct($productClassId)
    {
        $quantity = 1;

        $products = $this->cart->getProducts();

        if (isset($products[$productClassId])) {
            $quantity = $products[$productClassId]['quantity'] + 1;
        }

        return $this->setProductQuantity($productClassId, $quantity);
    }

    public function getProductQuantity($productClassId)
    {
        $quantity = 0;

        $products = $this->cart->getProducts();

        if (isset($products[$productClassId]) && isset($products[$productClassId]['quantity'])) {
            $quantity = $products[$productClassId]['quantity'];
        }

        return $quantity;
    }

    public function upProductQuantity($productClassId)
    {
        $quantity = $this->getProductQuantity($productClassId) + 1;

        return $this->setProductQuantity($productClassId, $quantity);
    }

    public function downProductQuantity($productClassId)
    {
        $quantity = $this->getProductQuantity($productClassId) - 1;

        if ($quantity > 0) {
            return $this->setProductQuantity($productClassId, $quantity);
        }

        return $this->removeProduct($productClassId);
    }

    public function setProductQuantity($productClassId, $quantity)
    {
        $product = $this->app['orm.em']->getRepository('Eccube\Entity\ProductClass')
            ->find($productClassId);
        
        $stock = $product->getStock();
        $stockUnlimited = $product->getStockUnlimited();
        $saleLimit = $product->getSaleLimit();
        if (!$stockUnlimited && $quantity > $stock) {
            $quantity = $stock;
            $this->setError('cart.over.stock');
        } elseif ($saleLimit && $quantity > $saleLimit) {
            $quantity = $saleLimit;
            $this->setError('cart.over.sale_limit');
        }

        if ($quantity > 0) {
            $this->cart->setProductQuantity($productClassId, $quantity);
        }

        return $this;
    }

    public function removeProduct($productClassId)
    {
        $this->cart->removeProduct($productClassId);

        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setError($error)
    {

        $this->errors[] = $error;

        if ($this->errors) {
            $this->app['session']->getFlashBag()->add('errors', $error);
        }

        return $this;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessage($message)
    {
        $this->message[] = $message;

        return $this;
    }

}
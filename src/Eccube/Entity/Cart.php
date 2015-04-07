<?php

namespace Eccube\Entity;

use Eccube\Application;

class Cart
{

    private $app;

    private $lock;

    private $products;

    private $preOrderId;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->initCartSession();
    }

    private function initCartSession()
    {
        $cart = $this->getCartSession();

        if (isset($cart['lock'])) {
            $this->lock = $cart['lock'];
        }
        if (isset($cart['products'])) {
            $this->products = $cart['products'];
        }
        if (isset($cart['pre_order_id'])) {
            $this->preOrderId = $cart['pre_order_id'];
        }
    }    

    private function setCartSession()
    {
        $cart = array(
            'lock' => $this->lock,
            'products' => $this->products,
            'pre_order_id' => $this->preOrderId,
        );
        $this->app['session']->set('cart', $cart);

        return $this;
    }

    public function getCartSession()
    {
        return $this->app['session']->get('cart');
    }

    public function setLock($lock)
    {
        $this->lock = $lock;
        $this->setCartSession();

        return $this;
    }

    public function getLock()
    {
        return $this->lock;
    }

    public function setPreOrderId($id)
    {
        $this->preOrderId = $id;
        $this->setCartSession();

        return $this;
    }

    public function getPreOrderId()
    {
        return $this->preOrderId;
    }

    public function setProductQuantity($productClassId, $quantity)
    {
        $product = $this->app['orm.em']
            ->getRepository('Eccube\Entity\ProductClass')
            ->find($productClassId);
        
        $this->products[$productClassId] = array(
            'price' => $product->getPrice02(),
            // 'tax_rate' => $product->getTaxRate(),
            'quantity' => $quantity,
        );
        $this->setCartSession();

        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function removeProduct($productClassId)
    {
        if (isset($this->products[$productClassId])) {
            unset($this->products[$productClassId]);
        }
        $this->setCartSession();

        return $this;
    }

    public function clearProducts()
    {
        $this->products = array();
        $this->setCartSession();

        return $this;
    }

}
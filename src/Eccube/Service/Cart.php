<?php

namespace Eccube\Service\CartService;

use Eccube\Application;

class Cart
{
    private $app;

    private $cart;

    private $errors;

    /*
        $cart['selected'] = $product_type_id
        $cart['carts'][$product_type_id]["locked"]
        $cart['carts'][$product_type_id]["items"][$product_class_id][price]
                                                           [price_inctax]
                                                           [quantity]
                                                           [point_rate]
                                                           [tax_rate]
                                                           [tax_rule]
                                                           [tax_adjust]
                                                           [product_class_entity]
     */
    private $products;

    const PRODUCT_TYPE_NORMAL = 1;

    const PRODUCT_TYPE_DOWNLOAD = 2;

    public function __construct(Application $app)
    {
        $this->app = $app;

        if ($app['session']->has('cart')) {
            $this->cart = $app['session']->get('cart');
        } else {
            // CartSessionを初期化
            $this->cart = array(
                'carts' => array(
                    self::PRODUCT_TYPE_NORMAL => array(
                        'locked' => false,
                        'items' => array(),
                    ),
                    self::PRODUCT_TYPE_DOWNLOAD => array(
                        'locked' => false,
                        'items' => array(),
                    ),
                ),
            );
        }
    }

    public function register()
    {
        $this->app['session']->set('cart', $this->cart);
    }

    public function select($productTypeId)
    {
        $this->cart['selected'] = $productTypeId;
    }

    public function getCart($productTypeId)
    {
        return $this->cart['carts'][$productTypeId];
    }

    public function unlock($productTypeId)
    {
        $this->cart['carts'][$productTypeId]['locked'] = false;
    }

    public function lock($productTypeId)
    {
        $this->cart['carts'][$productTypeId]['locked'] = true;
    }

    public function clear($productTypeId)
    {
        unset($this->cart['carts'][$productTypeId]['items']);
    }

    public function addProduct($productClassId)
    {
        $productTypeId = $this->getProductTypeIdByProductClassId($productClassId);

        $quantity = 1;

        if (isset($this->cart['carts'][$productTypeId]['items'][$productClassId])) {
            $quantity = $this->cart['carts'][$productTypeId]['items'][$productClassId]['quantity'] + 1;
        }

        $this->setProductQuantity($productClassId, $quantity);
    }

    public function removeProduct($productClassId)
    {
        $productTypeId = $this->getProductTypeIdByProductClassId($productClassId);

        $this->unlock($productTypeId);

        if (isset($this->cart['carts'][$productTypeId]['items'][$productClassId])) {
            unset($this->cart['carts'][$productTypeId]['items'][$productClassId]);
        }
    }

    public function setProductQuantity($productClassId, $quantity)
    {
        $product = $this->app['orm.em']->getRepository('Eccube\Entity\ProductClass')
            ->findOneBy(array('product_class_id' => $productClassId));
        
        $productTypeId = $product->getProductTypeId();

        $this->unlock($productTypeId);

        $stock = $product->getStock();
        $stockUnlimited = $product->getStockUnlimited();
        $saleLimit = $product->getSaleLimit();

        if (!$stockUnlimited && $quantity > $stock) {
            $quantity = $stock:
            $this->errors[] = 'cart.over.stock';
        } elseif ($saleLimit && $quantity > $saleLimit)) {
            $quantity = $saleLimit:
            $this->errors[] = 'cart.over.sale_limit';
        }

        if ($quantity > 0) {
            $this->cart['carts'][$productTypeId]['items'][$productClassId] = array(
                'quantity' => $quantity,
                'product_class_entity' => $product,
            );
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function getProductTypeIdByProductClassId($productClassId)
    {
        if (!$productClassId) {
            throw new InvalidIdException() ;
        }

        $product = $this->app['orm.em']->getRepository('Eccube\Entity\ProductClass')
            ->findOneBy(array('product_class_id' => $productClassId));
        
        if (!$product) {
            return ;
        }

        return $product->getProductTypeId();
    }
}
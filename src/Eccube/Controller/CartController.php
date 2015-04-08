<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class CartController
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
    }

    public function index(Application $app)
    {
        $cart = $app['eccube.service.cart'];
        $products = $cart->getProducts();
        $title = 'カゴの中';

        return $app['twig']->render(
            'Cart/index.twig',
            compact('title', 'products')
        );
    }

    public function add(Application $app, Request $request)
    {
        $productClassId = $request->get('product_class_id');
        $quantity = $request->request->has('quantity') ? $request->get('quantity'): 1;
        $app['eccube.service.cart']->addProduct($productClassId, $quantity);

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    public function up(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->upProductQuantity($productClassId);

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    public function down(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->downProductQuantity($productClassId);

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    public function remove(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->removeProduct($productClassId);

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    public function setQuantity(Application $app, $productClassId, $quantity)
    {
        $app['eccube.service.cart']->setProductQuantity($productClassId, $quantity);

        return $app->redirect($app['url_generator']->generate('cart'));
    }

}
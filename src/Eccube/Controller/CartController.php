<?php

namespace Eccube\Controller;

use Eccube\Application;

class CartController
{
    public function __construct()
    {
    }

    public function index(Application $app)
    {
        $cart = $app['eccube.service.cart'];
        $products = $cart->getProducts();
        $title = 'カゴの中';
        $errors = $cart->getErrors();
        $messages = $cart->getMessages();

        return $app['twig']->render(
            'Cart/index.twig',
            compact('title', 'products', 'errors', 'messages')
        );
    }

    public function add(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->addProduct($productClassId);

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
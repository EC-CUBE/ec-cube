<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class CartController
{
    public function index(Application $app)
    {
        $title = 'カゴの中';
        $Cart = $app['eccube.service.cart']->getCart();

        return $app['view']->render('Cart/index.twig',
            compact('title', 'Cart')
        );
    }

    public function add(Application $app, Request $request)
    {
        $productClassId = $request->get('product_class_id');
        $quantity = $request->request->has('quantity') ? $request->get('quantity') : 1;
        $app['eccube.service.cart']->addProduct($productClassId, $quantity)->save();

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    public function up(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->upProductQuantity($productClassId)->save();

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    public function down(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->downProductQuantity($productClassId)->save();

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    public function remove(Application $app, $productClassId)
    {
        $app['eccube.service.cart']->removeProduct($productClassId)->save();

        return $app->redirect($app['url_generator']->generate('cart'));
    }

    public function setQuantity(Application $app, $productClassId, $quantity)
    {
        $app['eccube.service.cart']->setProductQuantity($productClassId, $quantity)->save();

        return $app->redirect($app['url_generator']->generate('cart'));
    }

}
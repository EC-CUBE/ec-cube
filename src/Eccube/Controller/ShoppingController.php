<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ShoppingController extends AbstractController
{
    public function index(Application $app, Request $request)
    {
        $title = "購入確認 | レジ";
        
        return $app['twig']->render(
                'shopping/index.twig',
                array(
                    'title' => $title,
                    'order' => $preOrder)
        );
    }
}
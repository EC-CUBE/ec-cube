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

namespace Plugin\AnnotatedRouting\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/arc')]
class AnnotatedRoutingController
{
    #[Route(path: '/')]
    #[Template('AnnotatedRouting/Resource/template/index.twig')]
    public function index(Application $app)
    {
        return [];
    }

    #[Route(path: '/form', methods: ['GET'])]
    #[Template('AnnotatedRouting/Resource/template/form.twig')]
    public function form(Application $app)
    {
        return [];
    }

    #[Route(path: '/form', methods: ['POST'])]
    public function submit(Application $app, Request $request)
    {
        return $app->escape('Hello, '.$request->get('value'));
    }
}

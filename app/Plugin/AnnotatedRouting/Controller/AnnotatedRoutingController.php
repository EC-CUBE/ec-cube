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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(value="/arc", service=AnnotatedRoutingController::class)
 */
class AnnotatedRoutingController
{
    /**
     * @Route("/")
     * @Template("AnnotatedRouting/Resource/template/index.twig")
     */
    public function index(Application $app)
    {
        return [];
    }

    /**
     * @Route("/form")
     * @Method("GET")
     * @Template("AnnotatedRouting/Resource/template/form.twig")
     */
    public function form(Application $app)
    {
        return [];
    }

    /**
     * @Route("/form")
     * @Method("POST")
     */
    public function submit(Application $app, Request $request)
    {
        return $app->escape('Hello, '.$request->get('value'));
    }
}

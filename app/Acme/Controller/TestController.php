<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/test")
 * @Template("test/index.twig")
 */
class TestController
{
    /**
     * @Method("GET")
     * @Route("/detail2/{id}", requirements={"id" = "\d+"})
     * //@Security("has_role('ROLE_ADMIN')")
     */
    public function testMethod(Application $app, Request $request, $id = 0)
    {
        return new Response('test Method: '.$id);
    }

    /**
     * @Route("/initialize/{id}", requirements={"id" = "\d+"})
     */
    public function initialize(Application $app, Request $request, $id = 0)
    {
        dump('initialize');
        $t = new \Eccube\Entity\Csv();
        $t->getDispName($id);

        return $app->forward($app->path('test/new'), ['param_init' => $id]);
    }

    /**
     * @Method("GET")
     * @Route("/", name="test/index")
     */
    public function index(Application $app, Request $request)
    {
        dump('/');
        $id = 1;
        $app->forwardChain('/test/initialize/'.$id)
            ->forwardchain($app->path('test/new'), ['param_init' => $id], $response)
            ->forwardChain($app->path('test/new'), ['param_init' => $id], $response)
            ->forwardChain($app->path('test/new'), ['param_init' => $id], $response);

        return $response;
    }

    /**
     * @Method("GET")
     * @Route("/new", name="test/new")
     */
    public function newAction(Application $app, Request $request)
    {
        dump('new');
        $t = $app['request_scope']->get('csv');
        dump($t);

        return ['id' => $t->getDispName()];
    }

    /**
     * @Method("GET")
     * @Route("/post")
     */
    public function postAction(Application $app, Request $request)
    {
        dump('post');
        $app->forward('/test/initialize/5');

        return ['id' => $app['request_scope']->get('csv')->getDispName()];
    }
}

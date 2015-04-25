<?php

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PageController
{
    private $app;

    private $form;

    public function index(Application $app, $page_id = null, $device_id = 10)
    {
        return new \Symfony\Component\HttpFoundation\Response('hello index');
    }

    public function delete(Application $app, $page_id = null, $device_id = 10)
    {
        return $app->redirect($app['url_generator']->generate('admin_content_page'));
    }

}
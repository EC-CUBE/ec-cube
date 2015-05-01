<?php

namespace Eccube\Controller;

use Eccube\Application;

class TopController
{

    public function index(Application $app)
    {
        return $app['view']->render('index.twig');
    }
}

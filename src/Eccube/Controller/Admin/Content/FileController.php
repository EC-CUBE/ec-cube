<?php

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;

class FileController
{
    public function index(Application $app)
    {
        return $app['twig']->render('Admin/Content/index.twig');
    } 
}
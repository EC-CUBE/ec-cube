<?php

namespace Eccube\Controller;

use Eccube\Application;

class AbstractController
{
    public function __construct()
    {
    }

    protected function getBoundForm(Application $app, $type)
    {
        $form = $app['form.factory']
            ->createBuilder($app['eccube.form.type.' . $type], $app['eccube.entity.' . $type])
            ->getForm();
        $form->handleRequest($app['request']);

        return $form;
    }
}

<?php

/*
 * This file is part of the [code]
 *
 * Copyright (C) [year] [author]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\[code]\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class [code]Controller
{

    /**
     * [code]画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        // add code...

        return $app->render('[code]/Resource/template/index.twig', array(
            // add parameter...
        ));
    }

}

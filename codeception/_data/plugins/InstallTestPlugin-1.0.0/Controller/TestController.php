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

namespace Plugin\InstallTestPlugin\Controller;

use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route(path: '/plugin/install-test-plugin/test', name: 'plugin_install_test_plugin_test')]
    public function test(): Response
    {
        return new Response('InstallTestPlugin is working correctly!');
    }
}

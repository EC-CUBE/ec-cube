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

namespace Plugin\Template\Controller;

use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    #[Route('/template', name: 'template')]
    public function front(Request $request): Response
    {
        return $this->render('@Template/index.twig');
    }

    #[Route('/%eccube_admin_route%/template', name: 'template_admin')]
    public function admin(Request $request): Response
    {
        return $this->render('@Template/admin/index.twig');
    }
}

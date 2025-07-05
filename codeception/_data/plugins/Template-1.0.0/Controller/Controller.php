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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    /**
     * @Route("/template", name="template")
     *
     * @Template("@Template/index.twig")
     */
    public function front(Request $request)
    {
        return [];
    }

    /**
     * @Route("/%eccube_admin_route%/template", name="template_admin")
     *
     * @Template("@Template/admin/index.twig")
     */
    public function admin(Request $request)
    {
        return [];
    }
}

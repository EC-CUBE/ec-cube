<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller;

use Eccube\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service=TopController::class)
 */
class TopController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @Template("index.twig")
     */
    public function index(Application $app, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // FIXME 引数の $app とは別物...
        $application = $this->get('app');
        $BaseInfo = $application[\Eccube\Entity\BaseInfo::class];

        // $BaseInfo = $em->find(\Eccube\Entity\BaseInfo::class, 1);
        $Page = $em->find(\Eccube\Entity\Page::class, 1);
        return [
            'BaseInfo' => $BaseInfo,
            'Page' => $Page
        ];
    }
}

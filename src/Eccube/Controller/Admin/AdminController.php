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


namespace Eccube\Controller\Admin;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    public function login(Application $app, Request $request)
    {
        if ($app['security']->isGranted('ROLE_ADMIN')) {
            return $app->redirect($app['url_generator']->generate('admin_login'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createNamedBuilder('', 'admin_login')
            ->getForm();

        return $app['view']->render('Admin/login.twig', array(
            'maintitle' => '',
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ));
    }

    public function index(Application $app, Request $request)
    {
        $Orders = $app['eccube.repository.order']->getNew();

        return $app['view']->render('Admin/index.twig', array(
            'mypageno' => 'index',
            'Orders' => $Orders
        ));
    }
}

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


namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\AuthorityRole;
use Eccube\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;


class AuthorityController extends AbstractController
{
    public function index(Application $app, Request $request)
    {

        $AuthorityRoles = $app['eccube.repository.authority_role']->findAll();

        $form = $app->form()
            ->add('AuthorityRoles', 'collection', array(
                'type' => 'admin_authority_role',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'data' => $AuthorityRoles,
            ))
            ->getForm();
        // 空行を追加
        $form->get('AuthorityRoles')->add(uniqid(), 'admin_authority_role');

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                foreach ($data['AuthorityRoles'] as $AuthorityRole) {
                    if (!empty($AuthorityRole) && !empty($denyUrl)) {
                        $Authority = $AuthorityRole->getAuthority();
                        $denyUrl = $AuthorityRole->getDenyUrl();
                        $app['orm.em']->persist($AuthorityRole);
                    } else {
                        error_log("hpge");
                        $app['orm.em']->remove($AuthorityRole);
                    }
                }
                $app['orm.em']->flush();

                $app->addSuccess('admin.sysmte.authority.save.complete', 'admin');

                return $app->redirect($app->url('admin_setting_system_authority'));

            }
        }

        return $app->render('Setting/System/authority.twig', array(
            'form' => $form->createView(),
        ));
    }
}

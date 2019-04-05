<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;

class AuthorityController extends AbstractController
{

    public function index(Application $app, Request $request)
    {
        $AuthorityRoles = $app['eccube.repository.authority_role']->findAllSort();

        $builder = $app['form.factory']->createBuilder();
        $builder
            ->add('AuthorityRoles', 'collection', array(
                'type' => 'admin_authority_role',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'data' => $AuthorityRoles,
            ));

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'AuthorityRoles' => $AuthorityRoles,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_AUTHORITY_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if (count($AuthorityRoles) == 0) {
            // 1件もない場合、空行を追加
            $form->get('AuthorityRoles')->add(uniqid(), 'admin_authority_role');
        }


        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                foreach ($AuthorityRoles as $AuthorityRole) {
                    $app['orm.em']->remove($AuthorityRole);
                }

                foreach ($data['AuthorityRoles'] as $AuthorityRole) {
                    $Authority = $AuthorityRole->getAuthority();
                    $denyUrl = $AuthorityRole->getDenyUrl();
                    if ($Authority && !empty($denyUrl)) {
                        $app['orm.em']->persist($AuthorityRole);
                    } else {
                        $id = $AuthorityRole->getId();
                        if (!empty($id)) {
                            $role = $app['eccube.repository.authority_role']->find($id);
                            if ($role) {
                                // 削除
                                $app['orm.em']->remove($AuthorityRole);
                            }
                        }
                    }
                }
                $app['orm.em']->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'AuthorityRoles' => $AuthorityRoles,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_AUTHORITY_INDEX_COMPLETE, $event);

                $app->addSuccess('admin.system.authority.save.complete', 'admin');

                return $app->redirect($app->url('admin_setting_system_authority'));

            }
        }

        return $app->render('Setting/System/authority.twig', array(
            'form' => $form->createView(),
        ));
    }
}

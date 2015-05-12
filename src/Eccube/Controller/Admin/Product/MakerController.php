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
use Symfony\Component\HttpFoundation\Request;

class MakerController
{
    public function index(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $TargetMaker = $app['eccube.repository.maker']->find($id);
            if (!$TargetMaker) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
            }
        } else {
            $TargetMaker = new \Eccube\Entity\Maker();
        }

        $form = $app['form.factory']
            ->createBuilder('admin_maker', $TargetMaker)
            ->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.maker']->save($TargetMaker);

                if ($status) {
                    $app['session']->getFlashBag()->add('admin.success', 'admin.maker.save.complete');

                    return $app->redirect($app['url_generator']->generate('admin_product_maker'));
                } else {
                    $app['session']->getFlashBag()->add('admin.error', 'admin.maker.save.error');
                }
            }
        }

        $Makers = $app['eccube.repository.maker']->getList();

        return $app['view']->render('Admin/Product/maker.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => 'メーカー登録',
            'form' => $form->createView(),
            'Makers' => $Makers,
            'TargetMaker' => $TargetMaker,
        ));
    }

    public function up(Application $app, Request $request, $id)
    {
        $TargetMaker = $app['eccube.repository.maker']->find($id);
        if (!$TargetMaker) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_maker', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.maker']->up($TargetMaker);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.maker.up.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.maker.up.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_product_maker'));
    }

    public function down(Application $app, Request $request, $id)
    {
        $TargetMaker = $app['eccube.repository.maker']->find($id);
        if (!$TargetMaker) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_maker', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.maker']->down($TargetMaker);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.maker.down.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.maker.down.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_product_maker'));
    }

    public function delete(Application $app, Request $request, $id)
    {
        $TargetMaker = $app['eccube.repository.maker']->find($id);
        if (!$TargetMaker) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_maker', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.maker']->delete($TargetMaker);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.maker.delete.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.maker.delete.error');
        }

        return $app->redirect($app['url_generator']->generate('admin__product_maker'));
    }
}

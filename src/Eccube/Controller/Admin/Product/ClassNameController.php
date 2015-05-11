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


namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ClassNameController
{
    public function index(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $TargetClassName = $app['eccube.repository.class_name']->find($id);
            if (!$TargetClassName) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
            }
        } else {
            $TargetClassName = new \Eccube\Entity\ClassName();
        }

        $form = $app['form.factory']
            ->createBuilder('admin_class_name', $TargetClassName)
            ->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.class_name']->save($TargetClassName);

                if ($status) {
                    $app['session']->getFlashBag()->add('admin.success', 'admin.class_name.save.complete');

                    return $app->redirect($app['url_generator']->generate('admin_product_class_name'));
                } else {
                    $app['session']->getFlashBag()->add('admin.error', 'admin.class_name.save.error');
                }
            }
        }

        $ClassNames = $app['eccube.repository.class_name']->getList();

        return $app['view']->render('Admin/Product/class_name.twig', array(
            'form' => $form->createView(),
            'ClassNames' => $ClassNames,
            'TargetClassName' => $TargetClassName,
        ));
    }

    public function up(Application $app, Request $request, $id)
    {
        $TargetClassName = $app['eccube.repository.class_name']->find($id);
        if (!$TargetClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_class_name', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.class_name']->up($TargetClassName);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.class_name.up.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.class_name.up.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_product_class_name'));
    }

    public function down(Application $app, Request $request, $id)
    {
        $TargetClassName = $app['eccube.repository.class_name']->find($classNameId);
        if (!$TargetClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_class_name', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.class_name']->down($TargetClassName);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.class_name.down.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.class_name.down.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_product_class_name'));
    }

    public function delete(Application $app, Request $request, $classNameId)
    {
        $TargetClassName = $app['eccube.repository.class_name']->find($classNameId);
        if (!$TargetClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_class_name', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.class_name']->delete($TargetClassName);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.class_name.delete.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.class_name.delete.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_product_class_name'));
    }
}

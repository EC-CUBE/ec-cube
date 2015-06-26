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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClassNameController
{
    public function index(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $TargetClassName = $app['eccube.repository.class_name']->find($id);
            if (!$TargetClassName) {
                throw new NotFoundHttpException();
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
                    $app->addSuccess('admin.class_name.save.complete', 'admin');

                    return $app->redirect($app->url('admin_product_class_name'));
                } else {
                    $app->addError('admin.class_name.save.error', 'admin');
                }
            }
        }

        $ClassNames = $app['eccube.repository.class_name']->getList();

        return $app->render('Product/class_name.twig', array(
            'form' => $form->createView(),
            'ClassNames' => $ClassNames,
            'TargetClassName' => $TargetClassName,
        ));
    }

    public function up(Application $app, Request $request, $id)
    {
        $TargetClassName = $app['eccube.repository.class_name']->find($id);
        if (!$TargetClassName) {
            throw new NotFoundHttpException;
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
            $app->addSuccess('admin.class_name.up.complete', 'admin');
        } else {
            $app->addError('admin.class_name.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_product_class_name'));
    }

    public function down(Application $app, Request $request, $id)
    {
        $TargetClassName = $app['eccube.repository.class_name']->find($id);
        if (!$TargetClassName) {
            throw new NotFoundHttpException();
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
            $app->addSuccess('admin.class_name.down.complete', 'admin');
        } else {
            $app->addError('admin.class_name.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_product_class_name'));
    }

    public function delete(Application $app, Request $request, $id)
    {
        $TargetClassName = $app['eccube.repository.class_name']->find($id);
        if (!$TargetClassName) {
            throw new NotFoundHttpException();
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
            $app->addSuccess('admin.class_name.delete.complete', 'admin');
        } else {
            $app->addError('admin.class_name.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_product_class_name'));
    }

    public function moveRank(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $ranks = $request->request->all();
            foreach ($ranks as $classNameId => $rank) {
                $ClassName = $app['eccube.repository.class_name']
                    ->find($classNameId);
                $ClassName->setRank($rank);
                $app['orm.em']->persist($ClassName);
            }
            $app['orm.em']->flush();
        }
        return true;
    }
}

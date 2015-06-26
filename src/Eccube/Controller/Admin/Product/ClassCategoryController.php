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

class ClassCategoryController
{
    public function index(Application $app, Request $request, $class_name_id, $id = null)
    {
        //
        $ClassName = $app['eccube.repository.class_name']->find($class_name_id);
        if (!$ClassName) {
            throw new NotFoundHttpException();
        }
        if ($id) {
            $TargetClassCategory = $app['eccube.repository.class_category']->find($id);
            if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
                throw new NotFoundHttpException();
            }
        } else {
            $TargetClassCategory = new \Eccube\Entity\ClassCategory();
            $TargetClassCategory->setClassName($ClassName);
        }

        //
        $form = $app['form.factory']
            ->createBuilder('admin_class_category', $TargetClassCategory)
            ->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.class_category']->save($TargetClassCategory);

                if ($status) {
                    $app->addSuccess('admin.class_category.save.complete', 'admin');

                    return $app->redirect($app->url('admin_product_class_category', array('class_name_id' => $ClassName->getId())));
                } else {
                    $app->addError('admin.class_category.save.error', 'admin');
                }
            }
        }

        $ClassCategories = $app['eccube.repository.class_category']->getList($ClassName);

        return $app->render('Product/class_category.twig', array(
            'form' => $form->createView(),
            'ClassName' => $ClassName,
            'ClassCategories' => $ClassCategories,
            'TargetClassCategory' => $TargetClassCategory,
        ));
    }

    public function up(Application $app, Request $request, $class_name_id, $id)
    {
        //
        $ClassName = $app['eccube.repository.class_name']->find($class_name_id);
        if (!$ClassName) {
            throw new NotFoundHttpException();
        }
        $TargetClassCategory = $app['eccube.repository.class_category']->find($id);
        if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
            throw new NotFoundHttpException();
        }

        //
        $form = $app['form.factory']
            ->createNamedBuilder('admin_class_category', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        //
        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.class_category']->up($TargetClassCategory);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.class_category.up.complete', 'admin');
        } else {
            $app->addError('admin.class_category.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_product_class_category', array('class_name_id' => $ClassName->getId())));
    }

    public function down(Application $app, Request $request, $class_name_id, $id)
    {
        //
        $ClassName = $app['eccube.repository.class_name']->find($class_name_id);
        if (!$ClassName) {
            throw new NotFoundHttpException();
        }
        $TargetClassCategory = $app['eccube.repository.class_category']->find($id);
        if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
            throw new NotFoundHttpException();
        }

        //
        $form = $app['form.factory']
            ->createNamedBuilder('admin_class_category', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        //
        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.class_category']->down($TargetClassCategory);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.class_category.down.complete', 'admin');
        } else {
            $app->addError('admin.class_category.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_product_class_category', array('class_name_id' => $ClassName->getId())));
    }

    public function delete(Application $app, Request $request, $class_name_id, $id)
    {
        //
        $ClassName = $app['eccube.repository.class_name']->find($class_name_id);
        if (!$ClassName) {
            throw new NotFoundHttpException();
        }
        $TargetClassCategory = $app['eccube.repository.class_category']->find($id);
        if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
            throw new NotFoundHttpException();
        }

        //
        $form = $app['form.factory']
            ->createNamedBuilder('admin_class_category', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        //
        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.class_category']->delete($TargetClassCategory);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.class_category.delete.complete', 'admin');
        } else {
            $app->addError('admin.class_category.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_product_class_category', array('class_name_id' => $ClassName->getId())));
    }

    public function moveRank(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $ranks = $request->request->all();
            foreach ($ranks as $categoryId => $rank) {
                $ClassCategory = $app['eccube.repository.class_category']
                    ->find($categoryId);
                $ClassCategory->setRank($rank);
                $app['orm.em']->persist($ClassCategory);
            }
            $app['orm.em']->flush();
        }
        return true;
    }
}

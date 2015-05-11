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

class CategoryController
{
    public function index(Application $app, Request $request, $parent_id = null, $id = null)
    {
        //
        if ($parent_id) {
            $Parent = $app['eccube.repository.category']->find($parent_id);
            if (!$Parent) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
            }
        } else {
            $Parent = null;
        }
        if ($id) {
            $TargetCategory = $app['eccube.repository.category']->find($id);
            if (!$TargetCategory) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
            }
            $Parent = $TargetCategory->getParent();
        } else {
            $TargetCategory = new \Eccube\Entity\Category();
            $TargetCategory->setParent($Parent);
            if ($Parent) {
                $TargetCategory->setLevel($Parent->getLevel() + 1);
            } else {
                $TargetCategory->setLevel(1);
            }
        }

        //
        $form = $app['form.factory']
            ->createBuilder('admin_category', $TargetCategory)
            ->getForm();

        //
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.category']->save($TargetCategory);

                if ($status) {
                    $app['session']->getFlashBag()->add('admin.success', 'admin.category.save.complete');

                    if ($Parent) {
                        return $app->redirect($app['url_generator']->generate('admin_product_category_show', array('parent_id' => $Parent->getId())));
                    } else {
                        return $app->redirect($app['url_generator']->generate('admin_product_category'));
                    }
                } else {
                    $app['session']->getFlashBag()->add('admin.error', 'admin.category.save.error');
                }
            }
        }

        $Children = $app['eccube.repository.category']->getList(null);
        $Categories = $app['eccube.repository.category']->getList($Parent);

        return $app['view']->render('Admin/Product/category.twig', array(
            'form' => $form->createView(),
            'Children' => $Children,
            'Parent' => $Parent,
            'Categories' => $Categories,
            'TargetCategory' => $TargetCategory,
        ));
    }

    public function up(Application $app, Request $request, $id)
    {
        $TargetCategory = $app['eccube.repository.category']->find($id);
        if (!$TargetCategory) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }
        $Parent = $TargetCategory->getParent();

        $form = $app['form.factory']
            ->createNamedBuilder('admin_category', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.category']->up($TargetCategory);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.category.up.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.category.up.error');
        }

        if ($Parent) {
            return $app->redirect($app['url_generator']->generate('admin_product_category_show', array('parent_id' => $Parent->getId())));
        } else {
            return $app->redirect($app['url_generator']->generate('admin_product_category'));
        }
    }

    public function down(Application $app, Request $request, $id)
    {
        $TargetCategory = $app['eccube.repository.category']->find($id);
        if (!$TargetCategory) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }
        $Parent = $TargetCategory->getParent();

        $form = $app['form.factory']
            ->createNamedBuilder('admin_category', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.category']->down($TargetCategory);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.category.down.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.category.down.error');
        }

        if ($Parent) {
            return $app->redirect($app['url_generator']->generate('admin_product_category_show', array('parent_id' => $Parent->getId())));
        } else {
            return $app->redirect($app['url_generator']->generate('admin_product_category'));
        }
    }

    public function delete(Application $app, Request $request, $id)
    {
        $TargetCategory = $app['eccube.repository.category']->find($id);
        if (!$TargetCategory) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }
        $Parent = $TargetCategory->getParent();

        $form = $app['form.factory']
            ->createNamedBuilder('admin_category', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $app['eccube.repository.category']->delete($TargetCategory);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.category.delete.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.category.delete.error');
        }

        if ($Parent) {
            return $app->redirect($app['url_generator']->generate('admin_product_category_show', array('parent_id' => $Parent->getId())));
        } else {
            return $app->redirect($app['url_generator']->generate('admin_product_category'));
        }
    }
}

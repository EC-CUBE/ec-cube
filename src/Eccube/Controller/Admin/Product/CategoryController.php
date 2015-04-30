<?php

namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
 
class CategoryController
{
    public function index(Application $app, Request $request, $parentId = null, $categoryId = null)
    {
        // 
        if ($parentId) {
            $Parent = $app['eccube.repository.category']->find($parentId);
            if (!$Parent) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
            }
        } else {
            $Parent = null;
        }
        if ($categoryId) {
            $TargetCategory = $app['eccube.repository.category']->find($categoryId);
            if (!$TargetCategory) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
            }
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
                        return $app->redirect($app['url_generator']->generate('admin_category_show', array('parentId' => $Parent->getId())));
                    } else {
                        return $app->redirect($app['url_generator']->generate('admin_category'));
                    }
                } else {
                    $app['session']->getFlashBag()->add('admin.error', 'admin.category.save.error');
                }
            }
        }

        $Children = $app['eccube.repository.category']->getList(null);
        $Categories = $app['eccube.repository.category']->getList($Parent);

        return $app['view']->render('Admin/Product/category.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => 'カテゴリ登録',
            'form' => $form->createView(),
            'Children' => $Children,
            'Parent' => $Parent,
            'Categories' => $Categories,
            'TargetCategory' => $TargetCategory,
        ));
    }

    public function up(Application $app, Request $request, $categoryId)
    {
        $TargetCategory = $app['eccube.repository.category']->find($categoryId);
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
            return $app->redirect($app['url_generator']->generate('admin_category_show', array('parentId' => $Parent->getId())));
        } else {
            return $app->redirect($app['url_generator']->generate('admin_category'));
        }
    }

    public function down(Application $app, Request $request, $categoryId)
    {
        $TargetCategory = $app['eccube.repository.category']->find($categoryId);
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
            return $app->redirect($app['url_generator']->generate('admin_category_show', array('parentId' => $Parent->getId())));
        } else {
            return $app->redirect($app['url_generator']->generate('admin_category'));
        }
    }

    public function delete(Application $app, Request $request, $categoryId)
    {
        $TargetCategory = $app['eccube.repository.category']->find($categoryId);
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
            return $app->redirect($app['url_generator']->generate('admin_category_show', array('parentId' => $Parent->getId())));
        } else {
            return $app->redirect($app['url_generator']->generate('admin_category'));
        }
    }
}

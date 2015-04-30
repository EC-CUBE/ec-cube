<?php

namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
 
class ClassCategoryController
{
    public function index(Application $app, Request $request, $classNameId, $classCategoryId = null)
    {
        // 
        $ClassName = $app['eccube.repository.class_name']->find($classNameId);
        if (!$ClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }
        if ($classCategoryId) {
            $TargetClassCategory = $app['eccube.repository.class_category']->find($classCategoryId);
            if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
                    $app['session']->getFlashBag()->add('admin.success', 'admin.class_category.save.complete');

                    return $app->redirect($app['url_generator']->generate('admin_class_category', array('classNameId' => $ClassName->getId())));
                } else {
                    $app['session']->getFlashBag()->add('admin.success', 'admin.class_category.save.error');
                }
            }
        }

        $ClassCategories = $app['eccube.repository.class_category']->getList($ClassName);

        return $app['view']->render('Admin/Product/class_category.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => '規格管理＞分類登録',
            'form' => $form->createView(),
            'ClassName' => $ClassName,
            'ClassCategories' => $ClassCategories,
            'TargetClassCategory' => $TargetClassCategory,
        ));
    }

    public function up(Application $app, Request $request, $classNameId, $classCategoryId)
    {
        // 
        $ClassName = $app['eccube.repository.class_name']->find($classNameId);
        if (!$ClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }
        $TargetClassCategory = $app['eccube.repository.class_category']->find($classCategoryId);
        if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            $app['session']->getFlashBag()->add('admin.success', 'admin.class_category.up.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.class_category.up.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_class_category', array('classNameId' => $ClassName->getId())));
    }

    public function down(Application $app, Request $request, $classNameId, $classCategoryId)
    {
        // 
        $ClassName = $app['eccube.repository.class_name']->find($classNameId);
        if (!$ClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }
        $TargetClassCategory = $app['eccube.repository.class_category']->find($classCategoryId);
        if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            $app['session']->getFlashBag()->add('admin.success', 'admin.class_category.down.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.class_category.down.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_class_category', array('classNameId' => $ClassName->getId())));
    }

    public function delete(Application $app, Request $request, $classNameId, $classCategoryId)
    {
        // 
        $ClassName = $app['eccube.repository.class_name']->find($classNameId);
        if (!$ClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }
        $TargetClassCategory = $app['eccube.repository.class_category']->find($classCategoryId);
        if (!$TargetClassCategory || $TargetClassCategory->getClassName() != $ClassName) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            $app['session']->getFlashBag()->add('admin.success', 'admin.class_category.delete.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.class_category.delete.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_class_category', array('classNameId' => $ClassName->getId())));
    }
}

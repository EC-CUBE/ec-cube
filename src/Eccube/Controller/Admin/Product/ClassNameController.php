<?php

namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
 
class ClassNameController
{
    public function index(Application $app, Request $request, $classNameId = null)
    {
        if ($classNameId) {
            $TargetClassName = $app['eccube.repository.class_name']->find($classNameId);
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

                    return $app->redirect($app['url_generator']->generate('admin_class_name'));
                } else {
                    $app['session']->getFlashBag()->add('admin.success', 'admin.class_name.save.error');
                }
            }
        }

        $ClassNames = $app['eccube.repository.class_name']->getList();

        return $app['view']->render('Admin/Product/class_name.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => '規格管理',
            'form' => $form->createView(),
            'ClassNames' => $ClassNames,
            'TargetClassName' => $TargetClassName,
        ));
    }

    public function up(Application $app, Request $request, $classNameId)
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
                $status = $app['eccube.repository.class_name']->up($TargetClassName);
            }
        }

        if ($status === true) {
            $app['session']->getFlashBag()->add('admin.success', 'admin.class_name.up.complete');
        } else {
            $app['session']->getFlashBag()->add('admin.error', 'admin.class_name.up.error');
        }

        return $app->redirect($app['url_generator']->generate('admin_class_name'));
    }

    public function down(Application $app, Request $request, $classNameId)
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

        return $app->redirect($app['url_generator']->generate('admin_class_name'));
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

        return $app->redirect($app['url_generator']->generate('admin_class_name'));
    }
}

<?php

namespace Eccube\Controller\Admin;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class MakerController
{
    public function index(Application $app, Request $request, $makerId = null)
    {
        if ($makerId) {
            $TargetMaker = $app['eccube.repository.maker']->find($makerId);
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

                    return $app->redirect($app['url_generator']->generate('admin_maker'));
                } else {
                    $app['session']->getFlashBag()->add('admin.error', 'admin.maker.save.error');
                }
            }
        }

        $Makers = $app['eccube.repository.maker']->getList();

        return $app['view']->render('Admin/Maker/index.twig', array(
            'maintitle' => '商品管理',
            'subtitle' => 'メーカー登録',
            'form' => $form->createView(),
            'Makers' => $Makers,
            'TargetMaker' => $TargetMaker,
        ));
    }

    public function up(Application $app, Request $request, $makerId)
    {
        $TargetMaker = $app['eccube.repository.maker']->find($makerId);
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

        return $app->redirect($app['url_generator']->generate('admin_maker'));
    }

    public function down(Application $app, Request $request, $makerId)
    {
        $TargetMaker = $app['eccube.repository.maker']->find($makerId);
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

        return $app->redirect($app['url_generator']->generate('admin_maker'));
    }

    public function delete(Application $app, Request $request, $makerId)
    {
        $TargetMaker = $app['eccube.repository.maker']->find($makerId);
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

        return $app->redirect($app['url_generator']->generate('admin_maker'));
    }
}

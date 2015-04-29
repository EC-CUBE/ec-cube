<?php

namespace Eccube\Controller\Admin;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    public function login(Application $app, Request $request)
    {
        if ($app['security']->isGranted('ROLE_ADMIN')) {
            return $app->redirect($app['url_generator']->generate('admin'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createNamedBuilder('', 'admin_login')
            ->getForm();

        return $app['twig']->render('Admin/login.twig', array(
            'maintitle' => '',
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ));
    }

    public function index(Application $app, Request $request)
    {
        $Orders = $app['eccube.repository.order']->getNew();

        return $app['twig']->render('Admin/index.twig', array(
            'maintitle' => 'ホーム',
            'mypageno' => 'index',
            'Orders' => $Orders
        ));
    }

}
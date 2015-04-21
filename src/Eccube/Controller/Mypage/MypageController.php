<?php

namespace Eccube\Controller\MyPage;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MyPageController extends AbstractController
{
    private $title;

    public $form;

    public function __construct()
    {
        $this->title = 'マイページ';

    }

    public function login(Application $app, Request $request)
    {
        if ($app['security']->isGranted('ROLE_USER')) {
            return $app->redirect($app['url_generator']->generate('mypage'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createNamedBuilder('', 'customer_login')
            ->getForm();

        return $app['twig']->render('MyPage/login.twig', array(
            'title' => $this->title,
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ));
    }


    public function index(Application $app)
    {
        return $app['twig']->render('MyPage/index.twig', array(
            'title' => $this->title,
            'subtitle' => '購入履歴一覧',
            'mypageno' => 'index',
        ));
    }

}
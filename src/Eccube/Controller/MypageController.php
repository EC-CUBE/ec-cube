<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class MypageController extends AbstractController
{
    private $title;

    public $form;

    public function __construct()
    {
        $this->title = 'マイページ';

    }

    public function Login(Application $app, Request $request)
    {
        $form = $app['form.factory']
            ->createNamedBuilder('', 'customer_login')
            ->getForm();

        return $app['twig']->render('Mypage/login.twig', array(
            'title' => $this->title,
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            'form' => $form->createView(),
        ));
    }


    public function Index(Application $app)
    {
        return $app['twig']->render('Mypage/index.twig', array(
            'title' => $this->title,
            'subtitle' => '購入履歴一覧',
            'mypageno' => 'index',
        ));
    }

}
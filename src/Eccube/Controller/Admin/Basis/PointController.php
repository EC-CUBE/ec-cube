<?php

namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class PointController extends AbstractController
{
    private $main_title;

    private $title;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->title = 'ポイント設定';
    }

    public function Index(Application $app)
    {
        $baseInfo = $app['eccube.repository.base_info']->get();

        $form = $app['form.factory']
            ->createBuilder('point', $baseInfo)
            ->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $app['orm.em']->persist($baseInfo);
                $app['orm.em']->flush();
                return $app->redirect($app['url_generator']->generate('admin_basis_point'));
            }
        }

        return $app['twig']->render('Admin/Basis/point.twig', array(
            'main_title' => $this->main_title,
            'title'      => $this->title,
            'form'       => $form->createView(),
        ));
    }
}
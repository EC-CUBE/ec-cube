<?php

namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class PointController extends AbstractController
{
    private $main_title;
    private $sub_title;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->sub_title = 'ポイント設定';
    }

    public function index(Application $app)
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
                $app['session']->getFlashBag()->add('point.complete', 'admin.register.complete');
                return $app->redirect($app['url_generator']->generate('admin_basis_point'));
            }
        }

        return $app['twig']->render('Admin/Basis/point.twig', array(
            'tpl_maintitle' => $this->main_title,
            'tpl_subtitle'  => $this->sub_title,
            'form'       => $form->createView(),
        ));
    }
}
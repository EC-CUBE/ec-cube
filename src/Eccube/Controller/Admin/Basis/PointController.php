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
        $baseinfo = $app['eccube.repository.baseinfo']->findAll();
        $baseinfo = $baseinfo_t[0];
        
        $form = $app['form.factory']
            ->createBuilder('point', $baseinfo)
            ->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $app['orm.em']->persist($data);
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
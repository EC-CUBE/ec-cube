<?php

namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class TradelawController extends AbstractController
{
    private $main_title;

    private $title;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->title = '特定商取引法';
    }
    
    public function index(Application $app)
    {

        $baseinfo = $app['eccube.repository.base_info']->findAll();
        $baseinfo = $baseinfo[0];
   
        $form = $app['form.factory']
            ->createBuilder('tradelaw', $baseinfo)
            ->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $app['orm.em']->persist($baseinfo);
                $app['orm.em']->flush();
                $app['session']->getFlashBag()->add('tradelaw.complete', 'admin.register.complete');
                return $app->redirect($app['url_generator']->generate('admin_basis_tradelaw'));
            }
        }
        
        return $app['twig']->render('Admin/Basis/tradelaw.twig', array(
            'main_title' => $this->main_title,
            'title'      => $this->title,
            'form'       => $form->createView(),
        ));
    }
}
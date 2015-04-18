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

        $baseinfo = $app['eccube.repository.baseinfo']->findAll();
        $baseinfo = $baseinfo[0];
        
        $form = $app['form.factory']
            ->createBuilder('point', $baseinfo)
            ->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $app['orm.em']->persist($data);
                $app['orm.em']->flush();
                return $app->redirect($app['url_generator']->generate('admin_basis_tradelaw'));
            }
        }
        
        return $app['twig']->render('Admin/Basis/tradelaw.twig', array(
            'main_title' => $this->main_title,
            'title'      => $this->title,
            'form'       => $form->createView(),
        ));

/*
        $baseinfo = $app['eccube.repository.baseinfo']->findAll();
        $baseinfo = $baseinfo[0];
        
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
*/
    }
}
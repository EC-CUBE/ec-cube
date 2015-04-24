<?php

namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class BasisController extends AbstractController
{
    private $main_title;
    private $sub_title;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->sub_title = 'SHOPマスター';

        $this->tpl_subno = 'index';
        $this->tpl_mainno = 'basis';
    }

    public function Index(Application $app)
    {
        $BaseInfo = $app['eccube.repository.base_info']->get();
        // FIXME: ArrayにしたりStringにしたりやめたい
        $BaseInfo->setRegularHolidayIds(explode('|', $BaseInfo->getRegularHolidayIds()));

        $form = $app['form.factory']
            ->createBuilder('shop_master', $BaseInfo)
            ->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                // FIXME: ArrayにしたりStringにしたりやめたい
                $BaseInfo->setRegularHolidayIds(implode('|', $BaseInfo->getRegularHolidayIds()));
                $app['orm.em']->persist($BaseInfo);
                $app['orm.em']->flush();
                $app['session']->getFlashBag()->add('shop_master.complete', 'admin.register.complete');
                return $app->redirect($app['url_generator']->generate('admin_basis'));
            }
        }

        return $app['twig']->render('Admin/Basis/shop_master.twig', array(
            'tpl_maintitle' => $this->main_title,
            'tpl_subtitle'  => $this->sub_title,
            'form'       => $form->createView(),
        ));
    }
}
<?php

namespace Eccube\Controller\Admin\Order;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
 
class OrderController
{
    public $title;

    public function __construct()
    {
        $this->title = '受注マスター';
    }

    public function index(Application $app)
    {
        $Orders = array();

        $form = $app['form.factory']
            ->createBuilder('order_search')
            ->getForm();

        $showResult = false;

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $showResult = true;

                $qb = $app['orm.em']
                    ->getRepository('Eccube\Entity\Order')
                    ->getQueryBuilderBySearchData($form->getData());
                $query = $qb->getQuery();
                $Orders = $query->getResult();
            }

        }

        return $app['view']->render('Admin/Order/index.twig', array(
            'form' => $form->createView(),
            'showResult' => $showResult,
            'Orders' => $Orders,
            'title' => $this->title,
            'tpl_maintitle' => '受注管理＞受注一覧',
        ));
    }

    public function delete(Application $app, $orderId)
    {
        $Order = $app['orm.em']->getRepository('Eccube\Entity\Order')
            ->find($orderId);

        if ($Order) {
            $Order->setDelFlg(1);
            $app['orm.em']->persist($Order);
            $app['orm.em']->flush();

            $app['session']->getFlashBag()->add('admin.order.complete', 'admin.order.delete.complete');
        }

        return $this->index($app);
    }

}
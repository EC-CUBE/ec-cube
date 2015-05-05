<?php

namespace Eccube\Controller\Admin\Order;

use Doctrine\ORM\Query;
use Eccube\Application;

class StatusController
{
    protected $title;
    protected $subtitle;

    public function __construct()
    {
        $this->title = '受注管理';
        $this->subtitle = '対応状況管理';
    }

    public function index(Application $app, $statusId = 1)
    {
        $OrderStatuses = $app['orm.em']
            ->getRepository('\Eccube\Entity\Master\OrderStatus')
            ->findAllArray()
        ;
        $CurrentStatus =  $app['orm.em']
            ->getRepository('\Eccube\Entity\Master\OrderStatus')
            ->find($statusId)
        ;
        $Orders = $app['orm.em']
            ->getRepository('\Eccube\Entity\Order')
            ->findBy(array(
                'OrderStatus' => $CurrentStatus,
            ))
        ;
        $Payment = $app['orm.em']
            ->getRepository('\Eccube\Entity\Payment')
            ->findAllArray()
        ;

        $form = $app['form.factory']
            ->createBuilder()
            ->add('move', 'collection', array(
                'type'   => 'checkbox',
                'prototype' => true,
                'allow_add' => true,
            ))
            ->add('status', 'order_status', array(
                'expanded' => false,
                'multiple' => false,
            ))
            ->getForm()
        ;

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $data = $form->getData();
                foreach ($data['move'] as $orderId) {
                    $app['eccube.repository.order']->changeStatus($orderId, $data['status']);
                }
            }
        }

        return $app['view']->render('Admin/Order/status.twig', array(
            'form' => $form->createView(),
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'Payment' => $Payment,
            'Orders' => $Orders,
            'OrderStatuses' => $OrderStatuses,
            'CurrentStatus' => $CurrentStatus,
        ));
    }

}

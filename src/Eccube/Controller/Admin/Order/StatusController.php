<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller\Admin\Order;

use Doctrine\ORM\Query;
use Eccube\Application;

class StatusController
{
    protected $title;
    protected $subtitle;

    public function __construct()
    {
    }

    public function index(Application $app, $id = 1)
    {
        $OrderStatuses = $app['orm.em']
            ->getRepository('\Eccube\Entity\Master\OrderStatus')
            ->findAllArray()
        ;
        $CurrentStatus =  $app['orm.em']
            ->getRepository('\Eccube\Entity\Master\OrderStatus')
            ->find($id)
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
                
                $app->redirect($app['url_generator']->generate('admin_order_status', array('id' => $id)));
            }
        }

        return $app['view']->render('Order/status.twig', array(
            'form' => $form->createView(),
            'Payment' => $Payment,
            'Orders' => $Orders,
            'OrderStatuses' => $OrderStatuses,
            'CurrentStatus' => $CurrentStatus,
        ));
    }

}

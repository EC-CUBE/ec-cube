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

/**
 * Created by PhpStorm.
 * User: chihiro_adachi
 * Date: 15/04/23
 * Time: 14:01
 */

namespace Eccube\Controller\Admin\Order;

use Eccube\Application;
use Symfony\Component\HttpKernel\Exception as HttpException;

class EditController
{
    public function index(Application $app, $id = 0)
    {
        if ($id == 0) {
            $Order = $app['eccube.service.order']->newOrder();
        } else {
            $Order = $app['orm.em']
                ->getRepository('Eccube\Entity\Order')
                ->find($id);
        }
        if (is_null($Order)) {
            throw new HttpException\NotFoundHttpException('order is not found.');
        }

        $form = $app['form.factory']
            ->createBuilder('order', $Order)
            ->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $Order = $form->getData();
                $OrderDetails = $Order->getOrderDetails();
                $Shippings = $Order->getShippings();

                $app['orm.em']->persist($Order);
                $app['orm.em']->flush();
                // TODO: リダイレクトすると検索条件が消える
                // return $app->redirect($app['url_generator']->generate('admin_order'));
            }
        }

        return $app['view']->render('Admin/Order/edit.twig', array(
                'form' => $form->createView(),
                'Order' => $Order,
                'orderId' => $id,
        ));
    }
}

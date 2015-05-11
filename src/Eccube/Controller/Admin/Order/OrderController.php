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

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class OrderController
{
    public $title;

    public function __construct()
    {
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
        ));
    }

    public function delete(Application $app, $id)
    {
        $Order = $app['orm.em']->getRepository('Eccube\Entity\Order')
            ->find($id);

        if ($Order) {
            $Order->setDelFlg(1);
            $app['orm.em']->persist($Order);
            $app['orm.em']->flush();

            $app['session']->getFlashBag()->add('admin.order.complete', 'admin.order.delete.complete');
        }

        return $this->index($app);
    }
}

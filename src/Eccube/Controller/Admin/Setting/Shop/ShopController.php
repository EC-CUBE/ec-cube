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


namespace Eccube\Controller\Admin\Setting\Shop;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class ShopController extends AbstractController
{
    public function index(Application $app)
    {
        $BaseInfo = $app['eccube.repository.base_info']->get();

        $form = $app['form.factory']
            ->createBuilder('shop_master', $BaseInfo)
            ->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $app['orm.em']->persist($BaseInfo);
                $app['orm.em']->flush();
                $app->addSuccess('admin.shop.save.complete', 'admin');

                return $app->redirect($app->url('admin_setting_shop'));
            }
            $app->addError('admin.shop.save.error', 'admin');
        }

        return $app->render('Setting/Shop/shop_master.twig', array(
            'form' => $form->createView(),
        ));
    }
}

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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;

class CustomerAgreementController extends AbstractController
{
    public $form;

    public function __construct()
    {
    }

    public function index(Application $app, Request $request)
    {
        $Help = $app['eccube.repository.help']->get();

        $builder = $app['form.factory']
            ->createBuilder('customer_agreement', $Help);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Help' => $Help,
            ),
            $request
        );

        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CUSTOMER_AGREEMENT_INDEX_INITIALIZE, $event);
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Help = $form->getData();
            $app['orm.em']->persist($Help);

            $app['orm.em']->flush();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Help' => $Help,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CUSTOMER_AGREEMENT_INDEX_COMPLETE, $event);

            $app->addSuccess('admin.register.complete', 'admin');

            return $app->redirect($app->url('admin_setting_shop_customer_agreement'));

        }

        return $app->render('Setting/Shop/customer_agreement.twig', array(
            'form' => $form->createView(),
        ));
    }
}
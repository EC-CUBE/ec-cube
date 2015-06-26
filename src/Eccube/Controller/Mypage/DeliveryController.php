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


namespace Eccube\Controller\Mypage;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DeliveryController extends AbstractController
{
    /**
     * Index
     *
     * @param  Application $app
     * @return string
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app['user'];

        return $app->render('Mypage/delivery.twig', array(
            'Customer' => $Customer,
        ));
    }

    /**
     * edit
     *
     * @param  Application $app
     * @request  Symfony\Component\HttpFoundation\Request $app
     * @return mixed
     */
    public function edit(Application $app, Request $request, $id = null)
    {
        $Customer = $app['user'];

        $CustomerAddress = $app['eccube.repository.customer_address']->findOrCreateByCustomerAndId($Customer, $id);

        $parentPage = $request->get('parent_page', null);

        // 正しい遷移かをチェック
        $allowdParents = array(
            $app->url('mypage_delivery'),
            $app->url('shopping_delivery'),
        );

        // 遷移が正しくない場合、デフォルトであるマイページの配送先追加の画面を設定する
        if (!in_array($parentPage, $allowdParents)) {
            $parentPage  = $app->url('mypage_delivery');
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createBuilder('customer_address', $CustomerAddress)
            ->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $app['orm.em']->persist($CustomerAddress);
                $app['orm.em']->flush();

                $app->addSuccess('mypage.delivery.add.complete');

                return $app->redirect($app->url('mypage_delivery'));
            }
        }

        $BaseInfo = $app['eccube.repository.base_info']->get();

        return $app->render('Mypage/delivery_edit.twig', array(
            'form' => $form->createView(),
            'parentPage' => $parentPage,
            'BaseInfo' => $BaseInfo,
        ));
    }

    public function delete(Application $app, $id)
    {
        $Customer = $app['user'];

        // 別のお届け先削除
        if ($app['eccube.repository.customer_address']->deleteByCustomerAndId($Customer, $id)) {
            $app->addError('mypage.address.delete.failed');
        } else {
            $app->addSuccess('mypage.address.delete.complete');
        }

        return $app->redirect($app->url('mypage_delivery'));
    }
}

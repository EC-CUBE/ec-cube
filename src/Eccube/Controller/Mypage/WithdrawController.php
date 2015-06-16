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

class WithdrawController extends AbstractController
{

    /**
     * Index
     *
     * @param  Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Application $app, Request $request)
    {
        /* @var $Customer \Eccube\Entity\Customer */
        $Customer = $app['user'];

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('form');

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                switch ($request->get('mode')) {
                    case 'confirm':
                        return $app['twig']->render('Mypage/withdraw_confirm.twig', array(
                            'form' => $form->createView(),
                        ));
                    case 'complete':
                        // 顧客削除
                        $Customer->setDelFlg(1);
                        $app['orm.em']->persist($Customer);
                        $app['orm.em']->flush();

                        $BaseInfo = $app['eccube.repository.base_info']->get();

                        // メール送信
                        $app['eccube.service.mail']->sendCustomerWithdrawMail($Customer, $BaseInfo);

                        // ログアウト
                        $this->getSecurity($app)->setToken(null);

                        return $app->redirect($app->url('mypage_withdraw_complete'));
                }
            }
        }

        return $app['twig']->render('Mypage/withdraw.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Complete
     *
     * @param  Application $app
     * @return mixed
     */
    public function complete(Application $app, Request $request)
    {
        $BaseInfo = $app['eccube.repository.base_info']->get();

        return $app['view']->render('Mypage/withdraw_complete.twig', array(
            'BaseInfo' => $BaseInfo,
        ));
    }
}

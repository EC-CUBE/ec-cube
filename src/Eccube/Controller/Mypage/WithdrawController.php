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
use Eccube\Util\Str;
use Eccube\Common\Constant;
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

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app->form()->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                switch ($request->get('mode')) {
                    case 'confirm':
                        return $app->renderView('Mypage/withdraw_confirm.twig', array(
                            'form' => $form->createView(),
                        ));
                    case 'complete':

                        /* @var $Customer \Eccube\Entity\Customer */
                        $Customer = $app->user();

                        // 会員削除
                        $email = $Customer->getEmail();
                        // メールアドレスにダミーをセット
                        $Customer->setEmail(Str::random(60) . '@dummy.dummy');
                        $Customer->setDelFlg(Constant::ENABLED);

                        $app['orm.em']->flush();

                        // メール送信
                        $app['eccube.service.mail']->sendCustomerWithdrawMail($Customer, $email);

                        // ログアウト
                        $this->getSecurity($app)->setToken(null);

                        return $app->redirect($app->url('mypage_withdraw_complete'));
                }
            }
        }

        return $app->renderView('Mypage/withdraw.twig', array(
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
        return $app->renderView('Mypage/withdraw_complete.twig');
    }
}

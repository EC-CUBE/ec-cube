<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Util\Str;
use Symfony\Component\HttpFoundation\Request;

class WithdrawController extends AbstractController
{
    /**
     * 退会画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $builder = $app->form();

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    log_info('退会確認画面表示');

                    return $app->render('Mypage/withdraw_confirm.twig', array(
                        'form' => $form->createView(),
                    ));

                case 'complete':
                    log_info('退会処理開始');

                    /* @var $Customer \Eccube\Entity\Customer */
                    $Customer = $app->user();

                    // 会員削除
                    $email = $Customer->getEmail();
                    // メールアドレスにダミーをセット
                    $Customer->setEmail(Str::random(60) . '@dummy.dummy');
                    $Customer->setDelFlg(Constant::ENABLED);

                    $app['orm.em']->flush();

                    log_info('退会処理完了');

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Customer' => $Customer,
                        ), $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE, $event);

                    // メール送信
                    $app['eccube.service.mail']->sendCustomerWithdrawMail($Customer, $email);

                    // ログアウト
                    $this->getSecurity($app)->setToken(null);

                    log_info('ログアウト完了');

                    return $app->redirect($app->url('mypage_withdraw_complete'));
            }
        }

        return $app->render('Mypage/withdraw.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * 退会完了画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function complete(Application $app, Request $request)
    {
        return $app->render('Mypage/withdraw_complete.twig');
    }
}

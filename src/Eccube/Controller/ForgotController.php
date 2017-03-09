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

namespace Eccube\Controller;

use Eccube\Application;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\Validator\Constraints as Assert;

class ForgotController extends AbstractController
{
    /**
     * パスワードリマインダ.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        $builder = $app['form.factory']
            ->createNamedBuilder('', 'forgot');

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_FORGOT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Customer = $app['eccube.repository.customer']
                ->getActiveCustomerByEmail($form->get('login_email')->getData());

            if (!is_null($Customer)) {
                // リセットキーの発行・有効期限の設定
                $Customer
                    ->setResetKey($app['eccube.repository.customer']->getUniqueResetKey($app))
                    ->setResetExpire(new \DateTime('+' . $app['config']['customer_reset_expire'] .' min'));

                // リセットキーを更新
                $app['orm.em']->persist($Customer);
                $app['orm.em']->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Customer' => $Customer,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_FORGOT_INDEX_COMPLETE, $event);

                // 完了URLの生成
                $reset_url = $app->url('forgot_reset', array('reset_key' => $Customer->getResetKey()));

                // メール送信
                $app['eccube.service.mail']->sendPasswordResetNotificationMail($Customer, $reset_url);

                // ログ出力
                $app['monolog']->addInfo(
                    'send reset password mail to:'  . "{$Customer->getId()} {$Customer->getEmail()} {$request->getClientIp()}"
                );
            } else {
                log_warning('Un active customer try send reset password email: ', array('Enter email' => $form->get('login_email')->getData()));
            }

            return $app->redirect($app->url('forgot_complete'));
        }

        return $app->render('Forgot/index.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * パスワードリマインダ完了画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function complete(Application $app, Request $request)
    {
        return $app->render('Forgot/complete.twig');
    }

    /**
     * パスワード再発行実行画面.
     *
     * @param Application $app
     * @param Request $request
     * @param $reset_key
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reset(Application $app, Request $request, $reset_key)
    {
        $errors = $app['validator']->validateValue($reset_key, array(
            new Assert\NotBlank(),
            new Assert\Regex(array(
                'pattern' => '/^[a-zA-Z0-9]+$/',
            )))
        );

        if ('GET' === $request->getMethod()
                && count($errors) === 0) {
            try {
                $Customer = $app['eccube.repository.customer']
                    ->getActiveCustomerByResetKey($reset_key);
            } catch (\Exception $e) {
                throw new HttpException\NotFoundHttpException('有効期限が切れているか、無効なURLです。');
            }

            // パスワードの発行・更新
            $pass = $app['eccube.repository.customer']->getResetPassword();
            $Customer->setPassword($pass);

            // 発行したパスワードの暗号化
            if ($Customer->getSalt() === null) {
                $Customer->setSalt($app['eccube.repository.customer']->createSalt(5));
            }
            $encPass = $app['eccube.repository.customer']->encryptPassword($app, $Customer);
            $Customer->setPassword($encPass);

            $Customer->setResetKey(null);

            // パスワードを更新
            $app['orm.em']->persist($Customer);
            $app['orm.em']->flush();

            $event = new EventArgs(
                array(
                    'Customer' => $Customer,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_FORGOT_RESET_COMPLETE, $event);

            // メール送信
            $app['eccube.service.mail']->sendPasswordResetCompleteMail($Customer, $pass);

            // ログ出力
            $app['monolog']->addInfo(
                'reset password complete:' . "{$Customer->getId()} {$Customer->getEmail()} {$request->getClientIp()}"
            );
        } else {
            throw new HttpException\AccessDeniedHttpException('不正なアクセスです。');
        }

        return $app->render('Forgot/reset.twig');
    }

}

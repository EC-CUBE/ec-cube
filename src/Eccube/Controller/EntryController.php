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


namespace Eccube\Controller;

use Eccube\Application;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints as Assert;

class EntryController extends AbstractController
{

    /**
     * 会員登録画面.
     *
     * @param  Application $app
     * @param  Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        if ($app->isGranted('ROLE_USER')) {
            log_info('認証済のためログイン処理をスキップ');

            return $app->redirect($app->url('mypage'));
        }

        /** @var $Customer \Eccube\Entity\Customer */
        $Customer = $app['eccube.repository.customer']->newCustomer();

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('entry', $Customer);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_ENTRY_INDEX_INITIALIZE, $event);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    log_info('会員登録確認開始');
                    $builder->setAttribute('freeze', true);
                    $form = $builder->getForm();
                    $form->handleRequest($request);
                    log_info('会員登録確認完了');

                    return $app->render('Entry/confirm.twig', array(
                        'form' => $form->createView(),
                    ));

                case 'complete':
                    log_info('会員登録開始');
                    $Customer
                        ->setSalt(
                            $app['eccube.repository.customer']->createSalt(5)
                        )
                        ->setPassword(
                            $app['eccube.repository.customer']->encryptPassword($app, $Customer)
                        )
                        ->setSecretKey(
                            $app['eccube.repository.customer']->getUniqueSecretKey($app)
                        );

                    $CustomerAddress = new \Eccube\Entity\CustomerAddress();
                    $CustomerAddress
                        ->setFromCustomer($Customer);

                    $app['orm.em']->persist($Customer);
                    $app['orm.em']->persist($CustomerAddress);
                    $app['orm.em']->flush();

                    log_info('会員登録完了');

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Customer' => $Customer,
                            'CustomerAddress' => $CustomerAddress,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_ENTRY_INDEX_COMPLETE, $event);

                    $activateUrl = $app->url('entry_activate', array('secret_key' => $Customer->getSecretKey()));

                    /** @var $BaseInfo \Eccube\Entity\BaseInfo */
                    $BaseInfo = $app['eccube.repository.base_info']->get();
                    $activateFlg = $BaseInfo->getOptionCustomerActivate();

                    // 仮会員設定が有効な場合は、確認メールを送信し完了画面表示.
                    if ($activateFlg) {
                        // メール送信
                        $app['eccube.service.mail']->sendCustomerConfirmMail($Customer, $activateUrl);

                        if ($event->hasResponse()) {
                            return $event->getResponse();
                        }

                        log_info('仮会員登録完了画面へリダイレクト');

                        return $app->redirect($app->url('entry_complete'));
                        // 仮会員設定が無効な場合は認証URLへ遷移させ、会員登録を完了させる.
                    } else {
                        log_info('本会員登録画面へリダイレクト');

                        return $app->redirect($activateUrl);
                    }
            }
        }

        return $app->render('Entry/index.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * 会員登録完了画面.
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function complete(Application $app)
    {
        return $app->render('Entry/complete.twig', array());
    }

    /**
     * 会員のアクティベート（本会員化）を行う.
     *
     * @param Application $app
     * @param Request $request
     * @param $secret_key
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activate(Application $app, Request $request, $secret_key)
    {
        $errors = $app['validator']->validateValue($secret_key, array(
                new Assert\NotBlank(),
                new Assert\Regex(array(
                    'pattern' => '/^[a-zA-Z0-9]+$/',
                ))
            )
        );

        if ($request->getMethod() === 'GET' && count($errors) === 0) {
            log_info('本会員登録開始');
            try {
                $Customer = $app['eccube.repository.customer']
                    ->getNonActiveCustomerBySecretKey($secret_key);
            } catch (\Exception $e) {
                throw new HttpException\NotFoundHttpException('※ 既に会員登録が完了しているか、無効なURLです。');
            }

            $CustomerStatus = $app['eccube.repository.customer_status']->find(CustomerStatus::ACTIVE);
            $Customer->setStatus($CustomerStatus);
            $app['orm.em']->persist($Customer);
            $app['orm.em']->flush();

            log_info('本会員登録完了');

            $event = new EventArgs(
                array(
                    'Customer' => $Customer,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_ENTRY_ACTIVATE_COMPLETE, $event);

            // メール送信
            $app['eccube.service.mail']->sendCustomerCompleteMail($Customer);

            // 本会員登録してログイン状態にする
            $token = new UsernamePasswordToken($Customer, null, 'customer', array('ROLE_USER'));
            $this->getSecurity($app)->setToken($token);
            $request->getSession()->migrate(true, $app['config']['cookie_lifetime']);

            log_info('ログイン済に変更', array($app->user()->getId()));

            return $app->render('Entry/activate.twig');
        } else {
            throw new HttpException\AccessDeniedHttpException('不正なアクセスです。');
        }
    }
}

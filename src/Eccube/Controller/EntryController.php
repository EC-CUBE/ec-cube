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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EntryController extends AbstractController
{

    /**
     * Index
     *
     * @param  Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app['eccube.repository.customer']->newCustomer();

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('entry', $Customer);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                switch ($request->get('mode')) {
                    case 'confirm':
                        $builder->setAttribute('freeze', true);
                        $form = $builder->getForm();
                        $form->handleRequest($request);

                        return $app['twig']->render('Entry/confirm.twig', array(
                            'form' => $form->createView(),
                        ));
                        break;

                    case 'complete':
                        $Customer->setSalt(
                            $app['eccube.repository.customer']
                                ->createSalt(5)
                        );

                        $Customer->setPassword(
                            $app['eccube.repository.customer']
                                ->encryptPassword($app, $Customer)
                        );

                        $Customer->setSecretKey(
                            $app['orm.em']
                                ->getRepository('Eccube\Entity\Customer')
                                ->getUniqueSecretKey($app)
                        );

                        $CustomerAddress = new \Eccube\Entity\CustomerAddress();
                        $CustomerAddress->setName01($Customer->getName01())
                            ->setName02($Customer->getName02())
                            ->setKana01($Customer->getKana01())
                            ->setKana02($Customer->getKana02())
                            ->setZip01($Customer->getZip01())
                            ->setZip02($Customer->getZip02())
                            ->setZipcode($Customer->getZip01() . $Customer->getZip02())
                            ->setAddr01($Customer->getAddr01())
                            ->setAddr02($Customer->getAddr02())
                            ->setTel01($Customer->getTel01())
                            ->setTel02($Customer->getTel02())
                            ->setTel03($Customer->getTel03())
                            ->setFax01($Customer->getFax01())
                            ->setFax02($Customer->getFax02())
                            ->setFax03($Customer->getFax03())
                            ->setDelFlg($app['config']['disabled'])
                            ->setCustomer($Customer);

                        $app['orm.em']->persist($Customer);
                        $app['orm.em']->persist($CustomerAddress);
                        $app['orm.em']->flush();

                        $activateUrl = $app->url('entry_activate', array(
                                'id' => $Customer->getSecretKey()
                            ), true);


                        if ($app['config']['customer_confirm_mail']) {
                            // TODO: 後でEventとして実装する、送信元アドレス、BCCを調整する
                            // $app['eccube.event.dispatcher']->dispatch('customer.regist::after');
                            $message = $app['mailer']->createMessage()
                                ->setSubject('[EC-CUBE3] 会員登録のご確認')
                                ->setBody($app['view']->render('Mail/entry_confirm.twig', array(
                                    'customer' => $Customer,
                                    'activateUrl' => $activateUrl,
                                )))
                                ->setFrom(array('sample@example.com'))
                                ->setBcc($app['config']['mail_cc'])
                                ->setTo(array($Customer->getEmail()));
                            $app['mailer']->send($message);

                            return $app->redirect($app->url('entry_complete'));

                        } else {
                            return $app->redirect($activateUrl);
                        }

                        break;
                }
            }
        }

        return $app['view']->render('Entry/index.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Complete
     *
     * @param  Application $app
     * @return mixed
     */
    public function complete(Application $app)
    {
        return $app['view']->render('Entry/complete.twig', array(
        ));
    }

    /**
     * 会員のアクティベート（本会員化）を行う
     *
     * @param  Application $app
     * @return mixed
     */
    public function activate(Application $app, Request $request)
    {
        $secret_key = $request->get('id');
        $errors = $app['validator']->validateValue($secret_key, array(
                new Assert\NotBlank(),
                new Assert\Regex(array(
                    'pattern' => '/^[a-zA-Z0-9]+$/',
                ))
            )
            );

        if ($request->getMethod() === 'GET' && count($errors) === 0) {
            try {
                $Customer = $app['eccube.repository.customer']
                    ->getNonActiveCustomerBySecretKey($secret_key);
            } catch (\Exception $e) {
                throw new HttpException\NotFoundHttpException('※ 既に会員登録が完了しているか、無効なURLです。');
            }

            $CustomerStatus = $app['orm.em']
                ->getRepository('Eccube\Entity\Master\CustomerStatus')
                ->find(2);
            $Customer->setStatus($CustomerStatus);

            $app['orm.em']->persist($Customer);
            $app['orm.em']->flush();

            $message = $app['mail.message']
                ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
                ->setBody($app['view']->render('Mail/entry_complete.twig', array(
                    'customer' => $Customer,
                )))
                ->setFrom(array('sample@example.com'))
                ->setBcc($app['config']['mail_cc'])
                ->setTo(array($Customer->getEmail()));
            $app['mailer']->send($message);

            // 本会員登録
            $token = new UsernamePasswordToken($Customer, null, 'customer', array('ROLE_USER'));
            $app['security']->setToken($token);

            return $app['view']->render('Entry/activate.twig');
        } else {
            throw new HttpException\AccessDeniedHttpException('不正なアクセスです。');
        }
    }
}

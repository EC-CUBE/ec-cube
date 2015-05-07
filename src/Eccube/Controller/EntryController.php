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

class EntryController extends AbstractController
{
    private $title;

    public $form;

    public function __construct()
    {
        $this->title = '会員登録';

    }

    /**
     * Index
     *
     * @param  Application                                        $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Application $app, Request $request)
    {
        $Customer = $app['eccube.repository.customer']->newCustomer();

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('entry', $Customer);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                switch ($request->get('mode')) {
                    case 'confirm':
                        $builder->setAttribute('freeze', true);
                        $form = $builder->getForm();
                        $form->handleRequest($request);

                        return $app['twig']->render('Entry/confirm.twig', array(
                            'title' => $this->title,
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

                        $app['orm.em']->persist($Customer);
                        $app['orm.em']->flush();

                        $activateUrl = $app['url_generator']
                            ->generate('entry_activate', array(
                                'id' => $Customer->getSecretKey()
                            ), true);

                        if ($app['config']['customer_confirm_mail']) {
                            // TODO: 後でEventとして実装する、送信元アドレス、BCCを調整する
                            // $app['eccube.event.dispatcher']->dispatch('customer.regist::after');
                            $message = $app['mailer']->createMessage()
                                ->setSubject('[EC-CUBE3] 会員登録のご確認')
                                ->setBody($app['view']->render('Mail/entry_confirm.twig', array(
                                    'Customer' => $Customer,
                                    'activateUrl' => $activateUrl,
                                )))
                                ->setFrom(array('sample@example.com'))
                                ->setBcc($app['config']['mail_cc'])
                                ->setTo(array($Customer->getEmail()));
                            $app['mailer']->send($message);

                            return $app->redirect($app['url_generator']->generate('entry_complete'));
                        } else {
                            return $app->redirect($activateUrl);
                        }

                        break;
                }
            }
        }

        $kiyaku = $app['orm.em']
            ->getRepository('Eccube\Entity\Kiyaku')
            ->findAll();

        return $app['view']->render('Entry/index.twig', array(
            'title' => $this->title,
            'kiyaku' => $kiyaku,
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
            'title' => $this->title,
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
            )        );

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

            return $app['view']->render('Entry/activate.twig', array(
                'title' => $this->title,
            ));
        } else {
            throw new HttpException\AccessDeniedHttpException('不正なアクセスです。');
        }
    }
}

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


namespace Eccube\Controller\Admin\Order;

use Eccube\Application;
use Eccube\Entity\MailHistory;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MailController
{
    public function index(Application $app, Request $request, $id)
    {
        $Order = $app['eccube.repository.order']->find($id);

        if (is_null($Order)) {
            throw new NotFoundHttpException('order not found.');
        }

        $MailHistories = $app['eccube.repository.mail_history']->findBy(array('Order' => $id));

        $builder = $app['form.factory']->createBuilder('mail');

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
                'MailHistories' => $MailHistories,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            $mode = $request->get('mode');

            // テンプレート変更の場合は. バリデーション前に内容差し替え.
            if ($mode == 'change') {
                if ($form->get('template')->isValid()) {
                    /** @var $data \Eccube\Entity\MailTemplate */
                    $MailTemplate = $form->get('template')->getData();
                    $form = $builder->getForm();
                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Order' => $Order,
                            'MailTemplate' => $MailTemplate,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_CHANGE, $event);
                    $form->get('template')->setData($MailTemplate);
                    $form->get('subject')->setData($MailTemplate->getSubject());
                    $form->get('header')->setData($MailTemplate->getHeader());
                    $form->get('footer')->setData($MailTemplate->getFooter());
                }
            } else if ($form->isValid()) {
                switch ($mode) {
                    case 'confirm':
                        // フォームをFreezeして再生成.

                        $builder->setAttribute('freeze', true);
                        $builder->setAttribute('freeze_display_text', true);

                        $data = $form->getData();
                        $body = $this->createBody($app, $data['header'], $data['footer'], $Order);

                        $MailTemplate = $form->get('template')->getData();

                        $form = $builder->getForm();

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'Order' => $Order,
                                'MailTemplate' => $MailTemplate,
                            ),
                            $request
                        );
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_CONFIRM, $event);

                        $form->setData($data);
                        $form->get('template')->setData($MailTemplate);


                        return $app->render('Order/mail_confirm.twig', array(
                            'form' => $form->createView(),
                            'body' => $body,
                            'Order' => $Order,
                        ));
                        break;
                    case 'complete':

                        $data = $form->getData();

                        // メール送信
                        $message = $app['eccube.service.mail']->sendAdminOrderMail($Order, $data);

                        // 送信履歴を保存.
                        $MailTemplate = $form->get('template')->getData();
                        $MailHistory = new MailHistory();
                        $MailHistory
                            ->setSubject($message->getSubject())
                            ->setMailBody($message->getBody())
                            ->setMailTemplate($MailTemplate)
                            ->setSendDate(new \DateTime())
                            ->setOrder($Order);

                        $app['orm.em']->persist($MailHistory);
                        $app['orm.em']->flush($MailHistory);

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'Order' => $Order,
                                'MailTemplate' => $MailTemplate,
                                'MailHistory' => $MailHistory,
                            ),
                            $request
                        );
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_COMPLETE, $event);


                        return $app->redirect($app->url('admin_order_mail_complete'));
                        break;
                    default:
                        break;
                }
            }
        }

        return $app->render('Order/mail.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
            'MailHistories' => $MailHistories
        ));
    }


    public function complete(Application $app)
    {
        return $app->render('Order/mail_complete.twig');
    }


    public function view(Application $app, Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $MailHistory = $app['eccube.repository.mail_history']->find($id);

            if (is_null($MailHistory)) {
                throw new NotFoundHttpException('history not found.');
            }

            $event = new EventArgs(
                array(
                    'MailHistory' => $MailHistory,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_VIEW_COMPLETE, $event);

            return $app->render('Order/mail_view.twig', array(
                'subject' => $MailHistory->getSubject(),
                'body' => $MailHistory->getMailBody()
            ));
        }

    }



    public function mailAll(Application $app, Request $request)
    {

        $builder = $app['form.factory']->createBuilder('mail');

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_INITIALIZE, $event);

        $form = $builder->getForm();

        $ids = '';
        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            $mode = $request->get('mode');

            $ids = $request->get('ids');

            // テンプレート変更の場合は. バリデーション前に内容差し替え.
            if ($mode == 'change') {
                if ($form->get('template')->isValid()) {
                    /** @var $data \Eccube\Entity\MailTemplate */
                    $MailTemplate = $form->get('template')->getData();
                    $form = $builder->getForm();

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'MailTemplate' => $MailTemplate,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_CHANGE, $event);

                    $form->get('template')->setData($MailTemplate);
                    $form->get('subject')->setData($MailTemplate->getSubject());
                    $form->get('header')->setData($MailTemplate->getHeader());
                    $form->get('footer')->setData($MailTemplate->getFooter());
                }
            } else if ($form->isValid()) {
                switch ($mode) {
                    case 'confirm':
                        // フォームをFreezeして再生成.

                        $builder->setAttribute('freeze', true);
                        $builder->setAttribute('freeze_display_text', true);

                        $data = $form->getData();

                        $tmp = explode(',', $ids);

                        $Order = $app['eccube.repository.order']->find($tmp[0]);

                        if (is_null($Order)) {
                            throw new NotFoundHttpException('order not found.');
                        }

                        $body = $this->createBody($app, $data['header'], $data['footer'], $Order);

                        $MailTemplate = $form->get('template')->getData();

                        $form = $builder->getForm();

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'MailTemplate' => $MailTemplate,
                                'Order' => $Order,
                            ),
                            $request
                        );
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_CONFIRM, $event);

                        $form->setData($data);
                        $form->get('template')->setData($MailTemplate);

                        return $app->render('Order/mail_all_confirm.twig', array(
                            'form' => $form->createView(),
                            'body' => $body,
                            'ids' => $ids,
                        ));
                        break;

                    case 'complete':

                        $data = $form->getData();

                        $ids = explode(',', $ids);

                        foreach ($ids as $value) {

                            $Order = $app['eccube.repository.order']->find($value);


                            // メール送信
                            $message = $app['eccube.service.mail']->sendAdminOrderMail($Order, $data);

                            // 送信履歴を保存.
                            $MailTemplate = $form->get('template')->getData();
                            $MailHistory = new MailHistory();
                            $MailHistory
                                ->setSubject($message->getSubject())
                                ->setMailBody($message->getBody())
                                ->setMailTemplate($MailTemplate)
                                ->setSendDate(new \DateTime())
                                ->setOrder($Order);
                            $app['orm.em']->persist($MailHistory);
                        }

                        $app['orm.em']->flush($MailHistory);

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'MailHistory' => $MailHistory,
                            ),
                            $request
                        );
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_COMPLETE, $event);

                        return $app->redirect($app->url('admin_order_mail_complete'));
                        break;
                    default:
                        break;
                }
            }
        } else {
            $filter = function ($v) {
                return preg_match('/^ids\d+$/', $v);
            };
            $map = function ($v) {
                return preg_replace('/[^\d+]/', '', $v);
            };
            $keys = array_keys($request->query->all());
            $idArray = array_map($map, array_filter($keys, $filter));
            $ids = implode(',', $idArray);
        }

        return $app->render('Order/mail_all.twig', array(
            'form' => $form->createView(),
            'ids' => $ids,
        ));
    }


    private function createBody($app, $header, $footer, $Order)
    {
        return $app->renderView('Mail/order.twig', array(
            'header' => $header,
            'footer' => $footer,
            'Order' => $Order,
        ));
    }
}

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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Component;
use Eccube\Application;
use Eccube\Entity\MailHistory;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MailType;
use Eccube\Repository\MailHistoryRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Component
 * @Route(service=MailController::class)
 */
class MailController
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject(MailService::class)
     * @var MailService
     */
    protected $mailService;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(MailHistoryRepository::class)
     * @var MailHistoryRepository
     */
    protected $mailHistoryRepository;

    /**
     * @Inject(OrderRepository::class)
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @Route("/{_admin}/order/{id}/mail", requirements={"id" = "\d+"}, name="admin_order_mail")
     * @Template("Order/mail.twig")
     */
    public function index(Application $app, Request $request, $id)
    {
        $Order = $this->orderRepository->find($id);

        if (is_null($Order)) {
            throw new NotFoundHttpException('order not found.');
        }

        $MailHistories = $this->mailHistoryRepository->findBy(array('Order' => $id));

        $builder = $this->formFactory->createBuilder(MailType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
                'MailHistories' => $MailHistories,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_INITIALIZE, $event);

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
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_CHANGE, $event);
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
                        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_CONFIRM, $event);

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
                        $body = $this->createBody($app, $data['header'], $data['footer'], $Order);

                        // メール送信
                        $this->mailService->sendAdminOrderMail($Order, $data);

                        // 送信履歴を保存.
                        $MailTemplate = $form->get('template')->getData();
                        $MailHistory = new MailHistory();
                        $MailHistory
                            ->setSubject($data['subject'])
                            ->setMailBody($body)
                            ->setMailTemplate($MailTemplate)
                            ->setSendDate(new \DateTime())
                            ->setOrder($Order);

                        $this->entityManager->persist($MailHistory);
                        $this->entityManager->flush($MailHistory);

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'Order' => $Order,
                                'MailTemplate' => $MailTemplate,
                                'MailHistory' => $MailHistory,
                            ),
                            $request
                        );
                        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_COMPLETE, $event);


                        return $app->redirect($app->url('admin_order_mail_complete'));
                        break;
                    default:
                        break;
                }
            }
        }

        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'MailHistories' => $MailHistories
        ];
    }

    /**
     * @Route("/{_admin}/order/mail_complete", name="admin_order_mail_complete")
     * @Template("Order/mail_complete.twig")
     */
    public function complete(Application $app)
    {
        return [];
    }

    /**
     * @Route("/{_admin}/order/mail/view", name="admin_order_mail_view")
     * @Template("Order/mail_view.twig")
     */
    public function view(Application $app, Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $MailHistory = $this->mailHistoryRepository->find($id);

            if (is_null($MailHistory)) {
                throw new NotFoundHttpException('history not found.');
            }

            $event = new EventArgs(
                array(
                    'MailHistory' => $MailHistory,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_VIEW_COMPLETE, $event);

            return [
                'subject' => $MailHistory->getSubject(),
                'body' => $MailHistory->getMailBody()
            ];
        }

    }

    /**
     * @Route("/{_admin}/order/mail/mail_all", name="admin_order_mail_all")
     * @Template("Order/mail_all.twig")
     */
    public function mailAll(Application $app, Request $request)
    {

        $builder = $this->formFactory->createBuilder(MailType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_INITIALIZE, $event);

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
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_CHANGE, $event);

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

                        $Order = $this->orderRepository->find($tmp[0]);

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
                        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_CONFIRM, $event);

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

                            $Order = $this->orderRepository->find($value);

                            $body = $this->createBody($app, $data['header'], $data['footer'], $Order);

                            // メール送信
                            $this->mailService->sendAdminOrderMail($Order, $data);

                            // 送信履歴を保存.
                            $MailTemplate = $form->get('template')->getData();
                            $MailHistory = new MailHistory();
                            $MailHistory
                                ->setSubject($data['subject'])
                                ->setMailBody($body)
                                ->setMailTemplate($MailTemplate)
                                ->setSendDate(new \DateTime())
                                ->setOrder($Order);
                            $this->entityManager->persist($MailHistory);
                        }

                        $this->entityManager->flush($MailHistory);

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'MailHistory' => $MailHistory,
                            ),
                            $request
                        );
                        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_COMPLETE, $event);

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

        return [
            'form' => $form->createView(),
            'ids' => $ids,
        ];
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

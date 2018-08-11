<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Order;

use Eccube\Controller\AbstractController;
use Eccube\Entity\MailHistory;
use Eccube\Entity\Order;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MailType;
use Eccube\Repository\MailHistoryRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var MailHistoryRepository
     */
    protected $mailHistoryRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * MailController constructor.
     *
     * @param MailService $mailService
     * @param MailHistoryRepository $mailHistoryRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        MailService $mailService,
        MailHistoryRepository $mailHistoryRepository,
        OrderRepository $orderRepository
    ) {
        $this->mailService = $mailService;
        $this->mailHistoryRepository = $mailHistoryRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/order/{id}/mail", requirements={"id" = "\d+"}, name="admin_order_mail")
     * @Template("@admin/Order/mail.twig")
     */
    public function index(Request $request, Order $Order)
    {
        $MailHistories = $this->mailHistoryRepository->findBy(['Order' => $Order]);

        $builder = $this->formFactory->createBuilder(MailType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Order' => $Order,
                'MailHistories' => $MailHistories,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        // 本文確認用
        $body = $this->createBody('', '', $Order);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            $mode = $request->get('mode');

            // テンプレート変更の場合は. バリデーション前に内容差し替え.
            if ($mode == 'change') {
                if ($form->get('template')->isValid()) {
                    /** @var $data \Eccube\Entity\MailTemplate */
                    $MailTemplate = $form->get('template')->getData();
                    $data = $form->getData();

                    $twig = $MailTemplate->getFileName();
                    if (!$twig) {
                        $twig = 'Mail/order.twig';
                    }

                    $body = $this->createBody('', '', $Order, $twig);

                    $form = $builder->getForm();
                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'Order' => $Order,
                            'MailTemplate' => $MailTemplate,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_CHANGE, $event);
                    $form->get('template')->setData($MailTemplate);
                    $form->get('mail_subject')->setData($MailTemplate->getMailSubject());
                    $form->get('mail_header')->setData($MailTemplate->getMailHeader());
                    $form->get('mail_footer')->setData($MailTemplate->getMailFooter());
                }
            } else {
                if ($form->isValid()) {
                    $data = $form->getData();

                    $MailTemplate = $form->get('template')->getData();

                    $twig = $MailTemplate->getFileName();
                    if (!$twig) {
                        $twig = 'Mail/order.twig';
                    }

                    // メール送信
                    $message = $this->mailService->sendAdminOrderMail($Order, $data, $twig);

                    // 送信履歴を保存.
                    $MailTemplate = $form->get('template')->getData();
                    $MailHistory = new MailHistory();
                    $MailHistory
                        ->setMailSubject($message->getSubject())
                        ->setMailBody($message->getBody())
                        ->setSendDate(new \DateTime())
                        ->setOrder($Order);

                    $this->entityManager->persist($MailHistory);
                    $this->entityManager->flush($MailHistory);

                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'Order' => $Order,
                            'MailTemplate' => $MailTemplate,
                            'MailHistory' => $MailHistory,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_INDEX_COMPLETE, $event);

                    return $this->redirectToRoute('admin_order_page', ['page_no' => $this->session->get('eccube.admin.order.search.page_no', 1)]);
                }
            }
        }

        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'MailHistories' => $MailHistories,
            'body' => $body,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/order/mail/view", name="admin_order_mail_view")
     * @Template("@admin/Order/mail_view.twig")
     */
    public function view(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $id = $request->get('id');
        $MailHistory = $this->mailHistoryRepository->find($id);

        if (null === $MailHistory) {
            throw new NotFoundHttpException('history not found.');
        }

        $event = new EventArgs(
            [
                'MailHistory' => $MailHistory,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_VIEW_COMPLETE, $event);

        return [
            'mail_subject' => $MailHistory->getMailSubject(),
            'body' => $MailHistory->getMailBody(),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/order/mail/mail_all", name="admin_order_mail_all")
     * @Template("@admin/Order/mail_all.twig")
     */
    public function mailAll(Request $request)
    {
        $builder = $this->formFactory->createBuilder(MailType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
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
                        [
                            'form' => $form,
                            'MailTemplate' => $MailTemplate,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_CHANGE, $event);

                    $form->get('template')->setData($MailTemplate);
                    $form->get('mail_subject')->setData($MailTemplate->getMailSubject());
                    $form->get('mail_header')->setData($MailTemplate->getMailHeader());
                    $form->get('mail_footer')->setData($MailTemplate->getMailFooter());
                }
            } else {
                if ($form->isValid()) {
                    $data = $form->getData();

                    $ids = explode(',', $ids);

                    foreach ($ids as $value) {
                        $Order = $this->orderRepository->find($value);

                        $body = $this->createBody($data['mail_header'], $data['mail_footer'], $Order);

                        // メール送信
                        $this->mailService->sendAdminOrderMail($Order, $data);

                        // 送信履歴を保存.
                        $MailHistory = new MailHistory();
                        $MailHistory
                            ->setMailSubject($data['mail_subject'])
                            ->setMailBody($body)
                            ->setSendDate(new \DateTime())
                            ->setOrder($Order);
                        $this->entityManager->persist($MailHistory);
                    }

                    $this->entityManager->flush($MailHistory);

                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'MailHistory' => $MailHistory,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_COMPLETE, $event);

                    return $this->redirectToRoute('admin_order_page', ['page_no' => $this->session->get('eccube.admin.order.search.page_no', 1)]);
                }
            }
        } else {
            $ids = implode(',', (array) $request->get('ids'));
        }

        // 本文確認用
        $body = '';
        if ($ids != '') {
            $idArray = explode(',', $ids);
            $Order = $this->orderRepository->find($idArray[0]);
            $body = $this->createBody('', '', $Order);
        }

        return [
            'form' => $form->createView(),
            'ids' => $ids,
            'body' => $body,
        ];
    }

    private function createBody($header, $footer, $Order, $twig = 'Mail/order.twig')
    {
        return $this->renderView($twig, [
            'header' => $header,
            'footer' => $footer,
            'Order' => $Order,
        ]);
    }
}

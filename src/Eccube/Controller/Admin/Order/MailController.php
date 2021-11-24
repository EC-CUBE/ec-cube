<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Eccube\Form\Type\Admin\OrderMailType;
use Eccube\Repository\MailHistoryRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

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
     * @var Environment
     */
    protected $twig;

    /**
     * MailController constructor.
     *
     * @param MailService $mailService
     * @param MailHistoryRepository $mailHistoryRepository
     * @param OrderRepository $orderRepository
     * @param twig $twig
     */
    public function __construct(
        MailService $mailService,
        MailHistoryRepository $mailHistoryRepository,
        OrderRepository $orderRepository,
        Environment $twig
    ) {
        $this->mailService = $mailService;
        $this->mailHistoryRepository = $mailHistoryRepository;
        $this->orderRepository = $orderRepository;
        $this->twig = $twig;
    }

    /**
     * @Route("/%eccube_admin_route%/order/{id}/mail", requirements={"id" = "\d+"}, name="admin_order_mail", methods={"GET", "POST"})
     * @Template("@admin/Order/mail.twig")
     */
    public function index(Request $request, Order $Order)
    {
        $MailHistories = $this->mailHistoryRepository->findBy(['Order' => $Order]);

        $builder = $this->formFactory->createBuilder(OrderMailType::class);

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

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            $mode = $request->get('mode');

            $body = null;
            // テンプレート変更の場合は. バリデーション前に内容差し替え.
            switch ($mode) {
                case 'change':
                    if ($form->get('template')->isValid()) {
                        /** @var $data \Eccube\Entity\MailTemplate */
                        $MailTemplate = $form->get('template')->getData();
                        $data = $form->getData();

                        if ($MailTemplate) {
                            $twig = $MailTemplate->getFileName();
                            if (!$twig) {
                                $twig = 'Mail/order.twig';
                            }

                            // 本文確認用
                            $body = $this->createBody($Order, $twig);
                        }

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
                        if ($MailTemplate) {
                            $form->get('mail_subject')->setData($MailTemplate->getMailSubject());
                        }
                        $form->get('tpl_data')->setData($body);
                    }
                    break;
                case 'confirm':
                    if ($form->isSubmitted() && $form->isValid()) {
                        $builder->setAttribute('freeze', true);
                        $builder->setAttribute('freeze_display_text', false);
                        $form = $builder->getForm();
                        $form->handleRequest($request);

                        return $this->render('@admin/Order/mail_confirm.twig', [
                            'form' => $form->createView(),
                            'Order' => $Order,
                            'MailHistories' => $MailHistories,
                        ]);
                    }
                    break;
                case 'complete':
                    if ($form->isSubmitted() && $form->isValid()) {
                        $data = $form->getData();
                        $data['tpl_data'] = $form->get('tpl_data')->getData();

                        // メール送信
                        $message = $this->mailService->sendAdminOrderMail($Order, $data);

                        // 送信履歴を保存.
                        $MailTemplate = $form->get('template')->getData();
                        $MailHistory = new MailHistory();
                        $MailHistory
                            ->setMailSubject($message->getSubject())
                            ->setMailBody($message->getBody())
                            ->setSendDate(new \DateTime())
                            ->setOrder($Order);

                        $this->entityManager->persist($MailHistory);
                        $this->entityManager->flush();

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

                        $this->addSuccess('admin.order.mail_send_complete', 'admin');

                        return $this->redirectToRoute('admin_order_edit', ['id' => $Order->getId()]);
                    }
                    break;
                default:
                    break;
            }
        }

        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'MailHistories' => $MailHistories,
        ];
    }

    private function createBody($Order, $twig = 'Mail/order.twig')
    {
        return $this->renderView($twig, [
            'Order' => $Order,
        ]);
    }
}

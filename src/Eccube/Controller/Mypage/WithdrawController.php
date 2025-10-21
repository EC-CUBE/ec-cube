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

namespace Eccube\Controller\Mypage;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\Master\CustomerStatusRepository;
use Eccube\Repository\PageRepository;
use Eccube\Service\CartService;
use Eccube\Service\MailService;
use Eccube\Service\OrderHelper;
use Eccube\Util\StringUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class WithdrawController extends AbstractController
{
    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var CustomerStatusRepository
     */
    protected $customerStatusRepository;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var OrderHelper
     */
    private $orderHelper;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * WithdrawController constructor.
     *
     * @param MailService $mailService
     * @param CustomerStatusRepository $customerStatusRepository
     * @param TokenStorageInterface $tokenStorage
     * @param CartService $cartService
     * @param OrderHelper $orderHelper
     * @param PageRepository $pageRepository
     */
    public function __construct(
        MailService $mailService,
        CustomerStatusRepository $customerStatusRepository,
        TokenStorageInterface $tokenStorage,
        CartService $cartService,
        OrderHelper $orderHelper,
        PageRepository $pageRepository,
    ) {
        $this->mailService = $mailService;
        $this->customerStatusRepository = $customerStatusRepository;
        $this->tokenStorage = $tokenStorage;
        $this->cartService = $cartService;
        $this->orderHelper = $orderHelper;
        $this->pageRepository = $pageRepository;
    }

    /**
     * 退会画面.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/mypage/withdraw', name: 'mypage_withdraw', methods: ['GET', 'POST'])]
    #[Route(path: '/mypage/withdraw', name: 'mypage_withdraw_confirm', methods: ['GET', 'POST'])]
    #[Template(template: 'Mypage/withdraw.twig')]
    public function index(Request $request): Response|RedirectResponse|array
    {
        $builder = $this->formFactory->createBuilder();

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    log_info('退会確認画面表示');

                    return $this->render(
                        'Mypage/withdraw_confirm.twig',
                        [
                            'form' => $form->createView(),
                            'Page' => $this->pageRepository->getPageByRoute('mypage_withdraw_confirm'),
                        ]
                    );

                case 'complete':
                    log_info('退会処理開始');

                    /** @var Customer $Customer */
                    $Customer = $this->getUser();
                    $email = $Customer->getEmail();

                    // 退会ステータスに変更
                    $CustomerStatus = $this->customerStatusRepository->find(CustomerStatus::WITHDRAWING);
                    $Customer->setStatus($CustomerStatus);
                    $Customer->setEmail(StringUtil::random(60).'@dummy.dummy');

                    $this->entityManager->flush();

                    log_info('退会処理完了');

                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'Customer' => $Customer,
                        ], $request
                    );
                    $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE);

                    // メール送信
                    $this->mailService->sendCustomerWithdrawMail($Customer, $email);

                    // カートと受注のセッションを削除
                    $this->cartService->clear();
                    $this->orderHelper->removeSession();

                    // ログアウト
                    $this->tokenStorage->setToken(null);

                    log_info('ログアウト完了');

                    return $this->redirect($this->generateUrl('mypage_withdraw_complete'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 退会完了画面.
     *
     * @param Request $request
     *
     * @return array<empty>
     */
    #[Route(path: '/mypage/withdraw_complete', name: 'mypage_withdraw_complete', methods: ['GET'])]
    #[Template(template: 'Mypage/withdraw_complete.twig')]
    public function complete(Request $request): array
    {
        return [];
    }
}

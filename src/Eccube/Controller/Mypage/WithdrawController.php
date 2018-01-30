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


namespace Eccube\Controller\Mypage;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\Master\CustomerStatusRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route(service=WithdrawController::class)
 */
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
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function __construct(
        MailService $mailService,
        CustomerStatusRepository $customerStatusRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->mailService = $mailService;
        $this->customerStatusRepository = $customerStatusRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * 退会画面.
     *
     * @Route("/mypage/withdraw", name="mypage_withdraw")
     * @Template("Mypage/withdraw.twig")
     */
    public function index(Request $request)
    {
        $builder = $this->formFactory->createBuilder();

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    log_info('退会確認画面表示');

                    return $this->render(
                        'Mypage/withdraw_confirm.twig',
                        array(
                            'form' => $form->createView(),
                        )
                    );

                case 'complete':
                    log_info('退会処理開始');

                    /* @var $Customer \Eccube\Entity\Customer */
                    $Customer = $this->getUser();
                    $email = $Customer->getEmail();

                    // 退会ステータスに変更
                    $CustomerStatus = $this->customerStatusRepository->find(CustomerStatus::WITHDRAWING);
                    $Customer->setStatus($CustomerStatus);

                    $this->entityManager->flush();

                    log_info('退会処理完了');

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Customer' => $Customer,
                        ), $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE, $event);

                    // メール送信
                    $this->mailService->sendCustomerWithdrawMail($Customer, $email);

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
     * @Route("/mypage/withdraw_complete", name="mypage_withdraw_complete")
     * @Template("Mypage/withdraw_complete.twig")
     */
    public function complete(Request $request)
    {
        return [];
    }
}

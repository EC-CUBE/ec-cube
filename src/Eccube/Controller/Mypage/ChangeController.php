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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChangeController extends AbstractController
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var UserPasswordHasherInterface
     */
    protected $passwordHasher;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var baseInfoRepository
     */
    protected $baseInfoRepository;

    private const SESSION_KEY_PRE_EMAIL = 'eccube.front.mypage.change.preEmail';

    public function __construct(
        CustomerRepository $customerRepository,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage,
        BaseInfoRepository $baseInfoRepository,
        MailService $mailService
    ) {
        $this->customerRepository = $customerRepository;
        $this->passwordHasher = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->mailService = $mailService;
    }

    /**
     * 会員情報編集画面.
     *
     * @Route("/mypage/change", name="mypage_change", methods={"GET", "POST"})
     * @Template("Mypage/change.twig")
     */
    public function index(Request $request)
    {
        /** @var Customer $Customer */
        $Customer = $this->getUser();
        $Customer->setPlainPassword($this->eccubeConfig['eccube_default_password']);

        /** @var \Symfony\Component\Form\FormBuilderInterface $builder */
        $builder = $this->formFactory->createBuilder(EntryType::class, $Customer);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE);

        /** @var \Symfony\Component\Form\FormInterface $form */
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                log_info('会員編集開始');

                if ($Customer->getPlainPassword() !== $this->eccubeConfig['eccube_default_password']) {
                    $password = $this->passwordHasher->hashPassword($Customer, $Customer->getPlainPassword());
                    $Customer->setPassword($password);
                }

                // 会員情報変更時にメールを送信
                if ($this->baseInfoRepository->get()->isOptionMailNotifier()) {
                    // 情報のセット
                    $userData['userAgent'] = $request->headers->get('User-Agent');
                    $userData['preEmail'] = $request->getSession()->get(self::SESSION_KEY_PRE_EMAIL);
                    $userData['ipAddress'] = $request->getClientIp();

                    // メール送信
                    $this->mailService->sendCustomerChangeNotifyMail($Customer, $userData, trans('front.mypage.customer.notify_title'));
                }

                $this->session->remove(self::SESSION_KEY_PRE_EMAIL);

                $this->entityManager->flush();

                log_info('会員編集完了');

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'Customer' => $Customer,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE);

                return $this->redirect($this->generateUrl('mypage_change_complete'));
            }
            // see https://github.com/EC-CUBE/ec-cube/issues/6103
            $this->entityManager->refresh($Customer);
        }

        $preEmail = $form->get('email')->getData();
        $this->session->set(self::SESSION_KEY_PRE_EMAIL, $preEmail);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 会員情報編集完了画面.
     *
     * @Route("/mypage/change_complete", name="mypage_change_complete", methods={"GET"})
     *
     * @Template("Mypage/change_complete.twig")
     */
    public function complete(Request $request)
    {
        return [];
    }
}
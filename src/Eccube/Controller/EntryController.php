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

namespace Eccube\Controller;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CustomerStatusRepository;
use Eccube\Repository\PageRepository;
use Eccube\Service\CartService;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntryController extends AbstractController
{
    /**
     * @var CustomerStatusRepository
     */
    protected $customerStatusRepository;

    /**
     * @var ValidatorInterface
     */
    protected $recursiveValidator;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var \Eccube\Service\CartService
     */
    protected $cartService;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * EntryController constructor.
     *
     * @param CartService $cartService
     * @param CustomerStatusRepository $customerStatusRepository
     * @param MailService $mailService
     * @param BaseInfoRepository $baseInfoRepository
     * @param CustomerRepository $customerRepository
     * @param EncoderFactoryInterface $encoderFactory
     * @param ValidatorInterface $validatorInterface
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        CartService $cartService,
        CustomerStatusRepository $customerStatusRepository,
        MailService $mailService,
        BaseInfoRepository $baseInfoRepository,
        CustomerRepository $customerRepository,
        EncoderFactoryInterface $encoderFactory,
        ValidatorInterface $validatorInterface,
        TokenStorageInterface $tokenStorage,
        PageRepository $pageRepository
    ) {
        $this->customerStatusRepository = $customerStatusRepository;
        $this->mailService = $mailService;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->customerRepository = $customerRepository;
        $this->encoderFactory = $encoderFactory;
        $this->recursiveValidator = $validatorInterface;
        $this->tokenStorage = $tokenStorage;
        $this->cartService = $cartService;
        $this->pageRepository = $pageRepository;
    }

    /**
     * 会員登録画面.
     *
     * @Route("/entry", name="entry", methods={"GET", "POST"})
     * @Route("/entry", name="entry_confirm", methods={"GET", "POST"})
     * @Template("Entry/index.twig")
     */
    public function index(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            log_info('認証済のためログイン処理をスキップ');

            return $this->redirectToRoute('mypage');
        }

        /** @var $Customer \Eccube\Entity\Customer */
        $Customer = $this->customerRepository->newCustomer();

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createBuilder(EntryType::class, $Customer);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_ENTRY_INDEX_INITIALIZE);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    log_info('会員登録確認開始');
                    log_info('会員登録確認完了');

                    return $this->render(
                        'Entry/confirm.twig',
                        [
                            'form' => $form->createView(),
                            'Page' => $this->pageRepository->getPageByRoute('entry_confirm'),
                        ]
                    );

                case 'complete':
                    log_info('会員登録開始');

                    $existCustomer = $this->customerRepository->findOneBy([
                        'email' => $Customer->getEmail(),
                        'Status' => [
                            CustomerStatus::PROVISIONAL,
                            CustomerStatus::REGULAR,
                        ]
                    ]);

                    if ($existCustomer) {
                        log_info('会員登録済のため登録処理をスキップ');
                    } else {
                        log_info('会員登録を実行');

                        $encoder = $this->encoderFactory->getEncoder($Customer);
                        $salt = $encoder->createSalt();
                        $password = $encoder->encodePassword($Customer->getPlainPassword(), $salt);
                        $secretKey = $this->customerRepository->getUniqueSecretKey();

                        $Customer
                            ->setSalt($salt)
                            ->setPassword($password)
                            ->setSecretKey($secretKey)
                            ->setPoint(0);

                        $this->entityManager->persist($Customer);
                        $this->entityManager->flush();

                        log_info('会員登録完了');

                        $event = new EventArgs(
                            [
                                'form' => $form,
                                'Customer' => $Customer,
                            ],
                            $request
                        );
                    }

                    $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_ENTRY_INDEX_COMPLETE);

                    // 会員登録済の場合は既存のsecret_keyを利用
                    $secretKey = $existCustomer ? $existCustomer->getSecretKey() : $Customer->getSecretKey();

                    // 仮会員設定が有効な場合は、確認メールを送信し完了画面表示.
                    if ($this->BaseInfo->isOptionCustomerActivate()) {
                        log_info('仮会員設定が有効');

                        $activateUrl = $this->generateUrl('entry_activate', ['secret_key' => $secretKey], UrlGeneratorInterface::ABSOLUTE_URL);

                        // メール送信
                        $this->mailService->sendCustomerConfirmMail($Customer, $activateUrl, $existCustomer);

                        if ($event->hasResponse()) {
                            return $event->getResponse();
                        }

                        log_info('仮会員登録完了画面へリダイレクト');

                        return $this->redirectToRoute('entry_complete');
                    } else {
                        log_info('仮会員設定が無効');

                        if ($existCustomer) {
                            // 会員登録済の場合はメール通知のみ
                            $this->mailService->sendCustomerCompleteMail($Customer, $existCustomer);

                            log_info('会員登録完了画面へリダイレクト');

                            return $this->redirectToRoute('entry_activate_complete', [
                                'qtyInCart' => $this->getQuantityInCart(),
                            ]);
                        } else {
                            // 本会員として更新
                            $this->updateRegularCustomer($Customer);
                            // ログイン済へ変更
                            $this->doLogin($Customer, $request);
                            // メール通知
                            $this->mailService->sendCustomerCompleteMail($Customer);

                            log_info('会員登録完了画面へリダイレクト');

                            return $this->redirectToRoute('entry_activate_complete', [
                                'qtyInCart' => $this->getQuantityInCart(),
                            ]);
                        }
                    }
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 会員登録完了画面(仮会員).
     *
     * @Route("/entry/complete", name="entry_complete", methods={"GET"})
     * @Template("Entry/complete.twig")
     */
    public function complete()
    {
        return [];
    }

    /**
     * 会員のアクティベート（本会員化）を行う.
     *
     * @Route("/entry/activate/{secret_key}", name="entry_activate", methods={"GET"})
     */
    public function activate(Request $request, $secret_key)
    {
        $errors = $this->recursiveValidator->validate(
            $secret_key,
            [
                new Assert\NotBlank(),
                new Assert\Regex(
                    [
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                    ]
                ),
            ]
        );

        if (count($errors) === 0) {
            $Customer = $this->customerRepository->getProvisionalCustomerBySecretKey($secret_key);
            if (null === $Customer) {
                throw new HttpException\NotFoundHttpException();
            }

            // 本会員として更新
            $this->updateRegularCustomer($Customer);
            // ログイン済へ変更
            $this->doLogin($Customer, $request);
            // メール通知
            $this->mailService->sendCustomerCompleteMail($Customer);

            return $this->redirectToRoute('entry_activate_complete', [
                'qtyInCart' => $this->getQuantityInCart(),
            ]);
        }

        throw new HttpException\NotFoundHttpException();
    }

    /**
     * 会員登録完了画面(本会員).
     *
     * @Route("/entry/activate_complete", name="entry_activate_complete", methods={"GET"})
     * @Template("Entry/activate.twig")
     */
    public function activate_complete(Request $request)
    {
        return ['qtyInCart' => $request->query->get('qtyInCart')];
    }

    /**
     * カート内の登録数を取得する.
     *
     * @return int
     */
    private function getQuantityInCart(): int
    {
        $Carts = $this->cartService->getCarts();
        $qtyInCart = 0;
        foreach ($Carts as $Cart) {
            $qtyInCart += $Cart->getTotalQuantity();
        }

        if ($qtyInCart) {
            $this->cartService->save();
        }

        return $qtyInCart;
    }

    /**
     * ログイン状態に更新する.
     *
     * @param Customer $Customer
     * @param Request $request
     * @return void
     */
    private function doLogin(Customer $Customer, Request $request): void
    {
        $token = new UsernamePasswordToken($Customer, null, 'customer', ['ROLE_USER']);
        $this->tokenStorage->setToken($token);
        $request->getSession()->migrate(true);
    }

    /**
     * 本会員へ更新する.
     *
     * @param Customer $Customer
     * @return void
     */
    private function updateRegularCustomer(Customer $Customer): void
    {
        $CustomerStatus = $this->customerStatusRepository->find(CustomerStatus::REGULAR);
        $Customer->setStatus($CustomerStatus);
        $this->entityManager->persist($Customer);
        $this->entityManager->flush();
    }
}

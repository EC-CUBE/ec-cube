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

namespace Eccube\Controller;

use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\ForgotType;
use Eccube\Form\Type\Front\PasswordResetType;
use Eccube\Repository\CustomerRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ForgotController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * ForgotController constructor.
     *
     * @param ValidatorInterface $validator
     * @param MailService $mailService
     * @param CustomerRepository $customerRepository
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        ValidatorInterface $validator,
        MailService $mailService,
        CustomerRepository $customerRepository,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->validator = $validator;
        $this->mailService = $mailService;
        $this->customerRepository = $customerRepository;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * パスワードリマインダ.
     *
     * @Route("/forgot", name="forgot")
     * @Template("Forgot/index.twig")
     */
    public function index(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            throw new HttpException\NotFoundHttpException();
        }

        $builder = $this->formFactory
            ->createNamedBuilder('', ForgotType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_FORGOT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Customer = $this->customerRepository
                ->getRegularCustomerByEmail($form->get('login_email')->getData());

            if (!is_null($Customer)) {
                // リセットキーの発行・有効期限の設定
                $Customer
                    ->setResetKey($this->customerRepository->getUniqueResetKey())
                    ->setResetExpire(new \DateTime('+'.$this->eccubeConfig['eccube_customer_reset_expire'].' min'));

                // リセットキーを更新
                $this->entityManager->persist($Customer);
                $this->entityManager->flush();

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'Customer' => $Customer,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::FRONT_FORGOT_INDEX_COMPLETE, $event);

                // 完了URLの生成
                $reset_url = $this->generateUrl('forgot_reset', ['reset_key' => $Customer->getResetKey()], UrlGeneratorInterface::ABSOLUTE_URL);

                // メール送信
                $this->mailService->sendPasswordResetNotificationMail($Customer, $reset_url);

                // ログ出力
                log_info('send reset password mail to:'."{$Customer->getId()} {$Customer->getEmail()} {$request->getClientIp()}");
            } else {
                log_warning(
                    'Un active customer try send reset password email: ',
                    ['Enter email' => $form->get('login_email')->getData()]
                );
            }

            return $this->redirectToRoute('forgot_complete');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 再設定URL送信完了画面.
     *
     * @Route("/forgot/complete", name="forgot_complete")
     * @Template("Forgot/complete.twig")
     */
    public function complete(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            throw new HttpException\NotFoundHttpException();
        }

        return [];
    }

    /**
     * パスワード再発行実行画面.
     *
     * @Route("/forgot/reset/{reset_key}", name="forgot_reset")
     * @Template("Forgot/reset.twig")
     */
    public function reset(Request $request, $reset_key)
    {
        if ($this->isGranted('ROLE_USER')) {
            throw new HttpException\NotFoundHttpException();
        }

        $errors = $this->validator->validate(
            $reset_key,
            [
                new Assert\NotBlank(),
                new Assert\Regex(
                    [
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                    ]
                ),
            ]
        );

        if (count($errors) > 0) {
            // リセットキーに異常がある場合
            throw new HttpException\NotFoundHttpException();
        }

        $Customer = $this->customerRepository
            ->getRegularCustomerByResetKey($reset_key);

        if (null === $Customer) {
            // リセットキーから会員データが取得できない場合
            throw new HttpException\NotFoundHttpException();
        }

        $builder = $this->formFactory
            ->createNamedBuilder('', PasswordResetType::class);

        $form = $builder->getForm();
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            // リセットキー・入力メールアドレスで会員情報検索
            $Customer = $this->customerRepository
                ->getRegularCustomerByResetKey($reset_key, $form->get('login_email')->getData());
            if ($Customer) {
                // パスワードの発行・更新
                $encoder = $this->encoderFactory->getEncoder($Customer);
                $pass = $form->get('password')->getData();
                $Customer->setPassword($pass);

                // 発行したパスワードの暗号化
                if ($Customer->getSalt() === null) {
                    $Customer->setSalt($this->encoderFactory->getEncoder($Customer)->createSalt());
                }
                $encPass = $encoder->encodePassword($pass, $Customer->getSalt());

                // パスワードを更新
                $Customer->setPassword($encPass);
                // リセットキーをクリア
                $Customer->setResetKey(null);

                // パスワードを更新
                $this->entityManager->persist($Customer);
                $this->entityManager->flush();

                $event = new EventArgs(
                    [
                        'Customer' => $Customer,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::FRONT_FORGOT_RESET_COMPLETE, $event);

                // 完了メッセージを設定
                $this->addFlash('password_reset_complete', trans('front.forgot.reset_complete'));

                // ログインページへリダイレクト
                return $this->redirectToRoute('mypage_login');
            } else {
                // リセットキー・メールアドレスから会員データが取得できない場合
                $error = trans('front.forgot.reset_not_found');
            }
        }

        return [
            'error' => $error,
            'form' => $form->createView(),
        ];
    }
}

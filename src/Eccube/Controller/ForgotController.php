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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\ForgotType;
use Eccube\Repository\CustomerRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * @Component
 * @Route("/forgot", service=ForgotController::class)
 */
class ForgotController extends AbstractController
{
    /**
     * @Inject("validator")
     * @var RecursiveValidator
     */
    protected $recursiveValidator;

    /**
     * @Inject("monolog")
     * @var Logger
     */
    protected $logger;

    /**
     * @Inject(MailService::class)
     * @var MailService
     */
    protected $mailService;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(CustomerRepository::class)
     * @var CustomerRepository
     */
    protected $customerRepository;

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
     * パスワードリマインダ.
     *
     * @Route("", name="forgot")
     * @Template("Forgot/index.twig")
     */
    public function index(Application $app, Request $request)
    {
        $builder = $this->formFactory
            ->createNamedBuilder('', ForgotType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_FORGOT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Customer = $this->customerRepository
                ->getActiveCustomerByEmail($form->get('login_email')->getData());

            if (!is_null($Customer)) {
                // リセットキーの発行・有効期限の設定
                $Customer
                    ->setResetKey($this->customerRepository->getUniqueResetKey($app))
                    ->setResetExpire(new \DateTime('+'.$this->appConfig['customer_reset_expire'].' min'));

                // リセットキーを更新
                $this->entityManager->persist($Customer);
                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Customer' => $Customer,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::FRONT_FORGOT_INDEX_COMPLETE, $event);

                // 完了URLの生成
                $reset_url = $app->url('forgot_reset', array('reset_key' => $Customer->getResetKey()));

                // メール送信
                $this->mailService->sendPasswordResetNotificationMail($Customer, $reset_url);

                // ログ出力
                $this->logger->addInfo(
                    'send reset password mail to:'."{$Customer->getId()} {$Customer->getEmail()} {$request->getClientIp()}"
                );
            } else {
                log_warning(
                    'Un active customer try send reset password email: ',
                    array('Enter email' => $form->get('login_email')->getData())
                );
            }

            return $app->redirect($app->url('forgot_complete'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * パスワードリマインダ完了画面.
     *
     * @Route("/complete", name="forgot_complete")
     * @Template("Forgot/complete.twig")
     */
    public function complete(Application $app, Request $request)
    {
        return [];
    }

    /**
     * パスワード再発行実行画面.
     *
     * @Route("/reset/{reset_key}", name="forgot_reset")
     * @Template("Forgot/reset.twig")
     */
    public function reset(Application $app, Request $request, $reset_key)
    {
        $errors = $this->recursiveValidator->validate(
            $reset_key,
            array(
                new Assert\NotBlank(),
                new Assert\Regex(
                    array(
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                    )
                ),
            )
        );

        if ('GET' === $request->getMethod()
            && count($errors) === 0
        ) {
            try {
                $Customer = $this->customerRepository
                    ->getActiveCustomerByResetKey($reset_key);
            } catch (\Exception $e) {
                throw new HttpException\NotFoundHttpException('有効期限が切れているか、無効なURLです。');
            }

            // パスワードの発行・更新
            $pass = $this->customerRepository->getResetPassword();
            $Customer->setPassword($pass);

            // 発行したパスワードの暗号化
            if ($Customer->getSalt() === null) {
                $Customer->setSalt($this->customerRepository->createSalt(5));
            }
            $encPass = $this->customerRepository->encryptPassword($app, $Customer);
            $Customer->setPassword($encPass);

            $Customer->setResetKey(null);

            // パスワードを更新
            $this->entityManager->persist($Customer);
            $this->entityManager->flush();

            $event = new EventArgs(
                array(
                    'Customer' => $Customer,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_FORGOT_RESET_COMPLETE, $event);

            // メール送信
            $this->mailService->sendPasswordResetCompleteMail($Customer, $pass);

            // ログ出力
            $this->logger->addInfo(
                'reset password complete:'."{$Customer->getId()} {$Customer->getEmail()} {$request->getClientIp()}"
            );
        } else {
            throw new HttpException\AccessDeniedHttpException('不正なアクセスです。');
        }

        return [];
    }

}

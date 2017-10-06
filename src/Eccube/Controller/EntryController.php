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
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CustomerStatusRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * @Route(service=EntryController::class)
 */
class EntryController extends AbstractController
{
    /**
     * @Inject(CustomerStatusRepository::class)
     * @var CustomerStatusRepository
     */
    protected $customerStatusRepository;

    /**
     * @Inject("validator")
     * @var RecursiveValidator
     */
    protected $recursiveValidator;

    /**
     * @Inject(MailService::class)
     * @var MailService
     */
    protected $mailService;

    /**
     * @Inject(BaseInfo::class)
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

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
     * @Inject(CustomerRepository::class)
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @Inject("security.encoder_factory")
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * 会員登録画面.
     *
     * @Route("/entry", name="entry")
     * @Template("Entry/index.twig")
     */
    public function index(Application $app, Request $request)
    {
        if ($app->isGranted('ROLE_USER')) {
            log_info('認証済のためログイン処理をスキップ');

            return $app->redirect($app->url('mypage'));
        }

        /** @var $Customer \Eccube\Entity\Customer */
        $Customer = $this->customerRepository->newCustomer();

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createBuilder(EntryType::class, $Customer);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_ENTRY_INDEX_INITIALIZE, $event);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    log_info('会員登録確認開始');
                    log_info('会員登録確認完了');

                    return $app->render(
                        'Entry/confirm.twig',
                        array(
                            'form' => $form->createView(),
                        )
                    );

                case 'complete':
                    log_info('会員登録開始');

                    $encoder = $this->encoderFactory->getEncoder($Customer);
                    $salt = $encoder->createSalt();
                    $password = $encoder->encodePassword($Customer->getPassword(), $salt);
                    $secretKey = $this->customerRepository->getUniqueSecretKey();

                    $Customer
                        ->setSalt($salt)
                        ->setPassword($password)
                        ->setSecretKey($secretKey);

                    $CustomerAddress = new CustomerAddress();
                    $CustomerAddress
                        ->setFromCustomer($Customer);

                    $this->entityManager->persist($Customer);
                    $this->entityManager->persist($CustomerAddress);
                    $this->entityManager->flush();

                    log_info('会員登録完了');

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Customer' => $Customer,
                            'CustomerAddress' => $CustomerAddress,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::FRONT_ENTRY_INDEX_COMPLETE, $event);

                    $activateUrl = $app->url('entry_activate', array('secret_key' => $Customer->getSecretKey()));

                    $activateFlg = $this->BaseInfo->getOptionCustomerActivate();

                    // 仮会員設定が有効な場合は、確認メールを送信し完了画面表示.
                    if ($activateFlg) {
                        // メール送信
                        $this->mailService->sendCustomerConfirmMail($Customer, $activateUrl);

                        if ($event->hasResponse()) {
                            return $event->getResponse();
                        }

                        log_info('仮会員登録完了画面へリダイレクト');

                        return $app->redirect($app->url('entry_complete'));
                        // 仮会員設定が無効な場合は認証URLへ遷移させ、会員登録を完了させる.
                    } else {
                        log_info('本会員登録画面へリダイレクト');

                        return $app->redirect($activateUrl);
                    }
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 会員登録完了画面.
     *
     * @Route("/entry/complete", name="entry_complete")
     * @Template("Entry/complete.twig")
     */
    public function complete(Application $app, Request $request)
    {
        return [];
    }

    /**
     * 会員のアクティベート（本会員化）を行う.
     *
     * @Route("/entry/activate/{secret_key}", name="entry_activate")
     * @Template("Entry/activate.twig")
     */
    public function activate(Application $app, Request $request, $secret_key)
    {
        $errors = $this->recursiveValidator->validate(
            $secret_key,
            array(
                new Assert\NotBlank(),
                new Assert\Regex(
                    array(
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                    )
                ),
            )
        );

        if ($request->getMethod() === 'GET' && count($errors) === 0) {
            log_info('本会員登録開始');
            $Customer = $this->customerRepository->getProvisionalCustomerBySecretKey($secret_key);
            if (is_null($Customer)) {
                throw new HttpException\NotFoundHttpException('※ 既に会員登録が完了しているか、無効なURLです。');
            }

            $CustomerStatus = $this->customerStatusRepository->find(CustomerStatus::REGULAR);
            $Customer->setStatus($CustomerStatus);
            $this->entityManager->persist($Customer);
            $this->entityManager->flush();

            log_info('本会員登録完了');

            $event = new EventArgs(
                array(
                    'Customer' => $Customer,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_ENTRY_ACTIVATE_COMPLETE, $event);

            // メール送信
            $this->mailService->sendCustomerCompleteMail($Customer);

            // 本会員登録してログイン状態にする
            $token = new UsernamePasswordToken($Customer, null, 'customer', array('ROLE_USER'));
            $this->getSecurity($app)->setToken($token);

            log_info('ログイン済に変更', array($app->user()->getId()));

            return [];
        } else {
            throw new HttpException\AccessDeniedHttpException('不正なアクセスです。');
        }
    }
}

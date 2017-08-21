<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Order;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Form\Type\Front\NonMemberType;
use Eccube\Form\Type\Front\ShoppingShippingType;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Service\CartService;
use Eccube\Service\OrderHelper;
use Eccube\Service\ShoppingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * @Component
 * @Route(service=NonMemberShoppingController::class)
 */
class NonMemberShoppingController extends AbstractShoppingController
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
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject(PrefRepository::class)
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @Inject("session")
     * @var Session
     */
    protected $session;

    /**
     * @Inject(OrderHelper::class)
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(ShoppingService::class)
     * @var ShoppingService
     */
    protected $shoppingService;

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
     * @Inject(CartService::class)
     * @var CartService
     */
    protected $cartService;


    /**
     * 非会員処理
     *
     * @Route("/shopping/nonmember", name="shopping_nonmember")
     * @Template("Shopping/nonmember.twig")
     */
    public function index(Application $app, Request $request)
    {
        $cartService = $this->cartService;

        // カートチェック
        $response = $app->forward($app->path("shopping_check_to_cart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // ログイン済みの場合は, 購入画面へリダイレクト.
        if ($app->isGranted('ROLE_USER')) {
            return $app->redirect($app->url('shopping'));
        }

        $builder = $this->formFactory->createBuilder(NonMemberType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            log_info('非会員お客様情報登録開始');

            $data = $form->getData();
            $Customer = new Customer();
            $Customer
                ->setName01($data['name01'])
                ->setName02($data['name02'])
                ->setKana01($data['kana01'])
                ->setKana02($data['kana02'])
                ->setCompanyName($data['company_name'])
                ->setEmail($data['email'])
                ->setTel01($data['tel01'])
                ->setTel02($data['tel02'])
                ->setTel03($data['tel03'])
                ->setZip01($data['zip01'])
                ->setZip02($data['zip02'])
                ->setZipCode($data['zip01'].$data['zip02'])
                ->setPref($data['pref'])
                ->setAddr01($data['addr01'])
                ->setAddr02($data['addr02']);

            // 非会員複数配送用
            $CustomerAddress = new CustomerAddress();
            $CustomerAddress
                ->setCustomer($Customer)
                ->setName01($data['name01'])
                ->setName02($data['name02'])
                ->setKana01($data['kana01'])
                ->setKana02($data['kana02'])
                ->setCompanyName($data['company_name'])
                ->setTel01($data['tel01'])
                ->setTel02($data['tel02'])
                ->setTel03($data['tel03'])
                ->setZip01($data['zip01'])
                ->setZip02($data['zip02'])
                ->setZipCode($data['zip01'].$data['zip02'])
                ->setPref($data['pref'])
                ->setAddr01($data['addr01'])
                ->setAddr02($data['addr02'])
                ->setDelFlg(Constant::DISABLED);
            $Customer->addCustomerAddress($CustomerAddress);

            // 受注情報を取得
            /** @var Order $Order */
            $Order = $this->shoppingService->getOrder($this->appConfig['order_processing']);

            // 初回アクセス(受注データがない)の場合は, 受注情報を作成
            if (is_null($Order)) {
                // 受注情報を作成
                try {
                    // 受注情報を作成
//                    $Order = $app['eccube.service.shopping']->createOrder($Customer);
                    $Order = $this->orderHelper->createProcessingOrder(
                        $Customer,
                        $Customer->getCustomerAddresses()->current(),
                        $cartService->getCart()->getCartItems()
                    );
                    $cartService->setPreOrderId($Order->getPreOrderId());
                    $cartService->save();
                } catch (CartException $e) {
                    $app->addRequestError($e->getMessage());

                    return $app->redirect($app->url('cart'));
                }
            }

            $flowResult = $this->executePurchaseFlow($app, $Order);
            if ($flowResult->hasWarning() || $flowResult->hasError()) {
                return $app->redirect($app->url('cart'));
            }

            // 非会員用セッションを作成
            $nonMember = array();
            $nonMember['customer'] = $Customer;
            $nonMember['pref'] = $Customer->getPref()->getId();
            $this->session->set($this->sessionKey, $nonMember);

            $customerAddresses = array();
            $customerAddresses[] = $CustomerAddress;
            $this->session->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_COMPLETE, $event);

            if ($event->getResponse() !== null) {
                return $event->getResponse();
            }

            log_info('非会員お客様情報登録完了', array($Order->getId()));

            return $app->redirect($app->url('shopping'));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 非会員用複数配送設定時の新規お届け先の設定
     *
     */
    public function shippingMultipleEdit(Application $app, Request $request)
    {
        // カートチェック
        $response = $app->forward($app->path("shopping_check_to_art"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 非会員用Customerを取得
        $Customer = $this->shoppingService->getNonMember($this->sessionKey);
        $CustomerAddress = new CustomerAddress();
        $CustomerAddress->setCustomer($Customer);
        $Customer->addCustomerAddress($CustomerAddress);

        $builder = $this->formFactory->createBuilder(ShoppingShippingType::class, $CustomerAddress);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            log_info('非会員お届け先追加処理開始');

            // 非会員用のセッションに追加
            $customerAddresses = $this->session->get($this->sessionCustomerAddressKey);
            $customerAddresses = unserialize($customerAddresses);
            $customerAddresses[] = $CustomerAddress;
            $this->session->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'CustomerAddresses' => $customerAddresses,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_COMPLETE, $event);

            log_info('非会員お届け先追加処理完了');

            return $app->redirect($app->url('shopping_shipping_multiple'));
        }

        return $app->render(
            'Shopping/shipping_multiple_edit.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * お届け先の設定（非会員）がクリックされた場合の処理
     *
     * @Route("/shopping/shipping_edit_change/{id}", name="shopping_shipping_edit_change", requirements={"id":"\d+"})
     */
    public function shippingEditChange(Application $app, Request $request, $id)
    {
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        if ('POST' !== $request->getMethod()) {
            return $app->redirect($app->url('shopping'));
        }

        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_CHANGE_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $data['message'];
            $Order->setMessage($message);
            // 受注情報を更新
            $app['orm.em']->flush();

            // お届け先設定一覧へリダイレクト
            return $app->redirect($app->url('shopping_shipping_edit', array('id' => $id)));
        }

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
    }

    /**
     * お客様情報の変更(非会員)
     *
     * @Route("/shopping/customer", name="shopping_customer")
     */
    public function customer(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {

                log_info('非会員お客様情報変更処理開始');

                $data = $request->request->all();

                // 入力チェック
                $errors = $this->customerValidation($app, $data);

                foreach ($errors as $error) {
                    if ($error->count() != 0) {
                        log_info('非会員お客様情報変更入力チェックエラー');
                        $response = new Response(json_encode('NG'), 400);
                        $response->headers->set('Content-Type', 'application/json');

                        return $response;
                    }
                }

                $pref = $this->prefRepository->findOneBy(array('name' => $data['customer_pref']));
                if (!$pref) {
                    log_info('非会員お客様情報変更入力チェックエラー');
                    $response = new Response(json_encode('NG'), 400);
                    $response->headers->set('Content-Type', 'application/json');

                    return $response;
                }

                $Order = $this->shoppingService->getOrder($this->appConfig['order_processing']);
                if (!$Order) {
                    log_info('カートが存在しません');
                    $app->addError('front.shopping.order.error');

                    return $app->redirect($app->url('shopping_error'));
                }

                $Order
                    ->setName01($data['customer_name01'])
                    ->setName02($data['customer_name02'])
                    ->setCompanyName($data['customer_company_name'])
                    ->setTel01($data['customer_tel01'])
                    ->setTel02($data['customer_tel02'])
                    ->setTel03($data['customer_tel03'])
                    ->setZip01($data['customer_zip01'])
                    ->setZip02($data['customer_zip02'])
                    ->setZipCode($data['customer_zip01'].$data['customer_zip02'])
                    ->setPref($pref)
                    ->setAddr01($data['customer_addr01'])
                    ->setAddr02($data['customer_addr02'])
                    ->setEmail($data['customer_email']);

                // 配送先を更新
                $this->entityManager->flush();

                // 受注関連情報を最新状態に更新
                $this->entityManager->refresh($Order);

                $event = new EventArgs(
                    array(
                        'Order' => $Order,
                        'data' => $data,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_CUSTOMER_INITIALIZE, $event);

                log_info('非会員お客様情報変更処理完了', array($Order->getId()));
                $response = new Response(json_encode('OK'));
                $response->headers->set('Content-Type', 'application/json');
            } catch (\Exception $e) {
                log_error('予期しないエラー', array($e->getMessage()));
                $this->logger->error($e);

                $response = new Response(json_encode('NG'), 500);
                $response->headers->set('Content-Type', 'application/json');
            }

            return $response;
        }
    }

    /**
     * 非会員でのお客様情報変更時の入力チェック
     *
     * @param Application $app
     * @param array $data リクエストパラメータ
     * @return array
     */
    protected function customerValidation(Application $app, array $data)
    {
        // 入力チェック
        $errors = array();

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_name01'],
            array(
                new Assert\NotBlank(),
                new Assert\Length(array('max' => $this->appConfig['name_len'],)),
                new Assert\Regex(
                    array('pattern' => '/^[^\s ]+$/u', 'message' => 'form.type.name.firstname.nothasspace')
                ),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_name02'],
            array(
                new Assert\NotBlank(),
                new Assert\Length(array('max' => $this->appConfig['name_len'],)),
                new Assert\Regex(
                    array('pattern' => '/^[^\s ]+$/u', 'message' => 'form.type.name.firstname.nothasspace')
                ),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_company_name'],
            array(
                new Assert\Length(array('max' => $this->appConfig['stext_len'])),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_tel01'],
            array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                new Assert\Length(
                    array('max' => $this->appConfig['tel_len'], 'min' => $this->appConfig['tel_len_min'])
                ),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_tel02'],
            array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                new Assert\Length(
                    array('max' => $this->appConfig['tel_len'], 'min' => $this->appConfig['tel_len_min'])
                ),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_tel03'],
            array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                new Assert\Length(
                    array('max' => $this->appConfig['tel_len'], 'min' => $this->appConfig['tel_len_min'])
                ),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_zip01'],
            array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                new Assert\Length(
                    array('min' => $this->appConfig['zip01_len'], 'max' => $this->appConfig['zip01_len'])
                ),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_zip02'],
            array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                new Assert\Length(
                    array('min' => $this->appConfig['zip02_len'], 'max' => $this->appConfig['zip02_len'])
                ),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_addr01'],
            array(
                new Assert\NotBlank(),
                new Assert\Length(array('max' => $this->appConfig['address1_len'])),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_addr02'],
            array(
                new Assert\NotBlank(),
                new Assert\Length(array('max' => $this->appConfig['address2_len'])),
            )
        );

        $errors[] = $this->recursiveValidator->validate(
            $data['customer_email'],
            array(
                new Assert\NotBlank(),
                new Assert\Email(array('strict' => true)),
            )
        );

        return $errors;
    }
}
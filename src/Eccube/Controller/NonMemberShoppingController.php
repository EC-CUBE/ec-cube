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

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Form\Type\Front\NonMemberType;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Service\CartService;
use Eccube\Service\OrderHelper;
use Eccube\Service\ShoppingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NonMemberShoppingController extends AbstractShoppingController
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * @var ShoppingService
     */
    protected $shoppingService;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * NonMemberShoppingController constructor.
     *
     * @param ValidatorInterface $validator
     * @param PrefRepository $prefRepository
     * @param OrderHelper $orderHelper
     * @param ShoppingService $shoppingService
     * @param CartService $cartService
     */
    public function __construct(
        ValidatorInterface $validator,
        PrefRepository $prefRepository,
        OrderHelper $orderHelper,
        ShoppingService $shoppingService,
        CartService $cartService
    ) {
        $this->validator = $validator;
        $this->prefRepository = $prefRepository;
        $this->orderHelper = $orderHelper;
        $this->shoppingService = $shoppingService;
        $this->cartService = $cartService;
    }

    /**
     * 非会員処理
     *
     * @Route("/shopping/nonmember", name="shopping_nonmember")
     * @Template("Shopping/nonmember.twig")
     */
    public function index(Request $request)
    {
        $cartService = $this->cartService;

        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // ログイン済みの場合は, 購入画面へリダイレクト.
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('shopping');
        }

        $builder = $this->formFactory->createBuilder(NonMemberType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
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
                ->setAddr02($data['addr02']);
            $Customer->addCustomerAddress($CustomerAddress);

            // 受注情報を取得
            /** @var Order $Order */
            $Order = $this->shoppingService->getOrder(OrderStatus::PROCESSING);

            // 初回アクセス(受注データがない)の場合は, 受注情報を作成
            if (is_null($Order)) {
                // 受注情報を作成
                try {
                    // 受注情報を作成
                    $Order = $this->orderHelper->createProcessingOrder(
                        $Customer,
                        $Customer->getCustomerAddresses()->current(),
                        $cartService->getCart()->getCartItems()
                    );
                    $cartService->setPreOrderId($Order->getPreOrderId());
                    $cartService->save();
                } catch (CartException $e) {
                    $this->addRequestError($e->getMessage());

                    return $this->redirectToRoute('cart');
                }
            }

            $flowResult = $this->executePurchaseFlow($Order);
            if ($flowResult->hasWarning() || $flowResult->hasError()) {
                return $this->redirectToRoute('cart');
            }

            // 非会員用セッションを作成
            $nonMember = [];
            $nonMember['customer'] = $Customer;
            $nonMember['pref'] = $Customer->getPref()->getId();
            $this->session->set($this->sessionKey, $nonMember);

            $customerAddresses = [];
            $customerAddresses[] = $CustomerAddress;
            $this->session->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Order' => $Order,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_COMPLETE, $event);

            if ($event->getResponse() !== null) {
                return $event->getResponse();
            }

            log_info('非会員お客様情報登録完了', [$Order->getId()]);

            return $this->redirectToRoute('shopping');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * お届け先の設定（非会員）がクリックされた場合の処理
     *
     * @Route("/shopping/shipping_edit_change/{id}", name="shopping_shipping_edit_change", requirements={"id" = "\d+"})
     */
    public function shippingEditChange(Request $request, $id)
    {
        $Order = $this->shoppingService->getOrder(OrderStatus::PROCESSING);
        if (!$Order) {
            $this->addError('front.shopping.order.error');

            return $this->redirectToRoute('shopping_error');
        }

        if ('POST' !== $request->getMethod()) {
            return $this->redirectToRoute('shopping');
        }

        $builder = $this->shoppingService->getShippingFormBuilder($Order);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Order' => $Order,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_CHANGE_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $data['message'];
            $Order->setMessage($message);
            // 受注情報を更新
            $this->entityManager->flush();

            // お届け先設定一覧へリダイレクト
            return $this->redirectToRoute('shopping_shipping_edit', ['id' => $id]);
        }

        return $this->redirectToRoute('Shopping/index.twig', [
            'form' => $form->createView(),
            'Order' => $Order,
        ]);
    }

    /**
     * お客様情報の変更(非会員)
     *
     * @Route("/shopping/customer", name="shopping_customer")
     */
    public function customer(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            $response = new Response(json_encode(['status' => 'NG']), 400);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        try {
            log_info('非会員お客様情報変更処理開始');
            $data = $request->request->all();
            // 入力チェック
            $errors = $this->customerValidation($data);
            foreach ($errors as $error) {
                if ($error->count() != 0) {
                    log_info('非会員お客様情報変更入力チェックエラー');
                    $response = new Response(json_encode('NG'), 400);
                    $response->headers->set('Content-Type', 'application/json');

                    return $response;
                }
            }
            $pref = $this->prefRepository->findOneBy(['name' => $data['customer_pref']]);
            if (!$pref) {
                log_info('非会員お客様情報変更入力チェックエラー');
                $response = new Response(json_encode('NG'), 400);
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }
            $Order = $this->shoppingService->getOrder(OrderStatus::PROCESSING);
            if (!$Order) {
                log_info('カートが存在しません');
                $this->addError('front.shopping.order.error');

                return $this->redirectToRoute('shopping_error');
            }
            $Order
                ->setName01($data['customer_name01'])
                ->setName02($data['customer_name02'])
                ->setKana01($data['customer_kana01'])
                ->setKana02($data['customer_kana02'])
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
                [
                    'Order' => $Order,
                    'data' => $data,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_CUSTOMER_INITIALIZE, $event);
            log_info('非会員お客様情報変更処理完了', [$Order->getId()]);
            $message = ['status' => 'OK', 'kana01' => $data['customer_kana01'], 'kana02' => $data['customer_kana02']];
            $response = new Response(json_encode($message));
            $response->headers->set('Content-Type', 'application/json');
        } catch (\Exception $e) {
            log_error('予期しないエラー', [$e->getMessage()]);
            $response = new Response(json_encode(['status' => 'NG']), 500);
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     * 非会員でのお客様情報変更時の入力チェック
     *
     * @param array $data リクエストパラメータ
     *
     * @return array
     */
    protected function customerValidation(array &$data)
    {
        // 入力チェック
        $errors = [];

        $errors[] = $this->validator->validate(
            $data['customer_name01'],
            [
                new Assert\NotBlank(),
                new Assert\Length(['max' => $this->eccubeConfig['eccube_name_len']]),
                new Assert\Regex(
                    ['pattern' => '/^[^\s ]+$/u', 'message' => 'form.type.name.firstname.nothasspace']
                ),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_name02'],
            [
                new Assert\NotBlank(),
                new Assert\Length(['max' => $this->eccubeConfig['eccube_name_len']]),
                new Assert\Regex(
                    ['pattern' => '/^[^\s ]+$/u', 'message' => 'form.type.name.firstname.nothasspace']
                ),
            ]
        );

        $data['customer_kana01'] = mb_convert_kana($data['customer_kana01'], 'CV', 'utf-8');
        $errors[] = $this->validator->validate(
            $data['customer_kana01'],
            [
                new Assert\NotBlank(),
                new Assert\Length(['max' => $this->eccubeConfig['eccube_kana_len']]),
                new Assert\Regex(['pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u']),
            ]
        );
        $data['customer_kana02'] = mb_convert_kana($data['customer_kana02'], 'CV', 'utf-8');
        $errors[] = $this->validator->validate(
            $data['customer_kana02'],
            [
                new Assert\NotBlank(),
                new Assert\Length(['max' => $this->eccubeConfig['eccube_kana_len']]),
                new Assert\Regex(['pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u']),
        ]);

        $errors[] = $this->validator->validate(
            $data['customer_company_name'],
            [
                new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_tel01'],
            [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                new Assert\Length(
                    ['max' => $this->eccubeConfig['eccube_tel_len'], 'min' => $this->eccubeConfig['eccube_tel_len_min']]
                ),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_tel02'],
            [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                new Assert\Length(
                    ['max' => $this->eccubeConfig['eccube_tel_len'], 'min' => $this->eccubeConfig['eccube_tel_len_min']]
                ),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_tel03'],
            [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                new Assert\Length(
                    ['max' => $this->eccubeConfig['eccube_tel_len'], 'min' => $this->eccubeConfig['eccube_tel_len_min']]
                ),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_zip01'],
            [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                new Assert\Length(
                    ['min' => $this->eccubeConfig['eccube_zip01_len'], 'max' => $this->eccubeConfig['eccube_zip01_len']]
                ),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_zip02'],
            [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                new Assert\Length(
                    ['min' => $this->eccubeConfig['eccube_zip02_len'], 'max' => $this->eccubeConfig['eccube_zip02_len']]
                ),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_addr01'],
            [
                new Assert\NotBlank(),
                new Assert\Length(['max' => $this->eccubeConfig['eccube_address1_len']]),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_addr02'],
            [
                new Assert\NotBlank(),
                new Assert\Length(['max' => $this->eccubeConfig['eccube_address2_len']]),
            ]
        );

        $errors[] = $this->validator->validate(
            $data['customer_email'],
            [
                new Assert\NotBlank(),
                new Assert\Email(['strict' => true]),
            ]
        );

        return $errors;
    }
}

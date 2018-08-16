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
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\ShoppingShippingType;
use Eccube\Form\Type\ShippingMultipleType;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\CartService;
use Eccube\Service\OrderHelper;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\ShoppingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShippingMultipleController extends AbstractShoppingController
{
    /**
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @var OrderItemTypeRepository
     */
    protected $orderItemTypeRepository;

    /**
     * @var ShoppingService
     */
    protected $shoppingService;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var PurchaseFlow
     */
    protected $cartPurchaseFlow;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * ShippingMultipleController constructor.
     *
     * @param PrefRepository $prefRepository
     * @param OrderItemTypeRepository $orderItemTypeRepository
     * @param ShoppingService $shoppingService
     * @param CartService $cartService ,
     * @param OrderHelper $orderHelper
     */
    public function __construct(
        PrefRepository $prefRepository,
        OrderRepository $orderRepository,
        OrderItemTypeRepository $orderItemTypeRepository,
        ShoppingService $shoppingService,
        CartService $cartService,
        OrderHelper $orderHelper,
        PurchaseFlow $cartPurchaseFlow
    ) {
        $this->prefRepository = $prefRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->shoppingService = $shoppingService;
        $this->cartService = $cartService;
        $this->orderHelper = $orderHelper;
        $this->cartPurchaseFlow = $cartPurchaseFlow;
    }

    /**
     * 複数配送処理
     *
     * @Route("/shopping/shipping_multiple", name="shopping_shipping_multiple")
     * @Template("Shopping/shipping_multiple.twig")
     */
    public function index(Request $request)
    {
        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        /** @var \Eccube\Entity\Order $Order */
        $Order = $this->shoppingService->getOrder(OrderStatus::PROCESSING);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
            $this->addError('front.shopping.order.error');

            return $this->redirectToRoute('shopping_error');
        }

        // 処理しやすいようにすべてのShippingItemをまとめる
        $OrderItems = $Order->getProductOrderItems();

        // Orderに含まれる商品ごとの数量を求める
        $ItemQuantitiesByClassId = [];
        foreach ($OrderItems as $item) {
            $itemId = $item->getProductClass()->getId();
            $quantity = $item->getQuantity();
            if (array_key_exists($itemId, $ItemQuantitiesByClassId)) {
                $ItemQuantitiesByClassId[$itemId] += $quantity;
            } else {
                $ItemQuantitiesByClassId[$itemId] = $quantity;
            }
        }

        // FormBuilder用に商品ごとにShippingItemをまとめる
        $OrderItemsForFormBuilder = [];
        $tmpAddedClassIds = [];
        foreach ($OrderItems as $item) {
            $itemId = $item->getProductClass()->getId();
            if (!in_array($itemId, $tmpAddedClassIds)) {
                $OrderItemsForFormBuilder[] = $item;
                $tmpAddedClassIds[] = $itemId;
            }
        }

        // Form生成
        $builder = $this->formFactory->createBuilder();
        $builder
            ->add('shipping_multiple', CollectionType::class, [
                'entry_type' => ShippingMultipleType::class,
                'data' => $OrderItemsForFormBuilder,
                'allow_add' => true,
                'allow_delete' => true,
            ]);
        // Event
        $event = new EventArgs(
            [
                'builder' => $builder,
                'Order' => $Order,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            log_info('複数配送設定処理開始', [$Order->getId()]);

            $data = $form['shipping_multiple'];

            // フォームの入力から、送り先ごとに商品の数量を集計する
            $arrOrderItemTemp = [];
            foreach ($data as $mulitples) {
                $OrderItem = $mulitples->getData();
                foreach ($mulitples as $items) {
                    foreach ($items as $item) {
                        $CustomerAddress = $item['customer_address']->getData();
                        $customerAddressName = $CustomerAddress->getShippingMultipleDefaultName();

                        $itemId = $OrderItem->getProductClass()->getId();
                        $quantity = $item['quantity']->getData();

                        if (isset($arrOrderItemTemp[$customerAddressName]) && array_key_exists($itemId, $arrOrderItemTemp[$customerAddressName])) {
                            $arrOrderItemTemp[$customerAddressName][$itemId] = $arrOrderItemTemp[$customerAddressName][$itemId] + $quantity;
                        } else {
                            $arrOrderItemTemp[$customerAddressName][$itemId] = $quantity;
                        }
                    }
                }
            }

            // フォームの入力から、商品ごとの数量を集計する
            $itemQuantities = [];
            foreach ($arrOrderItemTemp as $FormItemByAddress) {
                foreach ($FormItemByAddress as $itemId => $quantity) {
                    if (array_key_exists($itemId, $itemQuantities)) {
                        $itemQuantities[$itemId] = $itemQuantities[$itemId] + $quantity;
                    } else {
                        $itemQuantities[$itemId] = $quantity;
                    }
                }
            }

            // -- ここから先がお届け先を再生成する処理 --

            // お届け先情報をすべて削除
            /** @var Shipping $Shipping */
            foreach ($Order->getShippings() as $Shipping) {
                foreach ($Shipping->getOrderItems() as $OrderItem) {
                    $Shipping->removeOrderItem($OrderItem);
                    $Order->removeOrderItem($OrderItem);
                    $this->entityManager->remove($OrderItem);
                }
                $Order->removeShipping($Shipping);
                $this->entityManager->remove($Shipping);
            }

            // お届け先のリストを作成する
            $ShippingList = [];
            foreach ($data as $mulitples) {
                $OrderItem = $mulitples->getData();
                $ProductClass = $OrderItem->getProductClass();
                $Delivery = $OrderItem->getShipping()->getDelivery();
                $saleTypeId = $ProductClass->getSaleType()->getId();

                foreach ($mulitples as $items) {
                    foreach ($items as $item) {
                        $CustomerAddress = $item['customer_address']->getData();
                        $customerAddressName = $CustomerAddress->getShippingMultipleDefaultName();

                        if (isset($ShippingList[$customerAddressName][$saleTypeId])) {
                            continue;
                        }
                        $Shipping = new Shipping();
                        $Shipping
                            ->setOrder($Order)
                            ->setFromCustomerAddress($CustomerAddress)
                            ->setDelivery($Delivery);
                        $Order->addShipping($Shipping);
                        $ShippingList[$customerAddressName][$saleTypeId] = $Shipping;
                    }
                }
            }
            // お届け先のリストを保存
            foreach ($ShippingList as $ShippingListByAddress) {
                foreach ($ShippingListByAddress as $Shipping) {
                    $this->entityManager->persist($Shipping);
                }
            }

            $ProductOrderType = $this->orderItemTypeRepository->find(OrderItemType::PRODUCT);

            // お届け先に、配送商品の情報(OrderItem)を関連付ける
            foreach ($data as $mulitples) {
                /** @var OrderItem $OrderItem */
                $OrderItem = $mulitples->getData();
                $ProductClass = $OrderItem->getProductClass();
                $Product = $OrderItem->getProduct();
                $saleTypeId = $ProductClass->getSaleType()->getId();
                $productClassId = $ProductClass->getId();

                foreach ($mulitples as $items) {
                    foreach ($items as $item) {
                        $CustomerAddress = $item['customer_address']->getData();
                        $customerAddressName = $CustomerAddress->getShippingMultipleDefaultName();

                        // お届け先から商品の数量を取得
                        $quantity = 0;
                        if (isset($arrOrderItemTemp[$customerAddressName]) && array_key_exists($productClassId, $arrOrderItemTemp[$customerAddressName])) {
                            $quantity = $arrOrderItemTemp[$customerAddressName][$productClassId];
                            unset($arrOrderItemTemp[$customerAddressName][$productClassId]);
                        } else {
                            // この配送先には送る商品がないのでスキップ（通常ありえない）
                            continue;
                        }

                        // 関連付けるお届け先のインスタンスを取得
                        $Shipping = $ShippingList[$customerAddressName][$saleTypeId];

                        // インスタンスを生成して保存
                        $OrderItem = new OrderItem();
                        $OrderItem->setShipping($Shipping)
                            ->setOrder($Order)
                            ->setProductClass($ProductClass)
                            ->setProduct($Product)
                            ->setProductName($Product->getName())
                            ->setProductCode($ProductClass->getCode())
                            ->setPrice($ProductClass->getPrice02())
                            ->setQuantity($quantity)
                            ->setOrderItemType($ProductOrderType);

                        $ClassCategory1 = $ProductClass->getClassCategory1();
                        if (!is_null($ClassCategory1)) {
                            $OrderItem->setClasscategoryName1($ClassCategory1->getName());
                            $OrderItem->setClassName1($ClassCategory1->getClassName()->getName());
                        }
                        $ClassCategory2 = $ProductClass->getClassCategory2();
                        if (!is_null($ClassCategory2)) {
                            $OrderItem->setClasscategoryName2($ClassCategory2->getName());
                            $OrderItem->setClassName2($ClassCategory2->getClassName()->getName());
                        }
                        $Shipping->addOrderItem($OrderItem);
                        $Order->addOrderItem($OrderItem);
                        $this->entityManager->persist($OrderItem);
                    }
                }
            }

            // 合計金額の再計算
            $flowResult = $this->validatePurchaseFlow($Order);
            if ($flowResult->hasWarning()) {
                return [
                    'form' => $form->createView(),
                    'OrderItems' => $OrderItemsForFormBuilder,
                    'compItemQuantities' => $ItemQuantitiesByClassId,
                    'errors' => $errors,
                ];
            }
            if ($flowResult->hasError()) {
                return $this->redirectToRoute('cart');
            }

            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Order' => $Order,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_COMPLETE, $event);

            log_info('複数配送設定処理完了', [$Order->getId()]);

            $this->entityManager->refresh($Order);

            $quantityByProductClass = [];
            foreach ($Order->getProductOrderItems() as $Item) {
                $id = $Item->getProductClass()->getId();
                if (isset($quantityByProductClass[$id])) {
                    $quantityByProductClass[$id] += $Item->getQuantity();
                } else {
                    $quantityByProductClass[$id] = $Item->getQuantity();
                }
            }
            $Cart = $this->cartService->getCart();
            foreach ($Cart->getCartItems() as $CartItem) {
                $id = $CartItem->getProductClass()->getId();
                if (isset($quantityByProductClass[$id])) {
                    $CartItem->setQuantity($quantityByProductClass[$id]);
                }
            }

            $this->cartPurchaseFlow->validate($Cart, new PurchaseContext());
            $this->cartService->save();

            return $this->redirectToRoute('shopping');
        }

        return [
            'form' => $form->createView(),
            'OrderItems' => $OrderItemsForFormBuilder,
            'compItemQuantities' => $ItemQuantitiesByClassId,
            'errors' => $errors,
        ];
    }

    /**
     * 複数配送設定時の新規お届け先の設定
     *
     * @Route("/shopping/shipping_multiple_edit", name="shopping_shipping_multiple_edit")
     * @Template("Shopping/shipping_multiple_edit.twig")
     */
    public function shippingMultipleEdit(Request $request)
    {
        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        /** @var Customer $Customer */
        $Customer = $this->getUser();
        $CustomerAddress = new CustomerAddress();
        $builder = $this->formFactory->createBuilder(ShoppingShippingType::class, $CustomerAddress);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('複数配送のお届け先追加処理開始');

            if ($this->isGranted('ROLE_USER')) {
                $CustomerAddresses = $Customer->getCustomerAddresses();

                $count = count($CustomerAddresses);
                if ($count >= $this->eccubeConfig['eccube_deliv_addr_max']) {
                    return [
                        'error' => trans('delivery.text.error.max_delivery_address'),
                        'form' => $form->createView(),
                    ];
                }

                $CustomerAddress->setCustomer($Customer);
                $this->entityManager->persist($CustomerAddress);
                $this->entityManager->flush($CustomerAddress);
            } else {
                // 非会員用のセッションに追加
                $CustomerAddresses = $this->session->get($this->sessionCustomerAddressKey);
                $CustomerAddresses = unserialize($CustomerAddresses);
                $CustomerAddresses[] = $CustomerAddress;
                $this->session->set($this->sessionCustomerAddressKey, serialize($CustomerAddresses));
            }

            $event = new EventArgs(
                [
                    'form' => $form,
                    'CustomerAddresses' => $CustomerAddresses,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_COMPLETE, $event);

            log_info('複数配送のお届け先追加処理完了');

            return $this->redirectToRoute('shopping_shipping_multiple');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}

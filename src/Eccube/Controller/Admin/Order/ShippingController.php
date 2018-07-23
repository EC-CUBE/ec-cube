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

namespace Eccube\Controller\Admin\Order;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Form\Type\Admin\ShippingType;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\OrderItemRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Service\TaxRuleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Eccube\Service\MailService;

class ShippingController extends AbstractController
{
    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var TaxRuleService
     */
    protected $taxRuleService;

    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Eccube\Service\MailService
     */
    protected $mailService;

    /**
     * EditController constructor.
     *
     * @param MailService $mailService
     * @param OrderItemRepository $orderItemRepository
     * @param CategoryRepository $categoryRepository
     * @param DeliveryRepository $deliveryRepository
     * @param TaxRuleService $taxRuleService
     * @param ShippingRepository $shippingRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        MailService $mailService,
        OrderItemRepository $orderItemRepository,
        CategoryRepository $categoryRepository,
        DeliveryRepository $deliveryRepository,
        TaxRuleService $taxRuleService,
        ShippingRepository $shippingRepository,
        SerializerInterface $serializer
    ) {
        $this->mailService = $mailService;
        $this->orderItemRepository = $orderItemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->taxRuleService = $taxRuleService;
        $this->shippingRepository = $shippingRepository;
        $this->serializer = $serializer;
    }

    /**
     * 出荷登録/編集画面.
     *
     * @Route("/%eccube_admin_route%/shipping/{id}/edit", requirements={"id" = "\d+"}, name="admin_shipping_edit")
     * @Template("@admin/Order/shipping.twig")
     */
    public function index(Request $request, Order $Order)
    {
        dump($_POST);
        $TargetShippings = $Order->getShippings();
        $OriginShippings = [];
        $OriginOrderItems = [];

        // 編集前の受注情報を保持
        foreach ($TargetShippings as $key => $TargetShipping) {
            $OriginShippings[$key] = clone $TargetShipping;

            // 編集前のお届け先のアイテム情報を保持
            $OriginOrderItems[$key] = new ArrayCollection();

            foreach ($TargetShipping->getOrderItems() as $OrderItem) {
                $OriginOrderItems[$key]->add($OrderItem);
            }
        }

        $builder = $this->formFactory->createBuilder();
        $builder
            ->add('shippings', CollectionType::class, [
                'entry_type' => ShippingType::class,
                'data' => $TargetShippings,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]);

        // 配送先の追加フラグ
        $builder
            ->add('add_shipping', HiddenType::class, [
                'mapped' => false,
            ]);

        // 配送先の追加フラグが立っている場合は新しいお届け先を追加
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if ($data['add_shipping']) {
                $Shippings = $data['shippings'];
                $newShipping = ['Delivery' => ''];
                $Shippings[] = $newShipping;
                $data['shippings'] = $Shippings;
                $data['add_shipping'] = '';
                $event->setData($data);
            }
        });

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $request->get('mode') == 'register') {
            // 削除された項目の削除
            /** @var Shipping $OriginShipping */
            foreach ($OriginShippings as $key => $OriginShipping) {
                // 削除された明細の削除
                /** @var OrderItem $OriginOrderItem */
                foreach ($OriginOrderItems[$key] as $OriginOrderItem) {
                    if (false === $TargetShippings[$key]->getOrderItems()->contains($OriginOrderItem)) {
                        $TargetShippings[$key]->removeOrderItem($OriginOrderItem); // 不要かも
                        $OriginOrderItem->setShipping(null);
                        $Order->removeOrderItem($OriginOrderItem);
                        $OriginOrderItem->setOrder(null);
                    }
                }
            }

            foreach ($TargetShippings as $key => $TargetShipping) {
                // TODO: Should move logic out of controller such as service, modal

                // FIXME 税額計算は CalculateService で処理する. ここはテストを通すための暫定処理
                // see EditControllerTest::testOrderProcessingWithTax
//                $OrderItems = $TargetShipping->getOrderItems();
//                $taxtotal = 0;
//                foreach ($OrderItems as $OrderItem) {
//                    $tax = $this->taxRuleService
//                        ->calcTax($OrderItem->getPrice(), $OrderItem->getTaxRate(), $OrderItem->getTaxRule());
//                    $OrderItem->setPriceIncTax($OrderItem->getPrice() + $tax);
//
//                    $taxtotal += $tax * $OrderItem->getQuantity();
//                }

                log_info('出荷登録開始', [$TargetShipping->getId()]);
                // TODO 在庫の有無や販売制限数のチェックなども行う必要があるため、完了処理もcaluclatorのように抽象化できないか検討する.
                // TODO 後続にある会員情報の更新のように、完了処理もcaluclatorのように抽象化できないか検討する.
                // 画面上で削除された明細をremove
//                if (array_key_exists($key, $OriginOrderItems)) {
//                    /** @var OrderItem $OrderItem */
//                    foreach ($OriginOrderItems[$key] as $OrderItem) {
//                        if (false === $TargetShipping->getOrderItems()->contains($OrderItem)) {
//                            $TargetShipping->removeOrderItem($OrderItem); // 不要かも
//                            $OrderItem->setShipping(null);
//                            $Order->removeOrderItem($OrderItem);
//                            $OrderItem->setOrder(null);
//                        }
//                    }
//                }

                foreach ($TargetShipping->getOrderItems() as $OrderItem) {
                    $TargetShipping->addOrderItem($OrderItem); // 不要かも
                    $OrderItem->setShipping($TargetShipping);
                    $Order->addOrderItem($OrderItem);
                    $OrderItem->setOrder($Order);
                }

                $TargetShipping->setOrder($Order);

                // 出荷ステータス変更時の処理
//                if ($TargetShipping->isShipped()) {
//                    // 「出荷済み」にステータスが変更された場合
//                    if ($OriginShippings[$key]->isShipped() == false) {
//                        // 出荷メールを送信
//                        if ($form->get('notify_email')->getData()) {
//                            try {
//                                $this->mailService->sendShippingNotifyMail(
//                                    $TargetShipping
//                                );
//                            } catch (\Exception $e) {
//                                log_error('メール通知エラー', [$TargetShipping->getId(), $e]);
//                                $this->addError(
//                                    'admin.shipping.edit.shipped_mail_failed',
//                                    'admin'
//                                );
//                            }
//                        }
//                    }
//                }
            }

            try {
                foreach ($TargetShippings as $TargetShipping) {
                    $this->entityManager->persist($TargetShipping);
                }
                $this->entityManager->flush();

                $this->addSuccess('admin.shipping.edit.save.complete', 'admin');
                $this->addInfo('admin.shipping.edit.save.info', 'admin');
                log_info('出荷登録完了', [$Order->getId()]);

                return $this->redirectToRoute('admin_shipping_edit', ['id' => $Order->getId()]);
            } catch (\Exception $e) {
                log_error('出荷登録エラー', [$Order->getId(), $e]);
                $this->addError('admin.flash.register_failed', 'admin');
            }
        } elseif ($form->isSubmitted() && $request->get('mode') == 'register' && $form->getErrors(true)) {
            $this->addError('admin.flash.register_failed', 'admin');
        }

        // 商品検索フォーム
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $searchProductModalForm = $builder->getForm();

        // 配送業者のお届け時間
        $times = [];
        $deliveries = $this->deliveryRepository->findAll();
        foreach ($deliveries as $Delivery) {
            $deliveryTiems = $Delivery->getDeliveryTimes();
            foreach ($deliveryTiems as $DeliveryTime) {
                $times[$Delivery->getId()][$DeliveryTime->getId()] = $DeliveryTime->getDeliveryTime();
            }
        }

        return [
            'form' => $form->createView(),
            'searchProductModalForm' => $searchProductModalForm->createView(),
            'Order' => $Order,
            'Shippings' => $TargetShippings,
            'shippingDeliveryTimes' => $this->serializer->serialize($times, 'json'),
        ];
    }
}

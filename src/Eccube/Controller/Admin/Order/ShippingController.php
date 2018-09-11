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
use Eccube\Service\MailService;
use Eccube\Service\OrderStateMachine;
use Eccube\Service\TaxRuleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
     * @var OrderStateMachine
     */
    private $orderStateMachine;

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
     * @param OrderStateMachine $orderStateMachine
     */
    public function __construct(
        MailService $mailService,
        OrderItemRepository $orderItemRepository,
        CategoryRepository $categoryRepository,
        DeliveryRepository $deliveryRepository,
        TaxRuleService $taxRuleService,
        ShippingRepository $shippingRepository,
        SerializerInterface $serializer,
        OrderStateMachine $orderStateMachine
    ) {
        $this->mailService = $mailService;
        $this->orderItemRepository = $orderItemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->taxRuleService = $taxRuleService;
        $this->shippingRepository = $shippingRepository;
        $this->serializer = $serializer;
        $this->orderStateMachine = $orderStateMachine;
    }

    /**
     * 出荷登録/編集画面.
     *
     * @Route("/%eccube_admin_route%/shipping/{id}/edit", requirements={"id" = "\d+"}, name="admin_shipping_edit")
     * @Template("@admin/Order/shipping.twig")
     */
    public function index(Request $request, Order $Order)
    {
        $TargetShippings = $Order->getShippings();

        // 編集前の受注情報を保持
        $OriginShippings = new ArrayCollection();
        $OriginOrderItems = [];

        foreach ($TargetShippings as $key => $TargetShipping) {
            $OriginShippings->add($TargetShipping);

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
            log_info('出荷登録開始', [$TargetShipping->getId()]);

            // 削除された項目の削除
            /** @var Shipping $OriginShipping */
            foreach ($OriginShippings as $key => $OriginShipping) {
                if (false === $TargetShippings->contains($OriginShipping)) {
                    // お届け先自体が削除された場合
                    // 削除されたお届け先に紐づく明細の削除
                    /** @var OrderItem $OriginOrderItem */
                    foreach ($OriginOrderItems[$key] as $OriginOrderItem) {
                        $this->entityManager->remove($OriginOrderItem);
                    }

                    // 削除されたお届け先の削除
                    $this->entityManager->remove($OriginShipping);
                } else {
                    // お届け先は削除されていない場合
                    // 削除された明細の削除
                    /** @var OrderItem $OriginOrderItem */
                    foreach ($OriginOrderItems[$key] as $OriginOrderItem) {
                        if (false === $TargetShippings[$key]->getOrderItems()->contains($OriginOrderItem)) {
                            $this->entityManager->remove($OriginOrderItem);
                        }
                    }
                }
            }

            // 追加された項目の追加
            foreach ($TargetShippings as $TargetShipping) {
                // 追加された明細の追加
                foreach ($TargetShipping->getOrderItems() as $OrderItem) {
                    $OrderItem->setShipping($TargetShipping);
                    $OrderItem->setOrder($Order);
                }

                // 追加されたお届け先の追加
                $TargetShipping->setOrder($Order);
            }

            try {
                foreach ($TargetShippings as $TargetShipping) {
                    $this->entityManager->persist($TargetShipping);
                }
                $this->entityManager->flush();

                $this->addInfo('admin.order.shipping_save_message', 'admin');
                $this->addSuccess('admin.common.save_complete', 'admin');
                log_info('出荷登録完了', [$Order->getId()]);

                return $this->redirectToRoute('admin_shipping_edit', ['id' => $Order->getId()]);
            } catch (\Exception $e) {
                log_error('出荷登録エラー', [$Order->getId(), $e]);
                $this->addError('admin.flash.register_failed', 'admin');
            }
        } elseif ($form->isSubmitted() && $request->get('mode') == 'register' && $form->getErrors(true)) {
            $this->addError('admin.common.save_error', 'admin');
        }

        // 商品検索フォーム
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $searchProductModalForm = $builder->getForm();

        // 配送業者のお届け時間
        $times = [];
        $deliveries = $this->deliveryRepository->findAll();
        foreach ($deliveries as $Delivery) {
            $deliveryTimes = $Delivery->getDeliveryTimes();
            foreach ($deliveryTimes as $DeliveryTime) {
                $times[$Delivery->getId()][$DeliveryTime->getId()] = $DeliveryTime->getDeliveryTime();
            }
        }

        return [
            'form' => $form->createView(),
            'searchProductModalForm' => $searchProductModalForm->createView(),
            'Order' => $Order,
            'shippingDeliveryTimes' => $this->serializer->serialize($times, 'json'),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/shipping/preview_notify_mail/{id}", requirements={"id" = "\d+"}, name="admin_shipping_preview_notify_mail")
     *
     * @param Shipping $Shipping
     *
     * @return Response
     *
     * @throws \Twig_Error
     */
    public function previewShippingNotifyMail(Shipping $Shipping)
    {
        return new Response($this->mailService->getShippingNotifyMailBody($Shipping, $Shipping->getOrder(), null, true));
    }

    /**
     * @Route("/%eccube_admin_route%/shipping/notify_mail/{id}", requirements={"id" = "\d+"}, name="admin_shipping_notify_mail", methods={"PUT"})
     *
     * @param Shipping $Shipping
     *
     * @return JsonResponse
     *
     * @throws \Twig_Error
     */
    public function notifyMail(Shipping $Shipping)
    {
        $this->isTokenValid();

        $this->mailService->sendShippingNotifyMail($Shipping);

        $Shipping->setMailSendDate(new \DateTime());
        $this->shippingRepository->save($Shipping);
        $this->entityManager->flush();

        return $this->json([
            'mail' => true,
            'shipped' => false,
        ]);
    }
}

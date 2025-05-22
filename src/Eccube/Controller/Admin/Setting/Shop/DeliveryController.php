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

namespace Eccube\Controller\Admin\Setting\Shop;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\DeliveryType;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\DeliveryTimeRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\Master\SaleTypeRepository;
use Eccube\Repository\PaymentOptionRepository;
use Eccube\Twig\Extension\EccubeExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DeliveryController
 */
class DeliveryController extends AbstractController
{
    /**
     * @var PaymentOptionRepository
     */
    protected $paymentOptionRepository;

    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var DeliveryTimeRepository
     */
    protected $deliveryTimeRepository;

    /**
     * @var DeliveryTimeRepository
     */
    protected $saleTypeRepository;

    /**
     * DeliveryController constructor.
     *
     * @param PaymentOptionRepository $paymentOptionRepository
     * @param DeliveryFeeRepository $deliveryFeeRepository
     * @param PrefRepository $prefRepository
     * @param DeliveryRepository $deliveryRepository
     */
    public function __construct(PaymentOptionRepository $paymentOptionRepository, DeliveryFeeRepository $deliveryFeeRepository, PrefRepository $prefRepository, DeliveryRepository $deliveryRepository, DeliveryTimeRepository $deliveryTimeRepository, SaleTypeRepository $saleTypeRepository)
    {
        $this->paymentOptionRepository = $paymentOptionRepository;
        $this->deliveryFeeRepository = $deliveryFeeRepository;
        $this->prefRepository = $prefRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryTimeRepository = $deliveryTimeRepository;
        $this->saleTypeRepository = $saleTypeRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/delivery", name="admin_setting_shop_delivery", methods={"GET"})
     * @Template("@admin/Setting/Shop/delivery.twig")
     */
    public function index(Request $request)
    {
        $Deliveries = $this->deliveryRepository
            ->findBy([], ['sort_no' => 'DESC']);

        $event = new EventArgs(
            [
                'Deliveries' => $Deliveries,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_INDEX_COMPLETE);

        return [
            'Deliveries' => $Deliveries,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/delivery/new", name="admin_setting_shop_delivery_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/setting/shop/delivery/{id}/edit", requirements={"id" = "\d+"}, name="admin_setting_shop_delivery_edit", methods={"GET", "POST"})
     * @Template("@admin/Setting/Shop/delivery_edit.twig")
     */
    public function edit(Request $request, EccubeExtension $extension, $id = null)
    {
        if (is_null($id)) {
            $SaleType = $this->saleTypeRepository->findOneBy([], ['sort_no' => 'ASC']);
            $Delivery = $this->deliveryRepository->findOneBy([], ['sort_no' => 'DESC']);

            $sortNo = 1;
            if ($Delivery) {
                $sortNo = $Delivery->getSortNo() + 1;
            }

            $Delivery = new Delivery();
            $Delivery
                ->setSortNo($sortNo)
                ->setVisible(true)
                ->setSaleType($SaleType);
        } else {
            $Delivery = $this->deliveryRepository->find($id);
            if (is_null($Delivery)) {
                throw new NotFoundHttpException();
            }
        }

        $originalDeliveryTimes = new ArrayCollection();

        foreach ($Delivery->getDeliveryTimes() as $deliveryTime) {
            $originalDeliveryTimes->add($deliveryTime);
        }

        // FormType: DeliveryFeeの生成
        $Prefs = $this->prefRepository
            ->findAll();

        foreach ($Prefs as $Pref) {
            $DeliveryFee = $this->deliveryFeeRepository
                ->findOneBy(
                    [
                        'Delivery' => $Delivery,
                        'Pref' => $Pref,
                    ]
                );
            if (!$DeliveryFee) {
                $DeliveryFee = new DeliveryFee();
                $DeliveryFee
                    ->setPref($Pref)
                    ->setDelivery($Delivery);
            }
            if (!$DeliveryFee->getFee()) {
                $Delivery->addDeliveryFee($DeliveryFee);
            }
        }

        $DeliveryFees = $Delivery->getDeliveryFees();
        $DeliveryFeesIndex = [];
        foreach ($DeliveryFees as $DeliveryFee) {
            $Delivery->removeDeliveryFee($DeliveryFee);
            $DeliveryFeesIndex[$DeliveryFee->getPref()->getId()] = $DeliveryFee;
        }
        ksort($DeliveryFeesIndex);
        foreach ($DeliveryFeesIndex as $DeliveryFee) {
            $Delivery->addDeliveryFee($DeliveryFee);
        }

        $builder = $this->formFactory
            ->createBuilder(DeliveryType::class, $Delivery);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Delivery' => $Delivery,
                'Prefs' => $Prefs,
                'DeliveryFees' => $DeliveryFees,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_EDIT_INITIALIZE);

        $form = $builder->getForm();

        // 支払方法をセット
        $Payments = [];
        foreach ($Delivery->getPaymentOptions() as $PaymentOption) {
            $Payments[] = $PaymentOption->getPayment();
        }

        $form['delivery_times']->setData($Delivery->getDeliveryTimes());
        $form['payments']->setData($Payments);

        // 登録ボタン押下
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $DeliveryData = $form->getData();

                // 配送時間の登録
                /** @var DeliveryTime $DeliveryTime */
                foreach ($originalDeliveryTimes as $DeliveryTime) {
                    if (false === $Delivery->getDeliveryTimes()->contains($DeliveryTime)) {
                        $this->entityManager->remove($DeliveryTime);
                    }
                }
                foreach ($DeliveryData['DeliveryTimes'] as $DeliveryTime) {
                    $DeliveryTime->setDelivery($Delivery);
                }

                // お支払いの登録
                $PaymentOptions = $this->paymentOptionRepository
                    ->findBy(['delivery_id' => $Delivery->getId()]);
                // 消す
                foreach ($PaymentOptions as $PaymentOption) {
                    $DeliveryData->removePaymentOption($PaymentOption);
                    $this->entityManager->remove($PaymentOption);
                }
                $this->entityManager->persist($DeliveryData);
                $this->entityManager->flush();

                // いれる
                $PaymentsData = $form->get('payments')->getData();
                foreach ($PaymentsData as $PaymentData) {
                    $PaymentOption = new PaymentOption();
                    $PaymentOption
                        ->setPaymentId($PaymentData->getId())
                        ->setPayment($PaymentData)
                        ->setDeliveryId($DeliveryData->getId())
                        ->setDelivery($DeliveryData);
                    $DeliveryData->addPaymentOption($PaymentOption);
                    $this->entityManager->persist($DeliveryData);
                }

                $this->entityManager->persist($DeliveryData);

                $this->entityManager->flush();

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'Delivery' => $Delivery,
                        'Prefs' => $Prefs,
                        'DeliveryFees' => $DeliveryFees,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_EDIT_COMPLETE);

                $this->addSuccess('admin.common.save_complete', 'admin');

                // 支払金額によって利用できない支払方法がある場合に警告を表示
                // @see https://github.com/EC-CUBE/ec-cube/pull/4940
                if (!empty($PaymentData)) {
                    $mergedRules = $this->getMergeRules($PaymentsData);
                    if (count($mergedRules) > 1) {
                        for ($i = 1; $i < count($mergedRules); $i++) {
                            $message = trans('admin.setting.shop.delivery.payment_warning', [
                                '%min%' => $extension->getPriceFilter($mergedRules[$i - 1]['max']),
                                '%max%' => $extension->getPriceFilter($mergedRules[$i]['min'] - 1),
                            ]);
                            $this->addWarning($message, 'admin');
                        }
                    }
                }

                return $this->redirectToRoute('admin_setting_shop_delivery_edit', ['id' => $Delivery->getId()]);
            }
        }

        return [
            'form' => $form->createView(),
            'delivery_id' => $Delivery->getId(),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/delivery/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_delivery_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Delivery $Delivery)
    {
        $this->isTokenValid();

        try {
            $this->entityManager->remove($Delivery);
            $this->entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addError(trans('admin.common.delete_error_foreign_key', ['%name%' => $Delivery->getName()]), 'admin');

            return $this->redirectToRoute('admin_setting_shop_delivery');
        }

        $sortNo = 1;
        $Delivs = $this->deliveryRepository
            ->findBy([], ['sort_no' => 'ASC']);

        foreach ($Delivs as $Deliv) {
            if ($Deliv->getId() != $Delivery->getId()) {
                $Deliv->setSortNo($sortNo);
                $sortNo++;
            }
        }

        $this->entityManager->flush();

        $event = new EventArgs(
            [
                'Delivs' => $Delivs,
                'Delivery' => $Delivery,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_DELETE_COMPLETE);

        $this->addSuccess('admin.common.delete_complete', 'admin');

        return $this->redirectToRoute('admin_setting_shop_delivery');
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/delivery/{id}/visibility", requirements={"id" = "\d+"}, name="admin_setting_shop_delivery_visibility", methods={"PUT"})
     */
    public function visibility(Request $request, Delivery $Delivery)
    {
        $this->isTokenValid();

        // 表示・非表示を切り替える
        if ($Delivery->isVisible()) {
            $message = trans('admin.common.to_hide_complete', ['%name%' => $Delivery->getName()]);
            $Delivery->setVisible(false);
        } else {
            $message = trans('admin.common.to_show_complete', ['%name%' => $Delivery->getName()]);
            $Delivery->setVisible(true);
        }
        $this->entityManager->persist($Delivery);

        $this->entityManager->flush();

        $event = new EventArgs(
            [
                'Delivery' => $Delivery,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_VISIBILITY_COMPLETE);

        $this->addSuccess($message, 'admin');

        return $this->redirectToRoute('admin_setting_shop_delivery');
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/delivery/sort_no/move", name="admin_setting_shop_delivery_sort_no_move", methods={"POST"})
     */
    public function moveSortNo(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        if ($this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $deliveryId => $sortNo) {
                $Delivery = $this->deliveryRepository->find($deliveryId);
                $Delivery->setSortNo($sortNo);
                $this->entityManager->persist($Delivery);
            }
            $this->entityManager->flush();
        }

        return $this->json('OK', 200);
    }

    /**
     * 利用条件の金額範囲を生成する.
     *
     * @param Payment[] $PaymentsData
     *
     * @return array
     */
    private function getMergeRules(array $PaymentsData)
    {
        // 手数料抜きの利用条件の一覧を作成
        $rules = array_map(function (Payment $Payment) {
            return [
                'min' => $Payment->getRuleMin() ? $Payment->getRuleMin() - $Payment->getCharge() : 0,
                'max' => $Payment->getRuleMax() ? $Payment->getRuleMax() - $Payment->getCharge() + 1 : PHP_INT_MAX,
            ];
        }, $PaymentsData);

        $mergeRules = [];

        foreach ($rules as $rule) {
            // かぶる条件があれば抽出
            $targetRules = array_filter($mergeRules, function ($mergeRule) use ($rule) {
                return $rule['min'] <= $mergeRule['max'] && $mergeRule['min'] <= $rule['max'];
            });

            if (count($targetRules) === 0) {
                $mergeRules[] = $rule;
            } else {
                // 被らない条件を抽出
                $mergeRules = array_filter($mergeRules, function ($mergeRule) use ($rule) {
                    return $rule['min'] > $mergeRule['max'] || $mergeRule['min'] > $rule['max'];
                });

                $targetRules[] = $rule;
                $min = min(array_map(function ($rule) {
                    return $rule['min'];
                }, $targetRules));

                $max = max(array_map(function ($rule) {
                    return $rule['max'];
                }, $targetRules));

                $mergeRules[] = ['min' => $min, 'max' => $max];
            }
        }

        usort($mergeRules, function ($a, $b) {
            if ($a['min'] == $b['min']) {
                return 0;
            }

            return ($a['min'] < $b['min']) ? -1 : 1;
        });

        return $mergeRules;
    }
}

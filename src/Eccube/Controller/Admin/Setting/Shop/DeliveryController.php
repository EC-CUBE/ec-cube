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


namespace Eccube\Controller\Admin\Setting\Shop;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Annotation\Inject;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryTime;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class DeliveryController
 *
 * @package Eccube\Controller\Admin\Setting\Shop
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
     * @Route("/%eccube_admin_route%/setting/shop/delivery", name="admin_setting_shop_delivery")
     * @Template("@admin/Setting/Shop/delivery.twig")
     */
    public function index(Request $request)
    {
        $Deliveries = $this->deliveryRepository
            ->findBy([], ['sort_no' => 'DESC']);

        $event = new EventArgs(
            array(
                'Deliveries' => $Deliveries,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_INDEX_COMPLETE, $event);

        return [
            'Deliveries' => $Deliveries,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/delivery/new", name="admin_setting_shop_delivery_new")
     * @Route("/%eccube_admin_route%/setting/shop/delivery/{id}/edit", requirements={"id" = "\d+"}, name="admin_setting_shop_delivery_edit")
     * @Template("@admin/Setting/Shop/delivery_edit.twig")
     */
    public function edit(Request $request, $id = null)
    {

        if (is_null($id)) {
            $SaleType = $this->saleTypeRepository->findOneBy(array(), array('sort_no' => 'DESC'));
            $Delivery = $this->deliveryRepository->findOneBy(array(), array('sort_no' => 'DESC'));

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
                ->findOrCreate(
                    array(
                        'Delivery' => $Delivery,
                        'Pref' => $Pref,
                    )
                );
            if (!$DeliveryFee->getFee()) {
                $Delivery->addDeliveryFee($DeliveryFee);
            }
        }

        $DeliveryFees = $Delivery->getDeliveryFees();
        $DeliveryFeesIndex = array();
        foreach ($DeliveryFees as $DeliveryFee) {
            $Delivery->removeDeliveryFee($DeliveryFee);
            $DeliveryFeesIndex[$DeliveryFee->getPref()->getId()] = $DeliveryFee;
        }
        ksort($DeliveryFeesIndex);
        foreach ($DeliveryFeesIndex as $timeId => $DeliveryFee) {
            $Delivery->addDeliveryFee($DeliveryFee);
        }

        $builder = $this->formFactory
            ->createBuilder(DeliveryType::class, $Delivery);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Delivery' => $Delivery,
                'Prefs' => $Prefs,
                'DeliveryFees' => $DeliveryFees,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        // 支払方法をセット
        $Payments = array();
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
                    ->findBy(array('delivery_id' => $Delivery->getId()));
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
                    array(
                        'form' => $form,
                        'Delivery' => $Delivery,
                        'Prefs' => $Prefs,
                        'DeliveryFees' => $DeliveryFees,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_EDIT_COMPLETE, $event);

                $this->addSuccess('admin.register.complete', 'admin');

                return $this->redirectToRoute('admin_setting_shop_delivery');
            }
        }

        return [
            'form' => $form->createView(),
            'delivery_id' => $Delivery->getId(),
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/setting/shop/delivery/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_delivery_delete")
     */
    public function delete(Request $request, Delivery $Delivery)
    {
        $this->isTokenValid();

        $this->entityManager->remove($Delivery);
        $this->entityManager->flush();

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
            array(
                'Delivs' => $Delivs,
                'Delivery' => $Delivery,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_DELETE_COMPLETE, $event);

        $this->addSuccess('admin.delete.complete', 'admin');

        return $this->redirectToRoute('admin_setting_shop_delivery');
    }

    /**
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/setting/shop/delivery/{id}/visibility", requirements={"id" = "\d+"}, name="admin_setting_shop_delivery_visibility")
     */
    public function visibility(Request $request, Delivery $Delivery)
    {
        $this->isTokenValid();

        // 表示・非表示を切り替える
        if ($Delivery->isVisible()) {
            $message = 'admin.delivery.hidden.complete';
            $Delivery->setVisible(false);
        } else {
            $message = 'admin.delivery.visible.complete';
            $Delivery->setVisible(true);
        }
        $this->entityManager->persist($Delivery);

        $this->entityManager->flush();

        $event = new EventArgs(
            array(
                'Delivery' => $Delivery,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_VISIBILITY_COMPLETE, $event);

        $this->addSuccess($message, 'admin');

        return $this->redirectToRoute('admin_setting_shop_delivery');
    }

    /**
     * @Method("POST")
     * @Route("/%eccube_admin_route%/setting/shop/delivery/sort_no/move", name="admin_setting_shop_delivery_sort_no_move")
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
}

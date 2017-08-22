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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Delivery;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\DeliveryType;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\PaymentOptionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Component
 * @Route(service=DeliveryController::class)
 */
class DeliveryController extends AbstractController
{
    /**
     * @Inject(PaymentOptionRepository::class)
     * @var PaymentOptionRepository
     */
    protected $paymentOptionRepository;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(DeliveryFeeRepository::class)
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * @Inject(PrefRepository::class)
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject(DeliveryRepository::class)
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @Route("/{_admin}/setting/shop/delivery", name="admin_setting_shop_delivery")
     * @Template("Setting/Shop/delivery.twig")
     */
    public function index(Application $app, Request $request)
    {
        $Deliveries = $this->deliveryRepository
            ->findBy(
                array('del_flg' => 0),
                array('rank' => 'DESC')
            );

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
     * @Route("/{_admin}/setting/shop/delivery/new", name="admin_setting_shop_delivery_new")
     * @Route("/{_admin}/setting/shop/delivery/{id}/edit", requirements={"id":"\d+"}, name="admin_setting_shop_delivery_edit")
     * @Template("Setting/Shop/delivery_edit.twig")
     */
    public function edit(Application $app, Request $request, Delivery $Delivery = null)
    {
        if (is_null($Delivery)) {
            // FIXME
            $Delivery = $this->deliveryRepository
                ->findOrCreate(0);
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

        // FormType: DeliveryTimeの生成
        $DeliveryTimes = $Delivery->getDeliveryTimes();
        $loop = 16 - count($DeliveryTimes);
        for ($i = 1; $i <= $loop; $i++) {
            $DeliveryTime = new \Eccube\Entity\DeliveryTime();
            $DeliveryTime->setDelivery($Delivery);
            $Delivery->addDeliveryTime($DeliveryTime);
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
                $DeliveryTimes = $form['delivery_times']->getData();
                foreach ($DeliveryTimes as $DeliveryTime) {
                    if (is_null($DeliveryTime->getDeliveryTime())) {
                        $Delivery->removeDeliveryTime($DeliveryTime);
                        $this->entityManager->remove($DeliveryTime);
                    }
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
                    $PaymentOption = new \Eccube\Entity\PaymentOption();
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

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app->url('admin_setting_shop_delivery'));
            }
        }

        return [
            'form' => $form->createView(),
            'delivery_id' => $Delivery->getId(),
        ];
    }

    /**
     * @Method("DELETE")
     * @Route("/{_admin}/setting/shop/delivery/{id}/delete", requirements={"id":"\d+"}, name="admin_setting_shop_delivery_delete")
     */
    public function delete(Application $app, Request $request, Delivery $Delivery)
    {
        $this->isTokenValid($app);

        $Delivery
            ->setDelFlg(Constant::ENABLED)
            ->setRank(0);

        $this->entityManager->persist($Delivery);

        $rank = 1;
        $Delivs = $this->deliveryRepository
            ->findBy(
                array('del_flg' => Constant::DISABLED),
                array('rank' => 'ASC')
            );
        foreach ($Delivs as $Deliv) {
            if ($Deliv->getId() != $Delivery->getId()) {
                $Deliv->setRank($rank);
                $rank++;
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

        $app->addSuccess('admin.delete.complete', 'admin');

        return $app->redirect($app->url('admin_setting_shop_delivery'));
    }

    /**
     * @Method("POST")
     * @Route("/{_admin}/setting/shop/delivery/rank/move", name="admin_setting_shop_delivery_rank_move")
     */
    public function moveRank(Application $app, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $ranks = $request->request->all();
        foreach ($ranks as $deliveryId => $rank) {
            $Delivery = $this->deliveryRepository
                ->find($deliveryId);
            $Delivery->setRank($rank);
        }
        $this->entityManager->flush();

        return true;
    }
}

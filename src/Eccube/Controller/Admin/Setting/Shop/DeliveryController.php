<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;

class DeliveryController extends AbstractController
{
    private $main_title;
    private $sub_title;

    public $form;

    public function __construct()
    {
    }

    public function index(Application $app, Request $request)
    {
        $Deliveries = $app['eccube.repository.delivery']
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
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_INDEX_COMPLETE, $event);

        return $app->render('Setting/Shop/delivery.twig', array(
            'Deliveries' => $Deliveries,
        ));
    }

    public function edit(Application $app, Request $request, $id = 0)
    {
        /* @var $Delivery \Eccube\Entity\Delivery */
        $Delivery = $app['eccube.repository.delivery']
            ->findOrCreate($id);

        // FormType: DeliveryFeeの生成
        $Prefs = $app['eccube.repository.master.pref']
            ->findAll();

        foreach ($Prefs as $Pref) {
            $DeliveryFee = $app['eccube.repository.delivery_fee']
                ->findOrCreate(array(
                    'Delivery' => $Delivery,
                    'Pref' => $Pref,
                ));
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

        $builder = $app['form.factory']
            ->createBuilder('delivery', $Delivery);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Delivery' => $Delivery,
                'Prefs' => $Prefs,
                'DeliveryFees' => $DeliveryFees,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        // 支払方法をセット
        $Payments = array();
        foreach ($Delivery->getPaymentOptions() as $PaymentOption) {
            $Payments[] = $PaymentOption->getPayment();
        }

        $form['delivery_times']->setData($Delivery->getDeliveryTimes());
        $form['payments']->setData($Payments);

        // 登録ボタン押下
        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $DeliveryData = $form->getData();

                // 配送時間の登録
                $DeliveryTimes = $form['delivery_times']->getData();
                foreach ($DeliveryTimes as $DeliveryTime) {
                    if (is_null($DeliveryTime->getDeliveryTime())) {
                        $Delivery->removeDeliveryTime($DeliveryTime);
                        $app['orm.em']->remove($DeliveryTime);
                    }
                }

                // お支払いの登録
                $PaymentOptions = $app['eccube.repository.payment_option']
                    ->findBy(array('delivery_id' => $id));
                // 消す
                foreach ($PaymentOptions as $PaymentOption) {
                    $DeliveryData->removePaymentOption($PaymentOption);
                    $app['orm.em']->remove($PaymentOption);
                }
                $app['orm.em']->persist($DeliveryData);
                $app['orm.em']->flush();

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
                    $app['orm.em']->persist($DeliveryData);
                }

                $app['orm.em']->persist($DeliveryData);

                $app['orm.em']->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Delivery' => $Delivery,
                        'Prefs' => $Prefs,
                        'DeliveryFees' => $DeliveryFees,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_EDIT_COMPLETE, $event);

                $app->addSuccess('admin.register.complete', 'admin');

                return $app->redirect($app->url('admin_setting_shop_delivery'));
            }
        }
        return $app->render('Setting/Shop/delivery_edit.twig', array(
            'form' => $form->createView(),
            'delivery_id' => $id,
        ));
    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $repo = $app['eccube.repository.delivery'];
        $Delivery = $repo->find($id);
        if (!$Delivery) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_setting_shop_delivery'));
        }

        $Delivery
            ->setDelFlg(Constant::ENABLED)
            ->setRank(0);

        $app['orm.em']->persist($Delivery);

        $rank = 1;
        $Delivs = $repo
            ->findBy(
                array('del_flg' => Constant::DISABLED),
                array('rank' => 'ASC')
            );
        foreach ($Delivs as $Deliv) {
            if ($Deliv->getId() != $id) {
                $Deliv->setRank($rank);
                $rank++;
            }
        }

        $app['orm.em']->flush();

        $event = new EventArgs(
            array(
                'Delivs' => $Delivs,
                'Delivery' => $Delivery,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_DELIVERY_DELETE_COMPLETE, $event);

        $app->addSuccess('admin.delete.complete', 'admin');

        return $app->redirect($app->url('admin_setting_shop_delivery'));
    }

    public function moveRank(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $ranks = $request->request->all();
            foreach ($ranks as $deliveryId => $rank) {
                $Delivery = $app['eccube.repository.delivery']
                    ->find($deliveryId);
                $Delivery->setRank($rank);
                $app['orm.em']->persist($Delivery);
            }
            $app['orm.em']->flush();
        }

        return true;
    }
}

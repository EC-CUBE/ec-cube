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

use Eccube\Application;
use Eccube\Form\Type\ShippingMultiType;
use Symfony\Component\HttpFoundation\Request;

class ShoppingController extends AbstractController
{
    /** @var \Eccube\Application */
    protected $app;
    /** @var \Eccube\Service\CartService */
    protected $cartService;
    /** @var \Eccube\Repository\OrderRepository */
    protected $orderRepository;
    /** @var \Eccube\Service\OrderService */
    protected $orderService;
    /** @var \Symfony\Component\Form\Form */
    protected $form;

    public function test(Application $app)
    {
        // カートに商品追加(テスト用)
        $app['eccube.service.cart']->clear();
        $app['eccube.service.cart']->addProduct(9);
        $app['eccube.service.cart']->addProduct(9);
        $app['eccube.service.cart']->addProduct(10);
        $app['eccube.service.cart']->addProduct(10);
        $app['eccube.service.cart']->addProduct(2);
        $app['eccube.service.cart']->lock();
        $app['eccube.service.cart']->save();

        return $app->redirect($app['url_generator']->generate('shopping'));
    }

    protected function init($app)
    {
        $this->app = $app;
        $this->cartService = $app['eccube.service.cart'];
        $this->orderRepository = $app['eccube.repository.order'];
        $this->orderService = $app['eccube.service.order'];
        $this->form = $app['form.factory']
            ->createBuilder('shopping')
            ->getForm();
        // todo
        $app['orm.em']->getFilters()->disable('soft_delete');
    }

    public function index(Application $app)
    {
        // TODO delete_flagではなく, 注文の確定フラグをみるようにする.
        $app['orm.em']->getFilters()->disable('soft_delete');

        // カートの変更チェック
        if (!$app['eccube.service.cart']->isLocked()) {
            return $app->redirect($app['url_generator']->generate('cart'));
        }

        // 受注データを取得.
        $preOrderId = $app['eccube.service.cart']->getPreOrderId();
        $Order = $app['eccube.repository.order']->findOneBy(array('id' => $preOrderId)); // TODO order_temp_idにする.

        // 初回アクセス(受注データがない)の場合は, 受注データを生成
        if (is_null($Order)) {
            // 未ログインの場合は, ログイン画面へリダイレクト.
            if (!$app['security']->isGranted('ROLE_USER')) {
                return $app->redirect($app['url_generator']->generate('shopping_login'));
            }
            $Order = $app['eccube.service.order']->registerPreOrderFromCartItems(
                $app['eccube.service.cart']->getCart()->getCartItems(),
                $app['user']
            );
            $app['eccube.service.cart']->setPreOrderId($Order->getId());
            $app['eccube.service.cart']->save();
        }

        // 受注関連情報を最新状態に更新
        $app['orm.em']->refresh($Order);

        $form = $app['form.factory']
            ->createBuilder('shopping')
            ->getForm();

        // 配送業者選択
        $deliveries = $this->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());
        $form->add('delivery', 'entity', array(
            'class' => 'Eccube\Entity\Deliv',
            'property' => 'name',
            'choices' => $deliveries,
            'data' => $Order->getDeliv()));

        // 支払い方法選択
        $paymentOptions = $Order->getDeliv()->getPaymentOptions();
        $payments = array();
        foreach ($paymentOptions as $paymentOption) {
            $payments[] = $paymentOption->getPayment();
        }
        $form->add('payment', 'entity', array(
            'class' => 'Eccube\Entity\Payment',
            'property' => 'method',
            'choices' => $payments,
            'data' => $Order->getPayment()));

        $title = "ご注文内容の確認";

        return $app['view']->render(
            'Shopping/index.twig',
            array(
                'form' => $form->createView(),
                'title' => $title,
                'order' => $Order)
        );
    }

    // 購入処理
    public function confirm(Application $app)
    {
        $this->init($app);

        if ('POST' === $app['request']->getMethod()) {
            $this->form->handleRequest($app['request']);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                /** @var $Order \Eccube\Entity\Order */
                $Order = $this->orderRepository->find($this->cartService->getPreOrderId());
                $Order->setMessage($data['message']);
                $this->orderService->commit($Order);
                $this->cartService->clear()->save();

                return $app->redirect($app['url_generator']->generate('shopping_complete'));
            }
        }

        // todo エラーハンドリング
        return $app->redirect($app['url_generator']->generate('cart'));
    }

    // 購入完了画面表示
    public function complete(Application $app)
    {
        $title = "ご購入完了";
        $baseInfo = $app['eccube.repository.base_info']->find(1);

        return $app['view']->render(
            'Shopping/complete.twig',
            array(
                'title' => $title,
                'baseInfo' => $baseInfo
            )
        );
    }

    // 配送業者設定
    public function delivery(Application $app)
    {
        $this->init($app);

        if ('POST' === $app['request']->getMethod()) {
            $this->form->handleRequest($app['request']);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                /** @var $Order \Eccube\Entity\Order */
                $Order = $this->orderRepository->find($this->cartService->getPreOrderId());
                // 配送業者をセット
                $delivery = $data['delivery'];
                $deliveryFees = $delivery->getDelivFees();
                $Order->setDeliv($delivery);
                $Order->setDelivFee($deliveryFees[0]->getFee());
                // 支払い情報をセット
                $paymentOptions = $delivery->getPaymentOptions();
                $payment = $paymentOptions[0]->getPayment();
                ;
                $Order->setPayment($payment);
                $Order->setPaymentMethod($payment->getMethod());
                $Order->setCharge($payment->getCharge());
                $app['orm.em']->persist($Order);
                $app['orm.em']->flush();
            }
        }

        return $app->redirect($app['url_generator']->generate('shopping'));
    }

    // 支払い方法設定
    public function payment(Application $app)
    {
        $this->init($app);

        if ('POST' === $app['request']->getMethod()) {
            $this->form->handleRequest($app['request']);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                /** @var $Order \Eccube\Entity\Order */
                $Order = $this->orderRepository->find($this->cartService->getPreOrderId());
                // 支払い情報をセット
                $payment = $data['payment'];
                $Order->setPayment($payment);
                $Order->setPaymentMethod($payment->getMethod());
                $Order->setCharge($payment->getCharge());
                $app['orm.em']->persist($Order);
                $app['orm.em']->flush();
            }
        }

        return $app->redirect($app['url_generator']->generate('shopping'));
    }

    // ポイント設定
    public function point(Application $app)
    {
        $this->init($app);

        /** @var $Order \Eccube\Entity\Order */
        $Order = $this->orderRepository->find($this->cartService->getPreOrderId());
        $point = $Order->getUsePoint();
        $pointFlg = $point > 0 ? 1 : 0;

        $form = $app['form.factory']->createBuilder()
            ->add('point_flg', 'choice', array(
                'required' => true,
                'choices'  => array(0 => '使用しない', 1 => '使用する'),
                'expanded' => true,
                'data' => $pointFlg))
            ->add('point', 'integer', array(
                'required' => true,
                'data' => $point))
            ->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $pointFlg = $data['point_flg'];
                $point = $data['point'];
                if ($pointFlg == 0) {
                    $point = 0;
                }
                $Order->setUsePoint($point);
                $app['orm.em']->persist($Order);
                $app['orm.em']->flush();

                return $app->redirect($app['url_generator']->generate('shopping'));
            }
        }

        return $app['view']->render(
            'Shopping/point.twig',
            array(
                'title' => 'ポイント設定',
                'order' => $Order,
                'form' => $form->createView()
            )
        );
    }

    // お届け先設定
    public function shipping(Application $app, Request $request)
    {
        $this->init($app);

        $Order = $this->orderRepository->find($this->cartService->getPreOrderId());
        $Shipping = $app['orm.em']
            ->getRepository('Eccube\Entity\Shipping')
            ->findOneBy(array('Order' => $Order));

        $addresses = array();
        if ($app['security']->isGranted('ROLE_USER')) {
            $OtherDelivs = $app['user']->getOtherDelivs();
            foreach ($OtherDelivs as $OtherDeliv) {
                $addresses[$OtherDeliv->getId()] = $OtherDeliv;
            }
        }

        $form = $app['form.factory']->createBuilder()
            ->add('addresses', 'choice', array(
                'choices' => $addresses,
                'expanded' => true,
                'data' => 0
            ))
            ->add('address', 'other_deliv', array(
                'data_class' => 'Eccube\Entity\Shipping'
            ))
            ->getForm();

        $form->get('address')->setData($Shipping);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ('select_address' === $request->get('mode')) {
                    $otherDelivId = $data['addresses'];
                    $OtherDeliv = $app['orm.em']
                        ->getRepository('Eccube\Entity\OtherDeliv')
                        ->find($otherDelivId);
                    $Shipping
                        ->setName01($OtherDeliv->getName01())
                        ->setName02($OtherDeliv->getName02())
                        ->setKana01($OtherDeliv->getKana02())
                        ->setKana02($OtherDeliv->getKana02())
                        ->setCompanyName($OtherDeliv->getCompanyName())
                        ->setTel01($OtherDeliv->getTel01())
                        ->setTel02($OtherDeliv->getTel02())
                        ->setTel03($OtherDeliv->getTel03())
                        ->setFax01($OtherDeliv->getFax01())
                        ->setFax02($OtherDeliv->getFax02())
                        ->setFax03($OtherDeliv->getFax03())
                        ->setZip01($OtherDeliv->getZip01())
                        ->setZip02($OtherDeliv->getZip02())
                        ->setPref($OtherDeliv->getPref())
                        ->setAddr01($OtherDeliv->getAddr01())
                        ->setAddr02($OtherDeliv->getAddr02());
                }

                // 配送先を更新
                $app['orm.em']->persist($Shipping);
                $app['orm.em']->flush();

                return $app->redirect($app['url_generator']->generate('shopping'));
            }
        }

        return $app['view']->render(
            'Shopping/shipping.twig',
            array(
                'form' => $form->createView(),
                'title' => 'お届け先設定',
                'Order' => $Order,
            )
        );
    }

    // 複数配送
    public function shippingMultiple(Application $app)
    {
        $this->init($app);

        $Order = $this->orderRepository->find($this->cartService->getPreOrderId());

        $Products = array();
        $data = array();
        foreach ($Order->getOrderDetails() as $OrderDetail) {
            /** @var  $OrderDetail \Eccube\Entity\OrderDetail */
            $max = $OrderDetail->getQuantity();
            for ($i = 0; $i < $max; $i++) {
                $productClassId =  $OrderDetail->getProductClass()->getId();
                $data[] = array(
                    'product_class_id' => $productClassId,
                );
                $Products[$productClassId] = $OrderDetail->getProduct();
                $ProductClasses[$productClassId] = $OrderDetail->getProductClass();
            }
        }

        $builder = $app['form.factory']->createBuilder();
        $builder->add('shipping_multi', 'collection', array(
            'type' => new ShippingMultiType($app),
            'options' => array(),
            'data' => $data
        ));

        $form = $builder->getForm();
        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $Shippings = $Order->getShippings();
                foreach ($Shippings as $Shipping) {
                    $ShipmentItems = $Shipping->getShipmentItems();
                    foreach ($ShipmentItems as $ShipmentItem) {
                        $app['orm.em']->remove($ShipmentItem);
                    }
                    $app['orm.em']->remove($Shipping);
                    $app['orm.em']->flush();
                }

                $data = $form->getData();
                $arrayData = array();
                foreach ($data['shipping_multi'] as $line) {
                    $productClassId = $line['product_class_id'];
                    $ProductClass = $app['orm.em']->getRepository('Eccube\Entity\ProductClass')->find($productClassId);
                    $OtherDeliv = $line['other_deliv'];
                    $quantity = $line['quantity'];
                    if ($quantity < 1) {
                        continue;
                    }
                    $arrayData[$OtherDeliv->getId()]['other_deliv'] = $OtherDeliv;
                    $arrayData[$OtherDeliv->getId()]['shipment_items'][] = array(
                        'ProductClass' => $ProductClass,
                        'quantity' => $quantity
                    );
                }
                $i = 1;
                foreach ($arrayData as $key => $values) {
                    $OtherDeliv = $values['other_deliv'];
                    $ShipmentItems = $values['shipment_items'];
                    $Shipping = $this->newShipping($OtherDeliv);
                    $Shipping->setShippingId($i++)
                        ->setOrderId($Order->getId())
                        ->setOrder($Order);
                    $app['orm.em']->persist($Shipping);
                    foreach ($ShipmentItems as $item) {
                        $ProductClass = $item['ProductClass'];
                        $Product = $ProductClass->getProduct();
                        $quantity = $item['quantity'];
                        $ShipmentItem = new \Eccube\Entity\ShipmentItem();
                        $ShipmentItem->setShippingId($Shipping->getShippingId());
                        $ShipmentItem->setShipping($Shipping)
                            ->setOrderId($Order->getId())
                            ->setProductClassId($ProductClass->getId())
                            ->setProductClass($ProductClass)
                            ->setProductName($Product->getName())
                            ->setProductCode($ProductClass->getCode())
                            ->setClasscategoryName1($ProductClass->getClassCategory1()->getName())
                            ->setClasscategoryName2($ProductClass->getClassCategory2()->getName())
                            ->setPrice($ProductClass->getPrice02())
                            ->setQuantity($quantity);
                        $app['orm.em']->persist($ShipmentItem);
                    }
                }
                $app['orm.em']->flush();

                return $app->redirect($app['url_generator']->generate('shopping'));
            }
        }

        return $app['view']->render(
            'Shopping/shipping_multiple.twig',
            array(
                'form'  => $form->createView(),
                'Products' => $Products,
                'ProductClassess' => $ProductClasses,
                'title' => 'お届け先設定(複数配送)',
            )
        );
    }

    public function login(Application $app)
    {
        if (!$app['eccube.service.cart']->isLocked()) {
            //return $app->redirect($app['url_generator']->generate('cart'));
        }

        if ($app['security']->isGranted('ROLE_USER')) {
            return $app->redirect($app['url_generator']->generate('shopping'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createNamedBuilder('', 'customer_login')
            ->getForm();

        return $app['view']->render('Shopping/login.twig', array(
            'title' => 'ログイン',
            'error' => $app['security.last_error']($app['request']),
            'form'  => $form->createView(),
        ));
    }

    public function nonmember(Application $app)
    {
        if ($this->cartChanged($app)) {
            $app->redirect($app['url_generator']->generate('shopping_error'));
        }

        $builder = $app['form.factory']->createBuilder('nonmember');
        $form = $builder->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $Customer = new \Eccube\Entity\Customer();
                $Customer->setName01($data['name01'])
                    ->setName02($data['name02'])
                    ->setKana01($data['kana01'])
                    ->setKana02($data['kana02'])
                    ->setCompanyName($data['company_name'])
                    ->setEmail($data['email'])
                    ->setTel01($data['tel01'])
                    ->setTel02($data['tel02'])
                    ->setTel03($data['tel03'])
                    ->setFax01($data['fax01'])
                    ->setFax02($data['fax02'])
                    ->setFax03($data['fax03'])
                    ->setZip01($data['zip01'])
                    ->setZip02($data['zip02'])
                    ->setPref($data['pref'])
                    ->setAddr01($data['addr01'])
                    ->setAddr02($data['addr02'])
                    ->setSex($data['sex'])
                    ->setBirth($data['birth'])
                    ->setJob($data['job']);
                // 受注関連情報を取得
                $preOrderId = $app['eccube.service.cart']->getPreOrderId();
                $Order = null;
                if (!is_null($preOrderId)) {
                    $Order = $app['eccube.repository.order']->find($preOrderId);
                }
                // 初回アクセスの場合は受注データを作成
                if (is_null($Order)) {
                    $Order = $app['eccube.service.order']->registerPreOrderFromCartItems(
                        $app['eccube.service.cart']->getCart()->getCartItems(),
                        $Customer
                    );
                    $app['eccube.service.cart']->setPreOrderId($Order->getId());
                    $app['eccube.service.cart']->save();
                }

                return $app->redirect($app['url_generator']->generate('shopping'));
            }
        }

        return $app['view']->render('Shopping/nonmember.twig', array(
            'form'  => $form->createView(),
            'title' => '非会員購入',
        ));
    }

    protected function isLoggedIn($app)
    {
        return $app['security']->isGranted('ROLE_USER');
    }

    protected function cartChanged($app)
    {
        return !$app['eccube.service.cart']->isLocked() === true;
    }

    // todo リファクタ
    private function newShipping($OtherDeliv)
    {
        $Shipping = new \Eccube\Entity\Shipping();
        $Shipping
            ->setName01($OtherDeliv->getName01())
            ->setName02($OtherDeliv->getName02())
            ->setKana01($OtherDeliv->getKana02())
            ->setKana02($OtherDeliv->getKana02())
            ->setCompanyName($OtherDeliv->getCompanyName())
            ->setTel01($OtherDeliv->getTel01())
            ->setTel02($OtherDeliv->getTel02())
            ->setTel03($OtherDeliv->getTel03())
            ->setFax01($OtherDeliv->getFax01())
            ->setFax02($OtherDeliv->getFax02())
            ->setFax03($OtherDeliv->getFax03())
            ->setZip01($OtherDeliv->getZip01())
            ->setZip02($OtherDeliv->getZip02())
            ->setPref($OtherDeliv->getPref())
            ->setAddr01($OtherDeliv->getAddr01())
            ->setAddr02($OtherDeliv->getAddr02())
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime())
            ->setDelFlg(0);

        return $Shipping;
    }

    // todo リファクタ
    private function findDeliveriesFromOrderDetails($app, $details)
    {
        $productTypeIds = array();
        foreach ($details as $detail) {
            $productTypeIds[] = $detail->getProductClass()->getProductType()->getId();
        }
        $productTypeIds = array_unique($productTypeIds);
        $qb = $app['orm.em']->createQueryBuilder();
        $deliveries = $qb->select("d")
            ->from("\\Eccube\\Entity\\Deliv", "d")
            ->where($qb->expr()->in('d.product_type_id', $productTypeIds))
            ->andWhere("d.del_flg = 0")
            ->orderBy("d.rank", "ASC")
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return $deliveries;
    }
}

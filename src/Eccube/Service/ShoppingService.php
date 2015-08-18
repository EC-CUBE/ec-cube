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

namespace Eccube\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Util\Str;

class ShoppingService
{
    /** @var \Eccube\Application */
    public $app;

    /** @var \Eccube\Service\CartService */
    protected $cartService;

    /** @var \Eccube\Service\OrderService */
    protected $orderService;

    /** @var \Eccube\Entity\BaseInfo */
    protected $BaseInfo;

    protected $em;

    public function __construct(Application $app, $cartService, $orderService)
    {
        $this->app = $app;
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->BaseInfo = $app['eccube.repository.base_info']->get();
    }

    /**
     * セッションにセットされた受注情報を取得
     *
     * @return null|object
     */
    public function getOrder()
    {

        // 受注データを取得
        $preOrderId = $this->cartService->getPreOrderId();
        $Order = $this->app['eccube.repository.order']->findOneBy(array(
            'pre_order_id' => $preOrderId,
            'OrderStatus' => $this->app['config']['order_processing']
        ));

        return $Order;

    }

    /**
     * 受注情報を作成
     *
     * @param $Customer
     * @return \Eccube\Entity\Order
     */
    public function createOrder($Customer)
    {
        // ランダムなpre_order_idを作成
        $preOrderId = sha1(Str::random(32));

        // 受注情報、受注明細情報、お届け先情報、配送商品情報を作成
        $Order = $this->registerPreOrder(
            $Customer,
            $preOrderId);

        $this->cartService->setPreOrderId($preOrderId);
        $this->cartService->save();

        return $Order;
    }

    /**
     * 仮受注情報作成
     *
     * @param $Customer
     * @param $preOrderId
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function registerPreOrder(Customer $Customer, $preOrderId)
    {

        $this->em = $this->app['orm.em'];

        // 受注情報を作成
        $Order = $this->getNewOrder($Customer);
        $Order->setPreOrderId($preOrderId);

        $this->em->persist($Order);

        // 配送業者情報を取得
        $deliveries = $this->getDeliveries();

        // お届け先情報を取得
        $Order = $this->getNewShipping($Order, $Customer, $deliveries);


        // 受注明細情報、配送商品情報を取得

        $subTotal = 0;
        $tax = 0;
        $totalQuantity = 0;
        $productTypes = array();
        $productDeliveryFeeTotal = 0;

        $optionProductDeliveryFee = $this->BaseInfo->getOptionProductDeliveryFee();

        // 受注詳細, 配送商品
        foreach ($this->cartService->getCart()->getCartItems() as $item) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getObject();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();

            $quantity = $item->getQuantity();
            $productTypes[] = $ProductClass->getProductType();
            $totalQuantity += $quantity;
            if (!is_null($optionProductDeliveryFee)) {
                // 商品ごとの配送料が設定
                if (!is_null($ProductClass->getDeliveryFee())) {
                    $productDeliveryFeeTotal += $ProductClass->getDeliveryFee() * $quantity;
                }
            }

            // 受注詳細
            $OrderDetail = $this->newOrderDetail($Product, $ProductClass, $quantity);
            $OrderDetail->setOrder($Order);

            $Order->addOrderDetail($OrderDetail);
            $this->em->persist($OrderDetail);

            // 小計
            $subTotal += $ProductClass->getPrice02IncTax() * $quantity;
            // 消費税のみの小計
            $tax += ($OrderDetail->getPriceIncTax() - $OrderDetail->getPrice()) * $quantity;

            // 配送商品
            $ShipmentItem = new \Eccube\Entity\ShipmentItem();
            $Shippings = $Order->getShippings();
            $Shipping = $Shippings[0];
            $ShipmentItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProductClass($ProductClass)
                ->setProduct($Product)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($quantity);

            $ClassCategory1 = $ProductClass->getClassCategory1();
            if (!is_null($ClassCategory1)) {
                $ShipmentItem->setClasscategoryName1($ClassCategory1->getName());
                $ShipmentItem->setClassName1($ClassCategory1->getClassName()->getName());
            }
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if (!is_null($ClassCategory2)) {
                $ShipmentItem->setClasscategoryName2($ClassCategory2->getName());
                $ShipmentItem->setClassName2($ClassCategory2->getClassName()->getName());
            }
            $Shipping->addShipmentItem($ShipmentItem);
            $this->em->persist($ShipmentItem);
        }

        // 初期選択の配送業者をセット
        $qb = $this->em->createQueryBuilder();
        $delivery = $qb->select("d")
            ->from("\Eccube\Entity\Delivery", "d")
            ->where($qb->expr()->in('d.ProductType', ':productTypes'))
            ->setParameter('productTypes', $productTypes)
            ->andWhere("d.del_flg = :delFlg")
            ->setParameter('delFlg', Constant::DISABLED)
            ->orderBy("d.rank", "ASC")
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        // 配送料金の設定
        $deliveryFee = $this->app['eccube.repository.delivery_fee']->findOneBy(array('Delivery' => $delivery, 'Pref' => $Shipping->getPref()));
        $Shipping->setDelivery($delivery);
        // $Shipping->setDeliveryFee($deliveryFee + $productDeliveryFeeTotal);
        $Shipping->setDeliveryFee($deliveryFee);
        $Shipping->setShippingDeliveryFee($deliveryFee->getFee() + $productDeliveryFeeTotal);

        $Order->setDeliveryFeeTotal($deliveryFee->getFee() + $productDeliveryFeeTotal);
        // 配送料無料条件(合計金額)
        $deliveryFreeAmount = $this->BaseInfo->getDeliveryFreeAmount();
        if (!is_null($deliveryFreeAmount)) {
            // 合計金額が設定金額以上であれば送料無料
            if ($subTotal >= $deliveryFreeAmount) {
                $Order->setDeliveryFeeTotal(0);
            }
        }

        // 配送料無料条件(合計数量)
        $deliveryFreeQuantity = $this->BaseInfo->getDeliveryFreeQuantity();
        if (!is_null($deliveryFreeQuantity)) {
            // 合計数量が設定数量以上であれば送料無料
            if ($totalQuantity >= $deliveryFreeQuantity) {
                $Order->setDeliveryFeeTotal(0);
            }
        }

        // 初期選択の支払い方法をセット
        $paymentOptions = $delivery->getPaymentOptions();
        $payments = $this->getPayments($paymentOptions, $subTotal);

        if (count($payments) > 0) {
            $payment = $payments[0];
            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setCharge($payment->getCharge());
        } else {
            $Order->setCharge(0);
        }

        $Order->setTax($tax);

        $total = $subTotal + $Order->getCharge() + $Order->getDeliveryFeeTotal();

        $Order->setTotal($total);
        $Order->setSubTotal($subTotal);
        $Order->setPaymentTotal($total);

        $this->em->flush();

        return $Order;

    }

    /**
     * 受注情報を作成
     * @param $Customer
     * @return \Eccube\Entity\Order
     */
    public function getNewOrder(Customer $Customer)
    {
        $Order = $this->newOrder();
        $this->copyToOrderFromCustomer($Order, $Customer);

        return $Order;
    }


    /**
     * 受注情報を作成
     * @return \Eccube\Entity\Order
     */
    public function newOrder()
    {
        $Order = new \Eccube\Entity\Order();
        $Order->setDiscount(0)
            ->setSubtotal(0)
            ->setTotal(0)
            ->setPaymentTotal(0)
            ->setCharge(0)
            ->setTax(0)
            ->setOrderStatus($this->app['eccube.repository.order_status']->find($this->app['config']['order_processing']))
            ->setDelFlg(Constant::DISABLED);

        return $Order;
    }

    public function newOrderDetail($Product, $ProductClass, $quantity)
    {
        $OrderDetail = new \Eccube\Entity\OrderDetail();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule($Product, $ProductClass);
        $OrderDetail->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity($quantity)
            ->setTaxRule($TaxRule->getCalcRule()->getId())
            ->setTaxRate($TaxRule->getTaxRate());

        $ClassCategory1 = $ProductClass->getClassCategory1();
        if (!is_null($ClassCategory1)) {
            $OrderDetail->setClasscategoryName1($ClassCategory1->getName());
            $OrderDetail->setClassName1($ClassCategory1->getClassName()->getName());
        }
        $ClassCategory2 = $ProductClass->getClassCategory2();
        if (!is_null($ClassCategory2)) {
            $OrderDetail->setClasscategoryName2($ClassCategory2->getName());
            $OrderDetail->setClassName2($ClassCategory2->getClassName()->getName());
        }

        return $OrderDetail;
    }

    /**
     * 受注情報を作成
     *
     * @param \Eccube\Entity\Order $Order
     * @param \Eccube\Entity\Customer|null $Customer
     * @return \Eccube\Entity\Order
     */
    public function copyToOrderFromCustomer(Order $Order, Customer $Customer = null)
    {
        if (is_null($Customer)) {
            return $Order;
        }

        if ($Customer->getId()) {
            $Order->setCustomer($Customer);
        }
        $Order
            ->setName01($Customer->getName01())
            ->setName02($Customer->getName02())
            ->setKana01($Customer->getKana01())
            ->setKana02($Customer->getKana02())
            ->setCompanyName($Customer->getCompanyName())
            ->setEmail($Customer->getEmail())
            ->setTel01($Customer->getTel01())
            ->setTel02($Customer->getTel02())
            ->setTel03($Customer->getTel03())
            ->setFax01($Customer->getFax01())
            ->setFax02($Customer->getFax02())
            ->setFax03($Customer->getFax03())
            ->setZip01($Customer->getZip01())
            ->setZip02($Customer->getZip02())
            ->setZipCode($Customer->getZip01() . $Customer->getZip02())
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02())
            ->setSex($Customer->getSex())
            ->setBirth($Customer->getBirth())
            ->setJob($Customer->getJob());

        return $Order;
    }


    /**
     * 配送業者情報を取得
     *
     * @return array
     */
    public function getDeliveries()
    {

        // カートに保持されている商品種別を取得
        $productTypes = $this->cartService->getProductTypes();

        // 商品種別に紐づく配送業者を取得
        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($productTypes);

        if ($this->BaseInfo->getOptionMultipleShipping() == Constant::ENABLED) {
            // 複数配送対応

            // 支払方法を取得
            $payments = $this->app['eccube.repository.payment']->findAllowedPayment($deliveries);

            if (count($productTypes) > 1) {
                // 商品種別が複数ある場合、配送対象となる配送業者を取得
                $deliveries = $this->app['eccube.repository.delivery']->findAllowedDeliveries($productTypes, $payments);
            }

        }

        return $deliveries;

    }


    /**
     * お届け先情報を作成
     *
     * @param Order $Order
     * @param Customer $Customer
     * @param $deliveries
     * @return Order
     */
    public function getNewShipping(Order $Order, Customer $Customer, $deliveries)
    {

        if ($this->BaseInfo->getOptionMultipleShipping() == Constant::ENABLED) {
            // 複数配送対応
            foreach ($deliveries as $Delivery) {
                $Shipping = new Shipping();

                $this->copyToShippingFromCustomer($Shipping, $Customer)
                    ->setOrder($Order)
                    ->setDelFlg(Constant::DISABLED);

                $this->em->persist($Shipping);

                $Order->addShipping($Shipping);
            }
        } else {
            $Shipping = new Shipping();

            $this->copyToShippingFromCustomer($Shipping, $Customer)
                ->setOrder($Order)
                ->setDelFlg(Constant::DISABLED);

            $this->em->persist($Shipping);

            $Order->addShipping($Shipping);

        }

        return $Order;

    }

    /**
     * お届け先情報を作成
     *
     * @param \Eccube\Entity\Shipping $Shipping
     * @param \Eccube\Entity\Customer|null $Customer
     * @return \Eccube\Entity\Shipping
     */
    public function copyToShippingFromCustomer(\Eccube\Entity\Shipping $Shipping, \Eccube\Entity\Customer $Customer = null)
    {
        if (is_null($Customer)) {
            return $Shipping;
        }
        $Shipping
            ->setName01($Customer->getName01())
            ->setName02($Customer->getName02())
            ->setKana01($Customer->getKana01())
            ->setKana02($Customer->getKana02())
            ->setCompanyName($Customer->getCompanyName())
            ->setTel01($Customer->getTel01())
            ->setTel02($Customer->getTel02())
            ->setTel03($Customer->getTel03())
            ->setFax01($Customer->getFax01())
            ->setFax02($Customer->getFax02())
            ->setFax03($Customer->getFax03())
            ->setZip01($Customer->getZip01())
            ->setZip02($Customer->getZip02())
            ->setZipCode($Customer->getZip01() . $Customer->getZip02())
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02());

        return $Shipping;
    }

}

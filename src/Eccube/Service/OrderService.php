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

class OrderService
{
    /** @var \Eccube\Application */
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function newOrder()
    {
        $Order = new \Eccube\Entity\Order();
        $Order->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime())
            ->setDiscount(0)
            ->setUsePoint(0)
            ->setAddPoint(0)
            ->setBirthPoint(0)
            ->setSubtotal(0)
            ->setTotal(0)
            ->setPaymentTotal(0)
            ->setCharge(0)
            ->setTax(0)
            ->setDelivFee(0)
            ->setOrderStatus($this->app['eccube.repository.order_status']->find(1)) // todo
            ->setDelFlg(0); // todo

        return $Order;
    }

    public function newOrderDetail($Product, $ProductClass, $quantity)
    {
        $OrderDetail = new \Eccube\Entity\OrderDetail();
        $OrderDetail->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity($quantity)
            ->setPointRate(0) // todo
            ->setTaxRule(0) // todo
            ->setTaxRate(0); // todo
        $ClassCategory1 = $ProductClass->getClassCategory1();
        if (!is_null($ClassCategory1)) {
            $OrderDetail->setClasscategoryName1($ClassCategory1->getName());
        }
        $ClassCategory2 = $ProductClass->getClassCategory2();
        if (!is_null($ClassCategory2)) {
            $OrderDetail->setClasscategoryName1($ClassCategory2->getName());
        }

        return $OrderDetail;
    }

     public function copyToOrderFromCustomer(\Eccube\Entity\Order $Order, \Eccube\Entity\Customer $Customer = null)
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
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02())
            ->setSex($Customer->getSex())
            ->setBirth($Customer->getBirth())
            ->setJob($Customer->getJob());

        return $Order;
     }

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
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02());

            return $Shipping;
        }

        public function registerPreOrderFromCartItems($cartItems, \Eccube\Entity\Customer $Customer = null)
        {
            // 受注
            $Order = $this->newOrder();
            $this->copyToOrderFromCustomer($Order, $Customer);

            $this->app['orm.em']->persist($Order);
            $this->app['orm.em']->flush();

            // 配送先
            $Shipping = new \Eccube\Entity\Shipping();

            $this->copyToShippingFromCustomer($Shipping, $Customer)
            ->setShippingId(1)
            ->setOrderId($Order->getId())
            ->setOrder($Order)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime())
            ->setDelFlg(0);
            $this->app['orm.em']->persist($Shipping);

            $Order->addShipping($Shipping);

            $point = 0;
            $subTotal = 0;
            $productTypeIds = array();

            // 受注詳細, 配送商品
            foreach ($cartItems as $item) {
                /* @var $ProductClass \Eccube\Entity\ProductClass */
                $ProductClass = $item->getObject();
                /* @var $Product \Eccube\Entity\Product */
                $Product = $ProductClass->getProduct();

                $quantity = $item->getQuantity();
                $productTypeIds[] = $ProductClass->getProductType()->getId();

                // 受注詳細
                $OrderDetail = $this->newOrderDetail($Product, $ProductClass, $quantity);
                $OrderDetail->setOrder($Order);

                $Order->addOrderDetail($OrderDetail);
                $this->app['orm.em']->persist($OrderDetail);

                // 小計
                $subTotal += $ProductClass->getPrice02IncTax();

                // 加算ポイント
                $point += $ProductClass->getPoint();

                // 配送商品
                $ShipmentItem = new \Eccube\Entity\ShipmentItem();
                $ShipmentItem->setShippingId($Shipping->getShippingId());
                $ShipmentItem->setShipping($Shipping)
                ->setOrderId($Order->getId())
                ->setProductClassId($ProductClass->getId())
                ->setProductClass($ProductClass)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($quantity);

                $ClassCategory1 = $ProductClass->getClassCategory1();
                if (!is_null($ClassCategory1)) {
                    $ShipmentItem->setClasscategoryName1($ClassCategory1->getName());
                }
                $ClassCategory2 = $ProductClass->getClassCategory2();
                if (!is_null($ClassCategory2)) {
                    $ShipmentItem->setClasscategoryName1($ClassCategory2->getName());
                }
                $Shipping->addShipmentItem($ShipmentItem);
                $this->app['orm.em']->persist($ShipmentItem);
            }

            // 初期選択の配送業者をセット
            $productTypeIds = array_unique($productTypeIds);
            $qb = $this->app['orm.em']->createQueryBuilder();
            $delivery = $qb->select("d")
            ->from("\\Eccube\\Entity\\Deliv", "d")
            ->where($qb->expr()->in('d.product_type_id', $productTypeIds))
            ->andWhere("d.del_flg = 0")
            ->orderBy("d.rank", "ASC")
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
            $deliveryFees = $delivery->getDelivFees();
            $Order->setDeliv($delivery);
            $Order->setDelivFee($deliveryFees[0]->getFee());

            // 初期選択の支払い方法をセット
            $paymentOptions = $delivery->getPaymentOptions();
            $payment = $paymentOptions[0]->getPayment();
            ;
            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setCharge($payment->getCharge());

            $total = $subTotal + $Order->getCharge() + $Order->getDelivFee();
            $paymentTotal = $total - $Order->getUsePoint();

            $Order->setTotal($total);
            $Order->setSubTotal($subTotal);
            $Order->setPaymentTotal($paymentTotal);
            $this->app['orm.em']->flush();

            return $Order;
        }

        public function commit(\Eccube\Entity\Order $Order)
        {
            // todo delFlagではなく確定フラグにする
            $Order->setDelFlg(0);

            // todo 在庫引当
            // todo ポイント引当
            $this->app['orm.em']->persist($Order);
            $this->app['orm.em']->flush();
        }
}

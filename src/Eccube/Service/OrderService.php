<?php
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

    public function copyToOrderFromCustomer(\Eccube\Entity\Order $order, \Eccube\Entity\Customer $customer = null)
    {
        if (is_null($customer)) {
            return $order;
        }

        $order->setCustomer($customer)
              ->setName01($customer->getName01())
              ->setName02($customer->getName02())
              ->setKana01($customer->getKana02())
              ->setKana02($customer->getKana02())
              ->setCompanyName($customer->getCompanyName())
              ->setEmail($customer->getEmail())
              ->setTel01($customer->getTel01())
              ->setTel02($customer->getTel02())
              ->setTel03($customer->getTel03())
              ->setFax01($customer->getFax01())
              ->setFax02($customer->getFax02())
              ->setFax03($customer->getFax03())
              ->setZip01($customer->getZip01())
              ->setZip02($customer->getZip02())
              ->setPref($customer->getPref())
              ->setAddr01($customer->getAddr01())
              ->setAddr02($customer->getAddr02())
              ->setSex($customer->getSex())
              ->setBirth($customer->getBirth())
              ->setJob($customer->getJob());

        return $order;
    }

    public function copyToShippingFromCustomer(\Eccube\Entity\Shipping $shipping, \Eccube\Entity\Customer $customer = null)
    {
        if (is_null($customer)) {
            return $shipping;
        }

        $shipping
            ->setName01($customer->getName01())
            ->setName02($customer->getName02())
            ->setKana01($customer->getKana02())
            ->setKana02($customer->getKana02())
            ->setCompanyName($customer->getCompanyName())
            ->setTel01($customer->getTel01())
            ->setTel02($customer->getTel02())
            ->setTel03($customer->getTel03())
            ->setFax01($customer->getFax01())
            ->setFax02($customer->getFax02())
            ->setFax03($customer->getFax03())
            ->setZip01($customer->getZip01())
            ->setZip02($customer->getZip02())
            ->setPref($customer->getPref())
            ->setAddr01($customer->getAddr01())
            ->setAddr02($customer->getAddr02());

        return $shipping;
    }

    public function registerPreOrderFromCart(array $products, \Eccube\Entity\Customer $customer = null)
    {
        // 受注
        $order = new \Eccube\Entity\Order();
        $this->copyToOrderFromCustomer($order, $customer)
             ->setCreateDate(new \DateTime())
             ->setUpdateDate(new \DateTime())
             ->setDiscount(0)
             ->setUsePoint(0)
             ->setAddPoint(0)
             ->setBirthPoint(0)
             ->setStatus(1)
             ->setDelFlg(1); // todo 未確定受注を、del_flg：1で立てる（仮）
        $this->app['orm.em']->persist($order);

        // 配送先
        $shipping = new \Eccube\Entity\Shipping();
        $this->copyToShippingFromCustomer($shipping, $customer)
             ->setShippingId(1)
             ->setOrderId($order->getId())
             ->setCreateDate(new \DateTime())
             ->setUpdateDate(new \DateTime())
             ->setDelFlg(0);
        $this->app['orm.em']->persist($shipping);

        // 商品種別
        $productTypeIds = array();

        // 受注詳細, 配送商品
        foreach ($products as $p) {
            $product = $p['Product'];
            $productClass = $p['ProductClass'];
            $quantity = $p['quantity'];

            // 商品種別
            $productTypeIds[] = $productClass->getProductTypeId();

            // 受注詳細
            $orderDetail = new \Eccube\Entity\OrderDetail();
            $orderDetail->setOrder($order)
                        ->setProduct($product)
                        ->setProductClass($productClass)
                        ->setProductName($product->getName())
                        ->setProductCode($productClass->getProductCode())
                        ->setClasscategoryName1($productClass->getClassCategory1()->getName())
                        ->setClasscategoryName2($productClass->getClassCategory2()->getName())
                        ->setPrice($productClass->getPrice02())
                        ->setQuantity($quantity)
                        ->setPointRate(0) // todo
                        ->setTaxRule(0) // todo
                        ->setTaxRate(0); // todo
            $this->app['orm.em']->persist($orderDetail);

            // 配送商品
            $shipmentItem = new \Eccube\Entity\ShipmentItem();
            $shipmentItem->setShippingId($shipping->getShippingId())
                         ->setOrderId($order->getId())
                         ->setProductClassId($productClass->getId())
                         ->setProductName($product->getName())
                         ->setProductCode($productClass->getProductCode())
                         ->setClasscategoryName1($productClass->getClassCategory1()->getName())
                         ->setClasscategoryName2($productClass->getClassCategory2()->getName())
                         ->setPrice($productClass->getPrice02())
                         ->setQuantity($quantity);
            $this->app['orm.em']->persist($shipmentItem);
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
        $order->setDeliv($delivery);
        $order->setDelivFee($deliveryFees[0]->getFee());

        // 初期選択の支払い方法をセット
        $paymentOptions = $delivery->getPaymentOptions();
        $payment = $paymentOptions[0]->getPayment();;
        $order->setPayment($payment);
        $order->setPaymentMethod($payment->getMethod());
        $order->setCharge($payment->getCharge());

        $this->app['orm.em']->flush();
        return $order;
    }

    public function commit(\Eccube\Entity\Order $order)
    {
        $order->setDelFlg(0); // todo
        $this->app['orm.em']->persist($order);
        $this->app['orm.em']->flush();
    }
}

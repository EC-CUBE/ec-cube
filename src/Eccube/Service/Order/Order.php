<?php
namespace Eccube\Service\Order;

use Eccube\Application;

class Order {
    
    /**
     *
     * @var Eccube\Application
     */
    protected $app;
    
    /**
     *
     * @var Eccube\Entity\Order
     */
    protected $order;

    /**
     *
     * @var array
     */
    protected $orderDetails;

    /**
     * @var array
     */
    protected $clculator;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    function setOrder($order) {
        $this->order = $order;
    }

    function getOrder() {
        return $this->order;
    }

    function setOrderDetails(array $orderDetails) {
        $this->orderDetails = $orderDetails;
    }

    function getOrderDetais() {
        return $this->orderDetails;
    }

    function calc() {
        foreach ($this->orderDetails as $detail) {
            foreach ($this->calculator as $c) {
                $c->calcOrderDetail($this->order, $detail);
            }
        }
        foreach ($this->calculator as $c) {
            $c->calcOrder($this->order);
        }
    }

    function addCalcurator(\Eccube\Service\Order\Calculator $c){
        $this->calculator[] = $c;
    }

    function findOrderByOrderId($orderId) {
        return $this->app['orm.em']
            ->getRepository("Eccube\\Entity\\Order")
            ->find($orderId);
    }

    public function findPreOrderByOrderId($preOrderId) {
        return $this->app['orm.em']->createQueryBuilder()
            ->select('o')
            ->from("Eccube\\Entity\\Order", 'o')
            ->where("o.orderId = :orderId and o.delFlg = 1")
            ->setParameter("orderId", $preOrderId)
            ->getQuery()
            ->getSingleResult();
    }

    function findOrderDetailsByOrderId($orderId) {
        return $this->app['orm.em']->createQueryBuilder()
            ->select('od')
            ->from("Eccube\\Entity\\OrderDetail", 'od')
            ->where("o.orderId = :orderId")
            ->setParameter("orderId", $orderId)
            ->getQuery()
            ->getResult();
    }

    function registerOrder($order, $orderDetails) {
        $this->app['orm.em']->persist($order);
        foreach ($orderDetails as $detail) {
            $this->app['orm.em']->persist($detail);
        }
        $this->app['orm.em']->flush();
    }

    public function createPreOrder(array $products) {
        $order = new \Eccube\Entity\Order();
        $order->setCustomerId($this->customer->getId());
        $order->setCreateDate(new \DateTime());
        $order->setUpdateDate(new \DateTime());
        $order->setDiscount(0);
        $order->setUsePoint(0);
        $order->setAddPoint(0);
        $order->setBirthPoint(0);
        $order->setStatus(1);
        $order->setDelFlg(1); // todo 未確定受注を、del_flg：1で立てる（仮）
        $this->app['orm.em']->persist($order);
        $this->app['orm.em']->flush();
        $orderId = $order->getOrderId();
        foreach ($products as $p) {
            $productClass = $p['entity'];
            $quantity = $p['quantity'];
            $orderDetail = new \Eccube\Entity\OrderDetail();
            $orderDetail->setOrderId($orderId);
            $orderDetail->setProductId($productClass->getProductId());
            $orderDetail->setProductClassId($productClass->getProductClassId());
            $orderDetail->setProductName($productClass->getProductName());
            $orderDetail->setProductCode($productClass->getProductCode());
            $orderDetail->setPrice($productClass->getPrice02());
            $orderDetail->setQuantity($quantity);
            $this->app['orm.em']->persist($orderDetail);
            $this->app['orm.em']->flush();
        }
        return $order;
    }

    public function commit($orderId) {
        $order = $this->app['orm.em']
                ->getRepository("Eccube\\Entity\\Order")
                ->find($orderId);
        $order->setDelFlg(0);
        $this->app['orm.em']->persist($order);
        $this->app['orm.em']->flush();
    }
}

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
     * @var array Eccube\Entity\OrderDetail
     */
    protected $orderDetail;
     
    /**
     *
     * @var array Eccube\Entity\Shipping
     */
    protected $shipping;
    
    /**
     *
     * @var array Eccube\Entity\ShipmentItem
     */
    protected $shipmentItem;
    
    /**
     *
     * @var Eccube\Entity\Customer 
     */
    protected $customer;
    
    public function __construct(Application $app) {
        $this->app = $app;
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
        $order->setDelFlg(1); // todo いったんdel_flg：1で立てる
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
    
    public function findPreOrder($preOrderId) {
        return $this->app['orm.em']->createQueryBuilder()
            ->select('o')
            ->from("Eccube\\Entity\\Order", 'o')
            ->where("o.orderId = :orderId and o.delFlg = 1")
            ->setParameter("orderId", $preOrderId)
            ->getQuery()
            ->getSingleResult();
    }
    
    public function calculate() {
        $order = $this->app['orm.em']
                ->getRepository("Eccube\\Entity\\Order")
                ->find($orderId);
        $orderDetails = $this->app['orm.em']
                ->getRepository("Eccube\\Entity\\Order")
                ->find($orderId);
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

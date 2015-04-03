<?php
namespace Eccube\Service\Order;

use Eccube\Application;

class Order {
    
    protected $app;
    
    public function __construct(Application $app) {
        $this->app = $app;
    }
    
    public function createPreOrder(array $products) {
        $order = new Eccube\Entity\OrderTmp();
        foreach ($products as $p) {
            $productClass = $p['entity'];
            $quantity = $p['entity'];
            $orderDetail = new Eccube\Entity\OrderDetail();
            $orderDetail->setProductId($productClass->getProductId());
            $orderDetail->setProductClassId($productClass->getProductClassId());
            $orderDetail->setPrice($productClass->getPrice02());
            $orderDetail->setQuantity($quantity);
            $order->addOrderDetail($orderDetail);
        }
        return $order;
    }
    
    public function findPreOrder($preOrderId) {
        $qb =  $this->app['orm.em']->createQueryBuilder()
            ->select('p, bp, b')
            ->from('Eccube\Entity\OrderTmp', 'o')
            ->leftJoin('o.OrderDetails', 'od', JOiN::ON, 'o.pre_order_id = od.pre_order_id')
            ->andWhere('o.pre_order_id = :pre_order_id');
        return $qb->getQuery()
            ->setParameters(array('pre_order_id' => $preOrderId))
            ->getSingleResult();
    }
}

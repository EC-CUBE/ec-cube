<?php

namespace Eccube\Service;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\Order;
use Eccube\Repository\OrderRepository;


/**
 * OrderやOrderに関連するエンティティを構築するクラス
 * namespaceやクラス名は要検討
 *
 * @package Eccube\Service
 */
class OrderHelper
{
    /** @var array */
    protected $config;

    /** @var EntityManager */
    protected $em;

    /** @var OrderRepository */
    protected $orderRepository;

    public function createOrder($Customer = null)
    {
        $Order = new Order();
        $Order->setPreOrderId($this->createPreOrderId());


        $this->em->persist($Order);
        $this->em->flush($Order);


    }

    public function createPreOrderId()
    {
        // ランダムなpre_order_idを作成
        do {
            $preOrderId = sha1(Str::random(32));

            $Order = $this->orderRepository->findOneBy(array(
                'pre_order_id' => $preOrderId,
                'OrderStatus' => $this->config['order_processing'],
            ));
        } while ($Order);

        return $preOrderId;
    }
}
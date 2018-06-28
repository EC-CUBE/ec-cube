<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Repository;

use Doctrine\ORM\Query;
use Eccube\Entity\Payment;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * PaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends AbstractRepository
{
    /**
     * PaymentRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @deprecated 呼び出し元で制御する
     *
     * @param $id
     *
     * @return \Eccube\Entity\Payment|null|object
     */
    public function findOrCreate($id)
    {
        if ($id == 0) {
            $Payment = $this->findOneBy([], ['sort_no' => 'DESC']);

            $sortNo = 1;
            if ($Payment) {
                $sortNo = $Payment->getSortNo() + 1;
            }

            $Payment = new \Eccube\Entity\Payment();
            $Payment
                ->setSortNo($sortNo)
                ->setFixed(true)
                ->setVisible(true);
        } else {
            $Payment = $this->find($id);
        }

        return $Payment;
    }

    /**
     * @return array
     */
    public function findAllArray()
    {
        $query = $this
            ->getEntityManager()
            ->createQuery('SELECT p FROM Eccube\Entity\Payment p INDEX BY p.id');
        $result = $query
            ->getResult(Query::HYDRATE_ARRAY);

        return $result;
    }

    /**
     * 支払方法を取得
     * 条件によってはDoctrineのキャッシュが返されるため、arrayで結果を返すパターンも用意
     *
     * @param $delivery
     * @param $returnType true : Object、false: arrayが戻り値
     *
     * @return array
     */
    public function findPayments($delivery, $returnType = false)
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('Eccube\Entity\PaymentOption', 'po', 'WITH', 'po.payment_id = p.id')
            ->where('po.Delivery = (:delivery)')
            ->orderBy('p.sort_no', 'DESC')
            ->setParameter('delivery', $delivery)
            ->getQuery();

        $query->expireResultCache(false);

        if ($returnType) {
            $payments = $query->getResult();
        } else {
            $payments = $query->getArrayResult();
        }

        return $payments;
    }

    /**
     * 共通の支払方法を取得
     *
     * @param $deliveries
     * @param bool $returnType
     *
     * @return array
     */
    public function findAllowedPayments($deliveries, $returnType = false)
    {
        $payments = [];
        foreach ($deliveries as $Delivery) {
            $paymentTmp = [];
            $p = $this->findPayments($Delivery, $returnType);
            if ($p == null) {
                continue;
            }
            foreach ($p as $payment) {
                $paymentTmp[$payment['id']] = $payment;
            }

            if (empty($payments)) {
                $payments = $paymentTmp;
            } else {
                $payments = array_intersect($payments, $paymentTmp);
            }
        }

        return $payments;
    }
}

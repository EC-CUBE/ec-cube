<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\OrderItem;
use Eccube\Entity\RefundRequest;
use Eccube\Util\StringUtil;

/**
 * RefundRequestRepository
 *
 * @extends AbstractRepository<RefundRequest>
 */
class RefundRequestRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RefundRequest::class);
    }

    /**
     * 管理画面の検索条件から QueryBuilder を生成する.
     *
     * @param array<string, mixed> $searchData
     */
    public function getQueryBuilderBySearchData(array $searchData): QueryBuilder
    {
        $qb = $this->createQueryBuilder('rr')
            ->innerJoin('rr.Order', 'o')
            ->innerJoin('rr.OrderItem', 'oi')
            ->innerJoin('rr.Customer', 'c')
            ->innerJoin('rr.RefundRequestStatus', 's');

        // 複合検索（申請ID・注文番号・会員ID・会員名）
        if (isset($searchData['multi']) && StringUtil::isNotBlank($searchData['multi'])) {
            $multi = preg_match('/^\d{0,10}$/', (string) $searchData['multi']) ? $searchData['multi'] : null;
            $qb->andWhere('rr.id = :multi_id OR o.order_no LIKE :multi_like OR c.id = :multi_id '
                .'OR CONCAT(c.name01, c.name02) LIKE :multi_like')
                ->setParameter('multi_id', $multi)
                ->setParameter('multi_like', '%'.$this->escapeLike($searchData['multi']).'%');
        }

        // ステータス（複数選択）。Form の EntityType(multiple) は空でも ArrayCollection を返すため
        // is_iterable は常に true。要素ゼロのまま IN 句を組むと "IN (NULL)" になり何もマッチしないので
        // 必ず件数チェックする。
        if (!empty($searchData['status']) && is_iterable($searchData['status']) && count($searchData['status']) > 0) {
            $qb->andWhere($qb->expr()->in('rr.RefundRequestStatus', ':status'))
                ->setParameter('status', $searchData['status']);
        }

        // 申請日時
        if (!empty($searchData['create_date_start'])) {
            $qb->andWhere('rr.create_date >= :create_date_start')
                ->setParameter('create_date_start', $searchData['create_date_start']);
        }
        if (!empty($searchData['create_date_end'])) {
            $date = clone $searchData['create_date_end'];
            $date = $date->modify('+1 days');
            $qb->andWhere('rr.create_date < :create_date_end')
                ->setParameter('create_date_end', $date);
        }

        // 更新日時
        if (!empty($searchData['update_date_start'])) {
            $qb->andWhere('rr.update_date >= :update_date_start')
                ->setParameter('update_date_start', $searchData['update_date_start']);
        }
        if (!empty($searchData['update_date_end'])) {
            $date = clone $searchData['update_date_end'];
            $date = $date->modify('+1 days');
            $qb->andWhere('rr.update_date < :update_date_end')
                ->setParameter('update_date_end', $date);
        }

        $qb->orderBy('rr.update_date', 'DESC');

        return $qb;
    }

    /**
     * 指定した注文明細に対する会員の返品申請を時系列で取得する.
     *
     * @return RefundRequest[]
     */
    public function findByOrderItemAndCustomer(OrderItem $OrderItem, Customer $Customer): array
    {
        return $this->createQueryBuilder('rr')
            ->innerJoin('rr.RefundRequestStatus', 's')
            ->leftJoin('rr.RefundRequestFiles', 'f')
            ->addSelect('s', 'f')
            ->where('rr.OrderItem = :OrderItem')
            ->andWhere('rr.Customer = :Customer')
            ->setParameter('OrderItem', $OrderItem)
            ->setParameter('Customer', $Customer)
            ->orderBy('rr.create_date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 会員の返品申請件数を、注文明細ID別に一括取得する（N+1回避）.
     *
     * @return array<int, int> [order_item_id => count]
     */
    public function getRefundRequestCountsByCustomer(Customer $Customer): array
    {
        $rows = $this->createQueryBuilder('rr')
            ->select('IDENTITY(rr.OrderItem) AS order_item_id', 'COUNT(rr.id) AS cnt')
            ->where('rr.Customer = :Customer')
            ->setParameter('Customer', $Customer)
            ->groupBy('rr.OrderItem')
            ->getQuery()
            ->getResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row['order_item_id']] = (int) $row['cnt'];
        }

        return $counts;
    }

    /**
     * 指定した注文明細に対する会員の「却下を除く」返品申請数量の合計を返す.
     *
     * 過剰返品（注文数量を超える申請）を防ぐための残数量計算に使う。
     * 却下(DECLINED)済みは数量を解放するため合計に含めない。
     */
    public function getRequestedQuantity(OrderItem $OrderItem, Customer $Customer): int
    {
        $sum = $this->createQueryBuilder('rr')
            ->select('COALESCE(SUM(rr.quantity), 0)')
            ->where('rr.OrderItem = :OrderItem')
            ->andWhere('rr.Customer = :Customer')
            ->andWhere('IDENTITY(rr.RefundRequestStatus) != :declined')
            ->setParameter('OrderItem', $OrderItem)
            ->setParameter('Customer', $Customer)
            ->setParameter('declined', RefundRequestStatus::DECLINED)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $sum;
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }
}

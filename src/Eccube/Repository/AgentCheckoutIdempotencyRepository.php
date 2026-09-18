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

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Eccube\Entity\AgentCheckoutIdempotency;

/**
 * AgentCheckoutIdempotencyRepository
 *
 * @extends AbstractRepository<AgentCheckoutIdempotency>
 */
class AgentCheckoutIdempotencyRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AgentCheckoutIdempotency::class);
    }

    /**
     * Idempotency-Key と主体で 1 件取得する.
     */
    public function findOneByKeyAndSubject(string $key, string $subject): ?AgentCheckoutIdempotency
    {
        return $this->findOneBy(['idempotency_key' => $key, 'subject' => $subject]);
    }

    /**
     * 指定時刻より前に作成された記録を削除する (保管期間超過分のクリーンアップ用).
     *
     * クリーンアップコマンド (派生 issue) から利用する想定。
     *
     * @return int 削除件数
     */
    public function deleteOlderThan(\DateTime $threshold): int
    {
        return (int) $this->createQueryBuilder('i')
            ->delete()
            ->where('i.create_date < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();
    }
}

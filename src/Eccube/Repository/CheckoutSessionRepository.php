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
use Eccube\Entity\CheckoutSession;

/**
 * CheckoutSessionRepository
 *
 * @extends AbstractRepository<CheckoutSession>
 */
class CheckoutSessionRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CheckoutSession::class);
    }

    /**
     * エージェント公開用のセッション識別子で 1 件取得する.
     */
    public function findOneBySessionId(string $sessionId): ?CheckoutSession
    {
        return $this->findOneBy(['session_id' => $sessionId]);
    }

    /**
     * 指定時刻より前に有効期限が切れた未完了セッションを取得する.
     *
     * クリーンアップコマンド (派生 issue) から利用する想定。
     *
     * @return array<int, CheckoutSession>
     */
    public function findExpired(\DateTime $now): array
    {
        $qb = $this->createQueryBuilder('cs');

        /** @var array<int, CheckoutSession> $result */
        $result = $qb
            ->where('cs.expires_at IS NOT NULL')
            ->andWhere('cs.expires_at < :now')
            ->andWhere($qb->expr()->notIn('cs.status', ':terminalStatuses'))
            ->setParameter('now', $now)
            ->setParameter('terminalStatuses', [
                CheckoutSession::STATUS_COMPLETED,
                CheckoutSession::STATUS_CANCELED,
                CheckoutSession::STATUS_EXPIRED,
            ])
            ->getQuery()
            ->getResult();

        return $result;
    }
}

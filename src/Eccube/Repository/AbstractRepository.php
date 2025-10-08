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

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\AbstractEntity;

/**
 * ECCUBE AbstractRepository
 *
 * @method T|null find($id, $lockMode = null, $lockVersion = null)
 * @method T|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method T[]    findAll()
 * @method T[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 *
 * @template T of AbstractEntity
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * エンティティを削除します。
     *
     * @param T $entity
     *
     * @return void
     */
    public function delete($entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * エンティティの登録/保存します。
     *
     * @param T $entity
     *
     * @return void
     */
    public function save($entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @return int|string|null
     */
    protected function getCacheLifetime(): int|string|null
    {
        if ($this->eccubeConfig !== null) {
            return $this->eccubeConfig['eccube_result_cache_lifetime'];
        }

        return 0;
    }

    /**
     * PostgreSQL環境かどうかを判定します。
     *
     * @return bool
     */
    protected function isPostgreSQL(): bool
    {
        return 'postgresql' == $this->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
    }

    /**
     * MySQL環境かどうかを判定します。
     *
     * @return bool
     */
    protected function isMySQL(): bool
    {
        return 'mysql' == $this->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
    }
}

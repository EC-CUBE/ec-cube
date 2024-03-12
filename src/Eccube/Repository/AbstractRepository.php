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
use Doctrine\DBAL\DBALException;
use Eccube\Entity\AbstractEntity;

abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @var array
     */
    protected $eccubeConfig;

    /**
     * エンティティを削除します。
     *
     * @param AbstractEntity $entity
     */
    public function delete($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * エンティティの登録/保存します。
     *
     * @param AbstractEntity $entity
     */
    public function save($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    protected function getCacheLifetime()
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
     *
     * @throws DBALException
     */
    protected function isPostgreSQL()
    {
        return 'postgresql' == $this->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
    }

    /**
     * MySQL環境かどうかを判定します。
     *
     * @return bool
     *
     * @throws DBALException
     */
    protected function isMySQL()
    {
        return 'mysql' == $this->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
    }
}

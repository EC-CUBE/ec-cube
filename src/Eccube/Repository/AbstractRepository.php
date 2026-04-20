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
use Doctrine\ORM\QueryBuilder;
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
    protected ?EccubeConfig $eccubeConfig = null;

    /**
     * エンティティを削除します。
     */
    public function delete(AbstractEntity $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * エンティティの登録/保存します。
     */
    public function save(AbstractEntity $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    protected function getCacheLifetime(): int|string|null
    {
        if ($this->eccubeConfig !== null) {
            return $this->eccubeConfig['eccube_result_cache_lifetime'];
        }

        return 0;
    }

    /**
     * PostgreSQL環境かどうかを判定します。
     */
    protected function isPostgreSQL(): bool
    {
        return 'postgresql' == $this->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
    }

    /**
     * MySQL環境かどうかを判定します。
     */
    protected function isMySQL(): bool
    {
        return 'mysql' == $this->getEntityManager()->getConnection()->getDatabasePlatform()->getName();
    }

    /**
     * 管理画面の検索時のソートの設定の際にソートキーが存在しない場合にWarningになるのでその対処
     * プラグインやカスタマイズでソートキーを追加し、QueryCustomizerで制御できるようにするための措置
     *
     * @param array<string, string> $sortColumns
     * @param array<string, mixed> $searchData
     */
    protected function setQueryBuilderAdminSearchDataOrderBy(QueryBuilder $qb, string $alias = 'p', array $sortColumns = [], array $searchData = []): void
    {
        if (isset($searchData['sortkey']) && !empty($searchData['sortkey'])) {
            $sortOrder = (isset($searchData['sorttype']) && $searchData['sorttype'] == 'a') ? 'ASC' : 'DESC';

            $addOrderBy = $sortColumns[$searchData['sortkey']] ?? "{$alias}.update_date";
            $qb->orderBy($addOrderBy, $sortOrder);
            $qb->addOrderBy("{$alias}.update_date", 'DESC');
            $qb->addOrderBy("{$alias}.id", 'DESC');
        } else {
            $qb->orderBy("{$alias}.update_date", 'DESC');
            $qb->addOrderBy("{$alias}.id", 'DESC');
        }
    }
}

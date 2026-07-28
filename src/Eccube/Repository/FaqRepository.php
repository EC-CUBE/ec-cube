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
use Doctrine\Persistence\ManagerRegistry;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Category;
use Eccube\Entity\Faq;
use Eccube\Entity\Product;

/**
 * @extends AbstractRepository<Faq>
 */
class FaqRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    /**
     * FAQ を登録/保存します.
     */
    #[\Override]
    public function save(AbstractEntity $entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * FAQ を削除します.
     */
    #[\Override]
    public function delete(AbstractEntity $entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    /**
     * サイト共通FAQ（商品・カテゴリに紐付かない）の一覧 QueryBuilder を返す（管理画面用）.
     */
    public function getQueryBuilderAll(): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->where('f.Product IS NULL')
            ->andWhere('f.Category IS NULL')
            ->orderBy('f.sort_no', 'ASC')
            ->addOrderBy('f.id', 'ASC');
    }

    /**
     * フロント表示用のサイト共通FAQ（表示のみ・並び順）を返す.
     *
     * @return Faq[]
     */
    public function getCommonFaq(): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.Product IS NULL')
            ->andWhere('f.Category IS NULL')
            ->andWhere('f.visible = true')
            ->orderBy('f.sort_no', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * フロント表示用の商品ごとFAQ（表示のみ・並び順）を返す.
     *
     * @return Faq[]
     */
    public function getProductFaq(Product $Product): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.Product = :Product')
            ->andWhere('f.visible = true')
            ->setParameter('Product', $Product)
            ->orderBy('f.sort_no', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * フロント表示用のカテゴリごとFAQ（表示のみ・並び順）を返す.
     *
     * @return Faq[]
     */
    public function getCategoryFaq(Category $Category): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.Category = :Category')
            ->andWhere('f.visible = true')
            ->setParameter('Category', $Category)
            ->orderBy('f.sort_no', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

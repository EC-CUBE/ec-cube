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

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Entity\Member;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * MemberRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MemberRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Member::class);
    }

    /**
     * 管理ユーザを登録します.
     *
     * @param Member $Member
     */
    public function save($Member)
    {
        $em = $this->getEntityManager();
        $em->persist($Member);
        $em->flush($Member);
    }

    /**
     * 管理ユーザを削除します.
     *
     * @param Member $Member
     *
     * @throws ForeignKeyConstraintViolationException 外部キー制約違反の場合
     * @throws DriverException SQLiteの場合, 外部キー制約違反が発生すると, DriverExceptionをthrowします.
     */
    public function delete($Member)
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.sort_no', 'm.sort_no - 1')
            ->where('m.sort_no > :sort_no')
            ->getQuery()
            ->execute();

        $em = $this->getEntityManager();
        $em->remove($Member);
        $em->flush($Member);
    }
}

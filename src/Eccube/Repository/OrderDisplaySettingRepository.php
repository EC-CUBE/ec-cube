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
use Eccube\Entity\OrderDisplaySetting;

/**
 * @extends AbstractRepository<OrderDisplaySetting>
 */
class OrderDisplaySettingRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderDisplaySetting::class);
    }

    /**
     * 表示項目設定を取得（有効な項目のみ・ソート順）.
     *
     * @return array<OrderDisplaySetting>
     */
    public function getEnabledSettings(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('o.sort_no', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

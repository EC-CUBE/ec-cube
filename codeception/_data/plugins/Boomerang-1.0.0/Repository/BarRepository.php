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

namespace Plugin\Boomerang\Repository;


use Eccube\Repository\AbstractRepository;
use Plugin\Boomerang\Entity\Bar;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

class BarRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bar::class);
    }
}

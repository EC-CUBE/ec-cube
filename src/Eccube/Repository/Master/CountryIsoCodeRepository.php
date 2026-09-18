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

namespace Eccube\Repository\Master;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Eccube\Entity\Master\CountryIsoCode;
use Eccube\Repository\AbstractRepository;

/**
 * CountryIsoCodeRepository.
 *
 * @extends AbstractRepository<CountryIsoCode>
 */
class CountryIsoCodeRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CountryIsoCode::class);
    }
}

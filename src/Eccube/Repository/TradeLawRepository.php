<?php

namespace Eccube\Repository;

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Eccube\Entity\TradeLaw;

class TradeLawRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TradeLaw::class);
    }
}

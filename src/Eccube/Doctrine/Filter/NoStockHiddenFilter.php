<?php

namespace Eccube\Doctrine\Filter;

use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Mapping\ClassMetadata;

class NoStockHiddenFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->reflClass->getName() === 'Eccube\Entity\ProductClass') {
            return $targetTableAlias . '.stock >= 1 OR ' . $targetTableAlias . '.stock_unlimited = 1';
        } else {
            return "";
        }
    }
}

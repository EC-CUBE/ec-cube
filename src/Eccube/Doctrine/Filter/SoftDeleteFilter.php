<?php

namespace Eccube\Doctrine\Filter;

use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Mapping\ClassMetadata;

class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->hasField('del_flg')) {
            return $targetTableAlias . '.del_flg = 0';
        } else {
            return "";
        }
    }
}

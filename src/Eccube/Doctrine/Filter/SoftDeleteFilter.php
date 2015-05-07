<?php

namespace Eccube\Doctrine\Filter;

use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Mapping\ClassMetadata;

class SoftDeleteFilter extends SQLFilter
{
    public $excludes = array();

    public function setExcludes($excludes)
    {
        $this->excludes = $excludes;

        return $this;
    }
    
    public function getExcludes()
    {
        return $this->excludes;
    }

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->hasField('del_flg') && !in_array($targetEntity->getName(), $this->getExcludes())) {
            return $targetTableAlias . '.del_flg = 0';
        } else {
            return "";
        }
    }
}

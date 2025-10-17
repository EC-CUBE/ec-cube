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

namespace Eccube\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Eccube\Entity\ProductClass;

class NoStockHiddenFilter extends SQLFilter
{
    #[\Override]
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if ($targetEntity->reflClass->getName() === ProductClass::class) {
            return $targetTableAlias.'.stock >= 1 OR '.$targetTableAlias.'.stock_unlimited = true';
        } else {
            return '';
        }
    }
}

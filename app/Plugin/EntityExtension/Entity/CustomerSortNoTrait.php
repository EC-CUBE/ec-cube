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

namespace Plugin\EntityExtension\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Attribute\EntityExtension;

#[EntityExtension(\Eccube\Entity\Customer::class)]
trait CustomerSortNoTrait
{
    /**
     * @var int|null
     */
    #[ORM\Column(name: 'sort_no', type: 'smallint', nullable: true)]
    public $sort_no;
}

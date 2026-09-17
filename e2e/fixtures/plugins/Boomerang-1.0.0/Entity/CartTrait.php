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

namespace Plugin\Boomerang\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Attribute\EntityExtension;
use Eccube\Entity\Cart;

#[EntityExtension(Cart::class)]
trait CartTrait
{
    #[ORM\Column(name: 'is_boomerang', type: Types::BOOLEAN, nullable: true, options: ['default' => false])]
    public ?bool $is_boomerang = null;

    #[ORM\ManyToOne(targetEntity: Bar::class)]
    #[ORM\JoinColumn(name: 'bar_id', referencedColumnName: 'id')]
    public ?Bar $bar = null;
}

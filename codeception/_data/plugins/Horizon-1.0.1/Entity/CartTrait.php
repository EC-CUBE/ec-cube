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

namespace Plugin\Horizon\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Attribute\EntityExtension;
use Eccube\Entity\Cart;

#[EntityExtension(Cart::class)]
trait CartTrait
{
    /**
     * @var bool
     */
    #[ORM\Column(name: 'is_horizon', type: 'boolean', options: ['default' => false])]
    public $is_horizon;

    /**
     * @var Dash
     */
    #[ORM\ManyToOne(targetEntity: Dash::class)]
    #[ORM\JoinColumn(name: 'dash_id', referencedColumnName: 'id')]
    public $dash;
}

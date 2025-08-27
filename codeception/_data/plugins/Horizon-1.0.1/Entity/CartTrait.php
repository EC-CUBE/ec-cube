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

use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Cart")
 */
trait CartTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(name="is_horizon", type="boolean", options={"default":false})
     */
    public $is_horizon;

    /**
     * @var Dash
     *
     * @ORM\ManyToOne(targetEntity="Plugin\Horizon\Entity\Dash")
     *
     * @ORM\JoinColumns({
     *
     *     @ORM\JoinColumn(name="dash_id", referencedColumnName="id")
     * })
     */
    public $dash;
}

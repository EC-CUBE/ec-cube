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

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Cart")
 */
trait CartTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(name="is_boomerang", type="boolean", options={"default":false}, nullable=true)
     */
    public $is_boomerang;

    /**
     * @var Bar
     *
     * @ORM\ManyToOne(targetEntity="Plugin\Boomerang\Entity\Bar")
     *
     * @ORM\JoinColumns({
     *
     *     @ORM\JoinColumn(name="bar_id", referencedColumnName="id")
     * })
     */
    public $bar;
}

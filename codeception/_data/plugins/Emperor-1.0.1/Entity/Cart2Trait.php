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

namespace Plugin\Emperor\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Cart")
 */
trait Cart2Trait
{
    /**
     * @ORM\OneToOne(targetEntity="Plugin\Emperor\Entity\Bar")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="bar_id", referencedColumnName="id")
     * })
     */
    public $bar;
}

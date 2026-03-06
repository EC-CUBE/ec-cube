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
trait CartTrait
{
    /**
     * @ORM\OneToOne(targetEntity="Plugin\Emperor\Entity\Foo")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="foo_id", referencedColumnName="id")
     * })
     */
    public $foo;
}

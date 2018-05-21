<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;

/**
 * @Eccube\EntityExtension("Eccube\Entity\Product")
 */
trait CountryTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="\Eccube\Entity\Master\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    public $Country;
}

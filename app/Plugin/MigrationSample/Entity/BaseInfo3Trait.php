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

namespace Plugin\MigrationSample\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\BaseInfo")
 */
trait BaseInfo3Trait
{
    /**
     * @var string
     *
     * @ORM\Column(name="migration_sample", type="string", length=255, nullable=true)
     */
    private $migration_sample;
}

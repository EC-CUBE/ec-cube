<?php

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;

/**
 * @Eccube\EntityExtension("Eccube\Entity\Product")
 */
trait DbTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="\Eccube\Entity\Master\Db")
     * @ORM\JoinColumn(name="database_id", referencedColumnName="id")
     */
    public $DataBase;
}
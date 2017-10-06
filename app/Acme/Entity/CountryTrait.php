<?php

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

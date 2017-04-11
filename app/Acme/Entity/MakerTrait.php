<?php

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;

/**
 * @Eccube\EntityExt(target="Eccube\Entity\Product")
 */
trait MakerTrait
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public $maker_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public $maker_url;
}
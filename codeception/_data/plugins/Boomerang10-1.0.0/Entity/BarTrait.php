<?php

namespace Plugin\Boomerang10\Entity;


use Eccube\Annotation\EntityExtension;
use Doctrine\ORM\Mapping as ORM;

/**
 * @EntityExtension("Plugin\Boomerang\Entity\Bar")
 */
trait BarTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
     */
    public $mail;
}
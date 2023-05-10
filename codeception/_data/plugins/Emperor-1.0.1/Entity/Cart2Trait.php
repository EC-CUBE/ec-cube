<?php

namespace Plugin\Emperor\Entity;


use Eccube\Annotation\EntityExtension;
use Doctrine\ORM\Mapping as ORM;

/**
 * @EntityExtension("Eccube\Entity\Cart")
 */
trait Cart2Trait
{
    /**
     * @ORM\OneToOne(targetEntity="Plugin\Emperor\Entity\Bar")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bar_id", referencedColumnName="id")
     * })
     */
    public $bar;
}

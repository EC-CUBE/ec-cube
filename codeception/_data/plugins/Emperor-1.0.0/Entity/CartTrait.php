<?php

namespace Plugin\Emperor\Entity;


use Eccube\Annotation\EntityExtension;
use Doctrine\ORM\Mapping as ORM;

/**
 * @EntityExtension("Eccube\Entity\Cart")
 */
trait CartTrait
{
    /**
     * @ORM\OneToOne(targetEntity="Plugin\Emperor\Entity\Foo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="foo_id", referencedColumnName="id")
     * })
     */
    public $foo;
}
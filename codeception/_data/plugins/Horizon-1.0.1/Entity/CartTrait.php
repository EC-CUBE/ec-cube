<?php

namespace Plugin\Horizon\Entity;

use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Cart")
 */
trait CartTrait
{
    /**
     * @var boolean
     * @ORM\Column(name="is_horizon", type="boolean", options={"default":false})
     */
    public $is_horizon;

    /**
     * @var Dash
     * @ORM\ManyToOne(targetEntity="Plugin\Horizon\Entity\Dash")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="dash_id", referencedColumnName="id")
     * })
     */
    public $dash;
}

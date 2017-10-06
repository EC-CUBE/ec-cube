<?php

namespace Acme\Entity;

use Doctrine\ORM\Mapping\AssociationOverride;
use Doctrine\ORM\Mapping\AssociationOverrides;
use Doctrine\ORM\Mapping\AttributeOverride;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\InheritanceType;

/**
 * Product の拡張
 * @Entity
 * @Table(name="extended_product")
 */
class ExtendedProduct extends \Eccube\Entity\Product
{
    /**
     * @Column(name="extended_parameter", type="string")
     */
    public $extendedParameter;

    /**
     * public フィールドでも大丈夫っぽい
     *
     * @ManyToOne(targetEntity="\Eccube\Entity\Master\Country")
     * @JoinColumn(name="country_id", referencedColumnName="id")
     */
    public $Country;
}

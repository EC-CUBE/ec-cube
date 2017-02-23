<?php

namespace Plugin\ExamplePlugin\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * Payment の拡張
 * @Entity
 * @Table(name="example_payment")
 */
class ExamplePayment extends \Eccube\Entity\Payment
{
    /**
     * @Column(name="use_paypal", type="smallint", nullable=true, options="default":0})
     */
    public $usePayPal = 0;
}

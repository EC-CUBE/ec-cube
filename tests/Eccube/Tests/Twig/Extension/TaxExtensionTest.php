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

namespace Eccube\Tests\Twig\Extension;

use Eccube\Repository\TaxRuleRepository;
use Eccube\Tests\EccubeTestCase;
use Eccube\Twig\Extension\TaxExtension;

class TaxExtensionTest extends EccubeTestCase
{
    /**
     * @var TaxExtension
     */
    protected $taxExtension;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    public function setUp()
    {
        parent::setUp();
        $this->taxExtension = $this->container->get(TaxExtension::class);
        $this->taxRuleRepository = $this->container->get(TaxRuleRepository::class);
    }

    public function testIsReducedTaxRate()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $OrderItem = $Order->getProductOrderItems()[0];
        self::assertFalse($this->taxExtension->isReducedTaxRate($OrderItem));

        $OrderItem->setTaxRate(99);
        self::assertTrue($this->taxExtension->isReducedTaxRate($OrderItem));
    }
}

<?php

declare(strict_types=1);

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

use Eccube\Entity\TaxRule;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Tests\EccubeTestCase;
use Eccube\Twig\Extension\TaxExtension;

final class TaxExtensionTest extends EccubeTestCase
{
    protected ?TaxExtension $taxExtension = null;

    protected ?TaxRuleRepository $taxRuleRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxExtension = static::getContainer()->get(TaxExtension::class);
        $this->taxRuleRepository = $this->entityManager->getRepository(TaxRule::class);
    }

    public function testIsReducedTaxRate()
    {
        $Customer = $this->createCustomer();
        $Order = $this->createOrder($Customer);

        $OrderItem = $Order->getProductOrderItems()[0];
        $this->assertFalse($this->taxExtension->isReducedTaxRate($OrderItem));

        $OrderItem->setTaxRate('99');
        $this->assertTrue($this->taxExtension->isReducedTaxRate($OrderItem));
    }
}

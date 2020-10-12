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

namespace Eccube\Tests\Entity;

use Eccube\Tests\EccubeTestCase;

final class ProductTest extends EccubeTestCase
{
    public function testAggregatesWithoutProductClass()
    {
        $Product = $this->createProduct(null, 0);
        $Product->getProductClasses()->clear();
        $this->assertNull($Product->getStockFind());
        $this->assertNull($Product->getStockMin());
        $this->assertNull($Product->getStockMax());
        $this->assertNull($Product->getStockUnlimitedMin());
        $this->assertNull($Product->getStockUnlimitedMax());
        $this->assertNull($Product->getPrice01Min());
        $this->assertNull($Product->getPrice01Max());
        $this->assertNull($Product->getPrice02Min());
        $this->assertNull($Product->getPrice02Max());
        $this->assertNull($Product->getPrice01IncTaxMin());
        $this->assertNull($Product->getPrice01IncTaxMax());
        $this->assertNull($Product->getPrice02IncTaxMin());
        $this->assertNull($Product->getPrice02IncTaxMax());
    }
}

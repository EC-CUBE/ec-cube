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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Service\PurchaseFlow\Processor\CustomerPurchaseInfoProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class CustomerPurchaseInfoProcessorTest extends EccubeTestCase
{
    /**
     * @throws \Eccube\Service\PurchaseFlow\PurchaseException
     */
    public function testCommit()
    {
        $Customer = $this->createCustomer();
        $OriginCustomer = clone $Customer;

        $Order = $this->createOrder($Customer);

        $processor = new CustomerPurchaseInfoProcessor();
        $processor->commit($Order, new PurchaseContext(null, $Customer));

        self::assertNotNull($Customer->getFirstBuyDate());
        self::assertGreaterThan($OriginCustomer->getLastBuyDate(), $Customer->getLastBuyDate());
        self::assertGreaterThan($OriginCustomer->getBuyTimes(), $Customer->getBuyTimes());
        self::assertGreaterThan($OriginCustomer->getBuyTotal(), $Customer->getBuyTotal());
    }
}

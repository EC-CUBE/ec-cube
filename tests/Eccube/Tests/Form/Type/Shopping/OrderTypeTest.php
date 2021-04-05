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

namespace Eccube\Tests\Form\Type\Shopping;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\Payment;
use Eccube\Form\Type\Shopping\OrderType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class OrderTypeTest extends AbstractTypeTestCase
{
    private $paymentRepository;

    private $orderType;

    public function setUp()
    {
        parent::setUp();
        $this->paymentRepository = $this->entityManager->getRepository(Payment::class);
        $this->orderType = self::$container->get(OrderType::class);
    }

    /**
     * @dataProvider filterPaymentsProvider
     */
    public function testFilterPayments($charge, $total, $min, $max, $result)
    {
        $Payment = new Payment();
        $Payment->setCharge($charge);
        $Payment->setRuleMin($min);
        $Payment->setRuleMax($max);

        $refObj = new \ReflectionObject($this->orderType);
        $refMethod = $refObj->getMethod('filterPayments');
        $refMethod->setAccessible(true);
        $FilterResults = $refMethod->invokeArgs($this->orderType, [new ArrayCollection([$Payment]), $total]);

        self::assertCount($result, $FilterResults);
    }

    public function filterPaymentsProvider()
    {
        return [
            // charge, total, min, max, result
            [null, null, null, null, 1],
            [50, 50, 99, null, 1],
            [50, 50, 100, null, 1],
            [50, 50, 101, null, 0],
            [50, 50, null, 99, 0],
            [50, 50, null, 100, 1],
            [50, 50, null, 101, 1],
        ];
    }
}

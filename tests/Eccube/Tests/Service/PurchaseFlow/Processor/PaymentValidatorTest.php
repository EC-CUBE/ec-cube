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

use Eccube\Entity\Delivery;
use Eccube\Service\PurchaseFlow\Processor\PaymentValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentValidatorTest extends EccubeTestCase
{
    /**
     * @var PaymentValidator
     */
    private $validator;

    /**
     * @var $Order
     */
    private $Order;

    protected function setUp(): void
    {
        parent::setUp();

        $deliveryRepository = $this->entityManager->getRepository(Delivery::class);
        $this->validator = new PaymentValidator($deliveryRepository);

        $Customer = $this->createCustomer();
        $this->Order = $this->createOrder($Customer);
    }

    public function testInstance()
    {
        self::assertInstanceOf(PaymentValidator::class, $this->validator);
    }

    public function testValidatePaymentVisibleFalse()
    {
        $this->Order->getPayment()->setVisible(false);

        $result = $this->validator->execute($this->Order, new PurchaseContext());

        self::assertTrue($result->isError());
    }
}

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
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Service\PurchaseFlow\Processor\DeliveryValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeliveryValidatorTest extends EccubeTestCase
{
    /**
     * @var DeliveryValidator
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
        $this->validator = new DeliveryValidator($deliveryRepository);

        $Customer = $this->createCustomer();
        $this->Order = $this->createOrder($Customer);
    }

    public function testInstance()
    {
        self::assertInstanceOf(DeliveryValidator::class, $this->validator);
    }

    public function testValidateDeliveryVisibleFalse()
    {
        /** @var Shipping $Shipping */
        foreach ($this->Order->getShippings() as $Shipping) {
            $Shipping->getDelivery()->setVisible(false);
            $Shipping->getDelivery()->isVisible();
            $result = $this->validator->execute($this->Order, new PurchaseContext());
            self::assertTrue($result->isError());
        }
    }
}

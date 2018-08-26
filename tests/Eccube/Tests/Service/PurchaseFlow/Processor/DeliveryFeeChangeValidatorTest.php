<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Cart;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeChangeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class DeliveryFeeChangeValidatorTest extends EccubeTestCase
{
    /**
     * @var DeliveryFeeChangeValidator
     */
    private $validator;

    /**
     * @var Customer
     */
    private $Customer;

    /**
     * @var Order
     */
    private $Order;

    /**
     * @var DeliveryFeeRepository
     */
    private $deliveryFeeRepository;

    public function setUp()
    {
        parent::setUp();

        $this->deliveryFeeRepository = $this->container->get(DeliveryFeeRepository::class);
        $this->validator = new DeliveryFeeChangeValidator($this->deliveryFeeRepository);

        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testNewInstance()
    {
        $deliveryFeeRepository = $this->container->get(DeliveryFeeRepository::class);
        $validator = new DeliveryFeeChangeValidator($deliveryFeeRepository);

        self::assertInstanceOf(DeliveryFeeChangeValidator::class, $validator);
    }

    public function testValidateWithCart()
    {
        $result = $this->validator->execute(new Cart(), new PurchaseContext());

        // カートの場合は何もしない.
        self::assertTrue($result->isSuccess());
    }

    public function testValidateWithNoDeliveryFeeItem()
    {
        // 送料明細のない受注を作成.
        foreach ($this->Order->getOrderItems() as $item) {
            $this->Order->removeOrderItem($item);
        }

        // 送料明細がない場合は何もしない.
        $result = $this->validator->execute($this->Order, new PurchaseContext());

        self::assertTrue($result->isSuccess());
    }

    public function testValidateDeliveryFeeTotalZero()
    {
        $this->Order->setDeliveryFeeTotal(0);

        // 送料が0の場合は何もしない.
        $result = $this->validator->execute($this->Order, new PurchaseContext());

        self::assertTrue($result->isSuccess());
    }

    public function testValidateFeeChanged()
    {
        $Fees = $this->deliveryFeeRepository->findAll();
        foreach ($Fees as $Fee) {
            $Fee->setFee($Fee->getFee() + 1);
            $this->entityManager->flush($Fee);
        }
        $result = $this->validator->execute($this->Order, new PurchaseContext());

        self::assertTrue($result->isWarning());
    }
}

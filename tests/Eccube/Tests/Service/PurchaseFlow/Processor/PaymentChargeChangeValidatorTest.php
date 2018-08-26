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
use Eccube\Repository\PaymentRepository;
use Eccube\Service\PurchaseFlow\Processor\PaymentChargeChangeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PaymentChargeChangeValidatorTest extends EccubeTestCase
{
    /**
     * @var PaymentChargeChangeValidator
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
     * @var PaymentRepository
     */
    private $paymentRepository;

    public function setUp()
    {
        parent::setUp();

        $this->paymentRepository = $this->container->get(PaymentRepository::class);
        $this->validator = new PaymentChargeChangeValidator();

        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
    }

    public function testNewInstance()
    {
        $validator = new PaymentChargeChangeValidator();

        self::assertInstanceOf(PaymentChargeChangeValidator::class, $validator);
    }

    public function testValidateWithCart()
    {
        $result = $this->validator->execute(new Cart(), new PurchaseContext());

        // カートの場合は何もしない.
        self::assertTrue($result->isSuccess());
    }

    public function testValidateNoChargeItems()
    {
        foreach ($this->Order->getOrderItems() as $item) {
            if ($item->isCharge()) {
                $this->Order->removeOrderItem($item);
            }
        }

        $result = $this->validator->execute($this->Order, new PurchaseContext());

        // 手数料明細がない場合は何もしない..
        self::assertTrue($result->isSuccess());
    }

    public function testValidateNoChanged()
    {
        $result = $this->validator->execute($this->Order, new PurchaseContext());

        // 差異がない場合はsuccess.
        self::assertTrue($result->isSuccess());
    }

    public function testValidateChargeChanged()
    {
        // dtb_paymentのchargeを更新
        $Payment = $this->Order->getPayment();
        $Payment->setCharge($Payment->getCharge() + 1);

        $result = $this->validator->execute($this->Order, new PurchaseContext());

        // warningになるはず.
        self::assertTrue($result->isWarning());

        foreach ($this->Order->getOrderItems() as $item) {
            if ($item->isCharge()) {
                // 手数料明細の金額が丸められている.
                self::assertSame($Payment->getCharge(), $item->getPrice());
            }
        }
    }
}

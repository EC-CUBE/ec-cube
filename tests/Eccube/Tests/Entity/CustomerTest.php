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

namespace Eccube\Tests\Entity;

use Eccube\Entity\Customer;
use Eccube\Entity\Delivery;
use Eccube\Entity\Payment;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Fixture\Generator;

/**
 * Customer の優先配送方法・優先支払方法のテスト.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6819
 */
final class CustomerTest extends EccubeTestCase
{
    /**
     * 優先支払方法・優先配送方法の設定とクリアができる.
     */
    public function testPreferredPaymentAndDeliveryAccessors(): void
    {
        $Customer = new Customer();
        $Payment = new Payment();
        $Delivery = new Delivery();

        $this->assertNotInstanceOf(Payment::class, $Customer->getPreferredPayment());
        $this->assertNotInstanceOf(Delivery::class, $Customer->getPreferredDelivery());

        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);
        $this->assertSame($Payment, $Customer->getPreferredPayment());
        $this->assertSame($Delivery, $Customer->getPreferredDelivery());

        $Customer->setPreferredPayment();
        $Customer->setPreferredDelivery();
        $this->assertNull($Customer->getPreferredPayment());
        $this->assertNull($Customer->getPreferredDelivery());
    }

    /**
     * 優先支払方法・優先配送方法が DB に保存される.
     */
    public function testPreferredPaymentAndDeliveryPersistence(): void
    {
        $Customer = $this->createCustomer();
        $Delivery = static::getContainer()->get(Generator::class)->createDelivery();
        $Payment = $this->createPayment($Delivery, 'テスト支払い方法');

        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Customer $found */
        $found = $this->entityManager->getRepository(Customer::class)->find($Customer->getId());
        $this->assertSame($Payment->getId(), $found->getPreferredPayment()->getId());
        $this->assertSame($Delivery->getId(), $found->getPreferredDelivery()->getId());
    }

    /**
     * 参照先の Payment / Delivery を削除すると, 外部キーの ON DELETE SET NULL により未設定へ戻る.
     */
    public function testPreferredResetToNullWhenReferenceDeleted(): void
    {
        $Customer = $this->createCustomer();
        $Delivery = static::getContainer()->get(Generator::class)->createDelivery();
        $Payment = $this->createPayment($Delivery, 'テスト支払い方法');

        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);
        $this->entityManager->flush();

        // 参照先を SQL で削除する(関連レコードを先に削除).
        $conn = $this->entityManager->getConnection();
        $conn->executeStatement('DELETE FROM dtb_payment_option WHERE delivery_id = :id', ['id' => $Delivery->getId()]);
        $conn->executeStatement('DELETE FROM dtb_delivery_fee WHERE delivery_id = :id', ['id' => $Delivery->getId()]);
        $conn->executeStatement('DELETE FROM dtb_delivery_time WHERE delivery_id = :id', ['id' => $Delivery->getId()]);
        $conn->executeStatement('DELETE FROM dtb_payment WHERE id = :id', ['id' => $Payment->getId()]);
        $conn->executeStatement('DELETE FROM dtb_delivery WHERE id = :id', ['id' => $Delivery->getId()]);

        $this->entityManager->clear();

        /** @var Customer $found */
        $found = $this->entityManager->getRepository(Customer::class)->find($Customer->getId());
        $this->assertNotInstanceOf(Payment::class, $found->getPreferredPayment());
        $this->assertNotInstanceOf(Delivery::class, $found->getPreferredDelivery());
    }
}

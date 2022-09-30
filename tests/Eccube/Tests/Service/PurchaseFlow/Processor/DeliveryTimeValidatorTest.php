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

use Eccube\Entity\DeliveryTime;
use Eccube\Service\PurchaseFlow\Processor\DeliveryTimeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use Eccube\Entity\Delivery;
use Eccube\Tests\Fixture\Generator;

class DeliveryTimeValidatorTest extends EccubeTestCase
{
    /**
     * @var DeliveryTimeValidator
     */
    private $validator;

    /**
     * @var $Order
     */
    private $Order;

    /**
     * @var Delivery
     */
    private $Delivery;


    protected function setUp(): void
    {
        parent::setUp();

        $deliveryTimeRepository = $this->entityManager->getRepository(DeliveryTime::class);
        $this->validator = new DeliveryTimeValidator($deliveryTimeRepository);
        $this->Delivery = $this->createDelivery();
        $Customer = $this->createCustomer();
        $this->Order = static::getContainer()->get(Generator::class)->createOrder($Customer,[],$this->Delivery);
    }
    public function testInstance()
    {
        self::assertInstanceOf(DeliveryTimeValidator::class, $this->validator);
    }

    public function testValidateDeliveryTimeVisibleFalse()
    {
        $this->Order->getShippings()[0]->setTimeId($this->Delivery->getDeliveryTimes()[0]->getId());
        $this->Delivery->getDeliveryTimes()[0]->setVisible(false);
        $result = $this->validator->execute($this->Order, new PurchaseContext());
        self::assertTrue($result->isError());
    }

    /**
     * 配送方法を生成する.
     *
     * @param integer $delivery_time_max_pattern 配送時間の最大パターン数
     *
     * @return Delivery
     */
    public function createDelivery($delivery_time_max_pattern = 1)
    {
        $Member = $this->entityManager->find(\Eccube\Entity\Member::class, 2);
        $SaleType = $this->entityManager->find(\Eccube\Entity\Master\SaleType::class, 1);

        $faker = $this->getFaker();
        $Delivery = new Delivery();
        $Delivery
            ->setServiceName($faker->word)
            ->setName($faker->word)
            ->setDescription($faker->paragraph())
            ->setConfirmUrl($faker->url)
            ->setSortNo($faker->randomNumber(2))
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setCreator($Member)
            ->setSaleType($SaleType)
            ->setVisible(true);
        $this->entityManager->persist($Delivery);
        $this->entityManager->flush();

        $delivery_time_patten = $faker->numberBetween(1, $delivery_time_max_pattern);
        for ($i = 0; $i < $delivery_time_patten; $i++) {
            $DeliveryTime = new DeliveryTime();
            $DeliveryTime
                ->setDelivery($Delivery)
                ->setDeliveryTime($faker->word)
                ->setSortNo($i + 1)
                ->setVisible(true);
            $this->entityManager->persist($DeliveryTime);
            $this->entityManager->flush();
            $Delivery->addDeliveryTime($DeliveryTime);
        }

        $this->entityManager->flush();

        return $Delivery;
    }
}

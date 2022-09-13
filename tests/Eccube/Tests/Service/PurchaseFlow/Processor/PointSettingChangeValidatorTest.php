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

use Eccube\Entity\BaseInfo;
use Eccube\Service\PurchaseFlow\Processor\PointSettingChangeValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class PointSettingChangeValidatorTest extends EccubeTestCase
{
    /** @var BaseInfo */
    private $BaseInfo;

    /**
     * @var PointSettingChangeValidator
     */
    private $validator;

    /**
     * @var $Order
     */
    private $Order;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get(PointSettingChangeValidator::class);
        $Customer = $this->createCustomer();
        $this->Order = $this->createOrder($Customer);
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->BaseInfo->setOptionPoint(true);
        $this->Order->setUsePoint(100);
    }

    public function testInstance()
    {
        self::assertInstanceOf(PointSettingChangeValidator::class, $this->validator);
    }

    public function testValidatePointSettingDisable()
    {
        $CloneOrder = clone $this->Order;
        $this->BaseInfo->setOptionPoint(false);
        $result = $this->validator->execute($this->Order, new PurchaseContext($CloneOrder));
        self::assertTrue($result->isError());
    }
}

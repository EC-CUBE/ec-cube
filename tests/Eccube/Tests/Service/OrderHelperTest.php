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

namespace Eccube\Tests\Service;

use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Service\OrderHelper;
use Eccube\Tests\EccubeTestCase;

class OrderHelperTest extends EccubeTestCase
{
    /**
     * @var OrderHelper
     */
    protected $helper;

    public function setUp()
    {
        parent::setUp();

        $this->helper = self::$container->get(OrderHelper::class);
    }

    public function testNewInstance()
    {
        $this->assertInstanceOf(OrderHelper::class, $this->helper = self::$container->get(OrderHelper::class));
    }

    /**
     * 受注の作成日時より会員の更新日時が古い場合は注文者情報を更新しない.
     */
    public function testUpdateCustomerInfoOldCustomer()
    {
        $Order = new Order();
        $Order->setCreateDate((new \DateTime('today')));

        $Customer = new Customer();
        $Customer->setUpdateDate((new \DateTime('yesterday')));
        $Customer->setName01('hoge');

        $this->helper->updateCustomerInfo($Order, $Customer);
        self::assertNull($Order->getName01());
    }

    /**
     * 受注の作成日時より会員の更新日時が新しい場合は注文者情報を更新する.
     */
    public function testUpdateCustomerInfoNewCustomer()
    {
        $Order = new Order();
        $Order->setCreateDate((new \DateTime('yesterday')));

        $Customer = new Customer();
        $Customer->setUpdateDate((new \DateTime('today')));
        $Customer->setName01('hoge');

        $this->helper->updateCustomerInfo($Order, $Customer);
        self::assertNotNull($Order->getName01());
        self::assertSame($Order->getName01(), $Customer->getName01());
    }
}

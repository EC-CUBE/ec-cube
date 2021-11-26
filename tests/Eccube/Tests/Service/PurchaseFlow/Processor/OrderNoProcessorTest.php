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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\OrderNoProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class OrderNoProcessorTest extends EccubeTestCase
{
    /**
     * @dataProvider processDataProvider
     *
     * @param $orderNoFormat
     * @param $expected
     *
     * @throws \ReflectionException
     */
    public function testProcess($orderNoFormat, $expected)
    {
        $Order = new Order();

        // order_idを123に固定
        $rc = new \ReflectionClass(Order::class);
        $prop = $rc->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($Order, 123);

        $config = $this->createMock(EccubeConfig::class);
        $config->method('offsetGet')->willReturn($orderNoFormat);
        $config->method('get')->willReturn('Asia/Tokyo');
        $processor = new OrderNoProcessor($config, $this->entityManager->getRepository(\Eccube\Entity\Order::class));

        $processor->process($Order, new PurchaseContext());

        self::assertRegExp($expected, (string) $Order->getOrderNo());
    }

    public function processDataProvider()
    {
        return [
            ['', '/^123$/'],
            ['{yyyy}', '/^'.(new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('Y').'$/'],
            ['{yy}', '/^'.(new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('y').'$/'],
            ['{mm}', '/^'.(new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('m').'$/'],
            ['{dd}', '/^'.(new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('d').'$/'],
            ['{id}', '/^123$/'],
            ['{id,0}', '/^123$/'],
            ['{id,1}', '/^123$/'],
            ['{id,2}', '/^123$/'],
            ['{id,4}', '/^0123$/'],
            ['{id,10}', '/^0000000123$/'],
            ['{random}', '/^123$/'],
            ['{random,1}', '/^\d{1}$/'],
            ['{random,10}', '/^\d{10}$/'],
            ['{random_alnum}', '/^123$/'],
            ['{random_alnum,1}', '/^[[:alnum:]]{1}$/'],
            ['{random_alnum,10}', '/^[[:alnum:]]{10}$/'],
            ['order_no', '/order_no/'],
            ['{hoge}', '/123/'],
            ['ORDER_{yy}_{mm}_{dd}_{id,5}_{random,5}_{random_alnum,10}',
                '/^'.
                'ORDER_'.
                (new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('y').'_'.
                (new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('m').'_'.
                (new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('d').'_'.
                '00123_'.
                '\d{5}_'.
                '[[:alnum:]]{10}'.
                '$/', ],
        ];
    }
}

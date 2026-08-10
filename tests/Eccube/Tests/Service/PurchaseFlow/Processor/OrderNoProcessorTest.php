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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\Processor\OrderNoProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class OrderNoProcessorTest extends EccubeTestCase
{
    /**
     * @param $orderNoFormat
     * @param $expected
     *
     * @throws \ReflectionException
     */
    #[DataProvider(methodName: 'processDataProvider')]
    public function testProcess($orderNoFormat, $expected)
    {
        $Order = new Order();

        // order_idを123に固定
        $rc = new \ReflectionClass(Order::class);
        $prop = $rc->getProperty('id');
        $prop->setValue($Order, 123);

        $config = $this->createMock(EccubeConfig::class);
        $config->method('offsetGet')->willReturn($orderNoFormat);
        $config->method('get')->willReturn('Asia/Tokyo');
        $processor = new OrderNoProcessor($config, $this->entityManager->getRepository(Order::class));

        $processor->process($Order, new PurchaseContext());

        $this->assertMatchesRegularExpression($expected, (string) $Order->getOrderNo());
    }

    public static function processDataProvider(): \Iterator
    {
        yield ['', '/^123$/'];
        yield ['{yyyy}', '/^'.(new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('Y').'$/'];
        yield ['{yy}', '/^'.(new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('y').'$/'];
        yield ['{mm}', '/^'.(new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('m').'$/'];
        yield ['{dd}', '/^'.(new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('d').'$/'];
        yield ['{id}', '/^123$/'];
        yield ['{id,0}', '/^123$/'];
        yield ['{id,1}', '/^123$/'];
        yield ['{id,2}', '/^123$/'];
        yield ['{id,4}', '/^0123$/'];
        yield ['{id,10}', '/^0000000123$/'];
        yield ['{random}', '/^123$/'];
        yield ['{random,1}', '/^\d{1}$/'];
        yield ['{random,10}', '/^\d{10}$/'];
        yield ['{random_alnum}', '/^123$/'];
        yield ['{random_alnum,1}', '/^[[:alnum:]]{1}$/'];
        yield ['{random_alnum,10}', '/^[[:alnum:]]{10}$/'];
        yield ['{random_alpha,10}', '/^[[:alpha:]]{10}$/'];
        yield ['order_no', '/order_no/'];
        yield ['{hoge}', '/123/'];
        yield ['ORDER_{yy}_{mm}_{dd}_{id,5}_{random,5}_{random_alnum,10}',
            '/^'.
            'ORDER_'.
            (new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('y').'_'.
            (new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('m').'_'.
            (new \DateTime('now', new \DateTimeZone('Asia/Tokyo')))->format('d').'_'.
            '00123_'.
            '\d{5}_'.
            '[[:alnum:]]{10}'.
            '$/', ];
    }
}

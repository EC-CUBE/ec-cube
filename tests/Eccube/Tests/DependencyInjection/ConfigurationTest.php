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

namespace Eccube\Tests\DependencyInjection;

use Eccube\DependencyInjection\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends KernelTestCase
{
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $builder = $configuration->getConfigTreeBuilder();
        self::assertInstanceOf(TreeBuilder::class, $builder);
    }

    public function testProcessConfiguration()
    {
        $configs = [
            'eccube' => [
                'rate_limiter' => [
                    'entry' => [
                        'route' => 'entry',
                        'type' => 'ip',
                        'method' => ['POST'],
                        'params' => ['mode' => 'complete'],
                        'limit' => 10,
                        'interval' => '30 minutes',
                    ],
                    'shopping_confirm' => [
                        'route' => null,
                        'limit' => 10,
                        'interval' => '30 minutes',
                    ],
                ],
            ],
        ];
        $expected = [
            'rate_limiter' => [
                'limiters' => [
                    'entry' => [
                        'route' => 'entry',
                        'type' => ['ip'],
                        'method' => ['POST'],
                        'params' => ['mode' => 'complete'],
                        'limit' => 10,
                        'interval' => '30 minutes',
                    ],
                    'shopping_confirm' => [
                        'route' => null,
                        'limit' => 10,
                        'interval' => '30 minutes',
                        'type' => [],
                        'method' => ['POST'],
                        'params' => [],
                    ],
                ],
            ],
        ];

        $processor = new Processor();
        $configuration = new Configuration();
        $actual = $processor->processConfiguration($configuration, $configs);

        self::assertSame($expected, $actual);
    }
}

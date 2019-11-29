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

namespace Eccube\Tests\DependencyInjection\Compiler;

use Eccube\DependencyInjection\Compiler\PluginPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PluginPassTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        self::$container = new ContainerBuilder();
        self::$container->register(\Plugin\Sample\TestClass::class)
            ->setPublic(true)
            ->addTag('test_tag');

        self::$container->register(\Plugin\SamplePayment\TestClass::class)
            ->setPublic(true)
            ->addTag('test_tag');
    }

    public function testAllEnabled()
    {
        self::$container->setParameter('eccube.plugins.disabled', []);
        self::$container->addCompilerPass(new PluginPass());
        self::$container->compile();

        $def = self::$container->getDefinition(\Plugin\Sample\TestClass::class);
        self::assertTrue($def->hasTag('test_tag'));

        $def = self::$container->getDefinition(\Plugin\SamplePayment\TestClass::class);
        self::assertTrue($def->hasTag('test_tag'));
    }

    public function testSampleDisabled()
    {
        self::$container->setParameter('eccube.plugins.disabled', ['Sample']);
        self::$container->addCompilerPass(new PluginPass());
        self::$container->compile();

        $def = self::$container->getDefinition(\Plugin\Sample\TestClass::class);

        self::assertFalse($def->hasTag('test_tag'), 'Sampleはタグが外れる');
        $def = self::$container->getDefinition(\Plugin\SamplePayment\TestClass::class);
        self::assertTrue($def->hasTag('test_tag'), 'SamplePaymentは残っているはず');
    }
}

namespace Plugin\Sample;

class TestClass
{
}

namespace Plugin\SamplePayment;

class TestClass
{
}

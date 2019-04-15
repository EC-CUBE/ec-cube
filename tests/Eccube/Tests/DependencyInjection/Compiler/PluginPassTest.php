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
        $this->container = new ContainerBuilder();
        $this->container->register(\Plugin\Sample\TestClass::class)
            ->setPublic(true)
            ->addTag('test_tag');

        $this->container->register(\Plugin\SamplePayment\TestClass::class)
            ->setPublic(true)
            ->addTag('test_tag');
    }

    public function testAllEnabled()
    {
        $this->container->setParameter('eccube.plugins.disabled', []);
        $this->container->addCompilerPass(new PluginPass());
        $this->container->compile();

        $def = $this->container->getDefinition(\Plugin\Sample\TestClass::class);
        self::assertTrue($def->hasTag('test_tag'));

        $def = $this->container->getDefinition(\Plugin\SamplePayment\TestClass::class);
        self::assertTrue($def->hasTag('test_tag'));
    }

    public function testSampleDisabled()
    {
        $this->container->setParameter('eccube.plugins.disabled', ['Sample']);
        $this->container->addCompilerPass(new PluginPass());
        $this->container->compile();

        $def = $this->container->getDefinition(\Plugin\Sample\TestClass::class);

        self::assertFalse($def->hasTag('test_tag'), 'Sampleはタグが外れる');
        $def = $this->container->getDefinition(\Plugin\SamplePayment\TestClass::class);
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

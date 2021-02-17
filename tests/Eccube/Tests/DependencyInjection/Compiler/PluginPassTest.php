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

class PluginPassTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->register(\Plugin\Sample\TestClass::class)
            ->setPublic(true)
            ->addTag('test_tag');

        $this->containerBuilder->register(\Plugin\SamplePayment\TestClass::class)
            ->setPublic(true)
            ->addTag('test_tag');
    }

    public function testAllEnabled()
    {
        $this->containerBuilder->setParameter('eccube.plugins.disabled', []);
        $this->containerBuilder->addCompilerPass(new PluginPass());
        $this->containerBuilder->compile();

        $def = $this->containerBuilder->getDefinition(\Plugin\Sample\TestClass::class);
        self::assertTrue($def->hasTag('test_tag'));

        $def = $this->containerBuilder->getDefinition(\Plugin\SamplePayment\TestClass::class);
        self::assertTrue($def->hasTag('test_tag'));
    }

    public function testSampleDisabled()
    {
        $this->containerBuilder->setParameter('eccube.plugins.disabled', ['Sample']);
        $this->containerBuilder->addCompilerPass(new PluginPass());
        $this->containerBuilder->compile();

        $def = $this->containerBuilder->getDefinition(\Plugin\Sample\TestClass::class);

        self::assertFalse($def->hasTag('test_tag'), 'Sampleはタグが外れる');
        $def = $this->containerBuilder->getDefinition(\Plugin\SamplePayment\TestClass::class);
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

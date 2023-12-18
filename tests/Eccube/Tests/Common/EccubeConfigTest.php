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

namespace Eccube\Common;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class EccubeConfigTest extends TestCase
{
    private $container;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    protected function setup(): void
    {
        $this->container = new Container();
        $this->eccubeConfig = new EccubeConfig(new ContainerBag($this->container));
    }

    public function testGet()
    {
        $this->container->setParameter('hoge.fuga', true);
        self::assertSame(true, $this->eccubeConfig->get('hoge.fuga'));
    }

    public function testGetNotFound()
    {
        $this->expectException(ParameterNotFoundException::class);
        $this->eccubeConfig->get('hoge.fuga');
    }

    public function testHas()
    {
        self::assertFalse($this->eccubeConfig->has('hoge.fuga'));
        $this->container->setParameter('hoge.fuga', true);
        self::assertTrue($this->eccubeConfig->has('hoge.fuga'));
    }

    public function testOffsetGet()
    {
        $this->container->setParameter('hoge.fuga', true);
        self::assertSame(true, $this->eccubeConfig->offsetGet('hoge.fuga'));
    }

    public function testOffsetGetNotFound()
    {
        $this->expectException(ParameterNotFoundException::class);
        $this->eccubeConfig->offsetGet('hoge.fuga');
    }

    public function testOffsetExist()
    {
        self::assertFalse($this->eccubeConfig->offsetExists('hoge.fuga'));
        $this->container->setParameter('hoge.fuga', true);
        self::assertTrue($this->eccubeConfig->offsetExists('hoge.fuga'));
    }

    public function testOffsetSet()
    {
        $this->expectException(\Exception::class);
        $this->eccubeConfig->offsetSet('hoge.fuga', true);
    }

    public function testOffsetUnset()
    {
        $this->expectException(\Exception::class);
        $this->eccubeConfig->offsetUnset('hoge.fuga');
    }
}

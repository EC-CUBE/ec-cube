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
use Eccube\DependencyInjection\EccubeExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EccubeExtensionTest extends KernelTestCase
{
    private EccubeExtension $extension;

    public function setUp(): void
    {
        parent::setUp();
        $this->extension = new EccubeExtension();
    }

    public function testLoad()
    {
        $this->extension->load([], new ContainerBuilder());
        self::assertIsArray($this->extension->getProcessedConfigs());
    }

    public function testGetAlias()
    {
        self::assertSame('eccube', $this->extension->getAlias());
    }

    public function testGetConfiguration()
    {
        $container = new ContainerBuilder();
        $configuration = $this->extension->getConfiguration([], $container);
        self::assertInstanceOf(Configuration::class, $configuration);
    }
}

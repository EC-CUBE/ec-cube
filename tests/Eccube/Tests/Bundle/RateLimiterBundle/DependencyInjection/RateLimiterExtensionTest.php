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

namespace Eccube\Tests\Bundle\RateLimiterBundle;

use Eccube\Bundle\RateLimiterBundle\DependencyInjection\Configuration;
use Eccube\Bundle\RateLimiterBundle\DependencyInjection\RateLimiterExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RateLimiterExtensionTest extends KernelTestCase
{
    private RateLimiterExtension $extension;

    public function setUp(): void
    {
        parent::setUp();
        $this->extension = new RateLimiterExtension();
    }

    public function testLoad()
    {
        $this->extension->load([], new ContainerBuilder());
        self::assertIsArray($this->extension->getProcessedConfigs());
    }

    public function testGetAlias()
    {
        self::assertSame('eccube_rate_limiter', $this->extension->getAlias());
    }

    public function testGetConfiguration()
    {
        $container = new ContainerBuilder();
        $configuration = $this->extension->getConfiguration([], $container);
        self::assertInstanceOf(Configuration::class, $configuration);
    }
}

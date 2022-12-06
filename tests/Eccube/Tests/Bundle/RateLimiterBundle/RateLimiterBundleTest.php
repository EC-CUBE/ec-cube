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

use Eccube\Bundle\RateLimiterBundle\DependencyInjection\RateLimiterExtension;
use Eccube\Bundle\RateLimiterBundle\RateLimiterBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RateLimiterBundleTest extends KernelTestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new RateLimiterBundle();
        $extension = $bundle->getContainerExtension();
        self::assertInstanceOf(RateLimiterExtension::class, $extension);
    }
}

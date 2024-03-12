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

namespace Eccube;

use Eccube\DependencyInjection\EccubeExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EccubeBundleTest extends KernelTestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new EccubeBundle();
        $extension = $bundle->getContainerExtension();
        self::assertInstanceOf(EccubeExtension::class, $extension);
    }
}

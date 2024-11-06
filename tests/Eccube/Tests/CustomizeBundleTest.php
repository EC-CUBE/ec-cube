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

namespace Eccube\Tests;

use Customize\Bundle\CustomizeBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class CustomizeBundleTest extends KernelTestCase
{
    public function tearDown(): void
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__.'/../../../app/Customize/Bundle');
        $fs->remove(__DIR__.'/../../../app/Customize/Resource/config/bundles.php');

        parent::tearDown();
    }

    public function testContainsCustomizeBundle()
    {
        $fs = new Filesystem();
        $originDir = __DIR__.'/../../Fixtures/Customize';
        $targetDir = __DIR__.'/../../../app/Customize';
        $fs->mirror($originDir, $targetDir);

        $kernel = static::bootKernel();
        $bundleNames = [];
        foreach ($kernel->getBundles() as $bundle) {
            $bundleNames[] = get_class($bundle);
        }

        self::assertContains(CustomizeBundle::class, $bundleNames);
    }

    public function testNotContainsCustomizeBundle()
    {
        $kernel = static::bootKernel();
        $bundleNames = [];
        foreach ($kernel->getBundles() as $bundle) {
            $bundleNames[] = get_class($bundle);
        }

        self::assertNotContains(CustomizeBundle::class, $bundleNames);
    }
}

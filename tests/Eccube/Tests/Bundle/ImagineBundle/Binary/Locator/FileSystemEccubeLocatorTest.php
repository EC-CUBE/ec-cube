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

namespace Eccube\Tests\Bundle\ImagineBundle\Binary\Locator;

use Eccube\Bundle\ImagineBundle\Binary\Locator\FileSystemEccubeLocator;
use Liip\ImagineBundle\Binary\Locator\LocatorInterface;
use PHPUnit\Framework\TestCase;

class FileSystemEccubeLocatorTest extends TestCase
{
    public function testImplementsLocatorInterface(): void
    {
        $this->assertInstanceOf(LocatorInterface::class, new FileSystemEccubeLocator());
    }

    public function testGenerateAbsolutePath(): void
    {
        $locator = $this->getLocator([__DIR__ . '/../../../../../../Fixtures/Bundle/ImagineBundle/FileSystemLocator/root-01']);
        $absolute = $locator->locate('root-01/file.ext');

        $this->assertNotNull($absolute);
    }

    protected function getLocator(array $roots): LocatorInterface
    {
        return new FileSystemEccubeLocator($roots);
    }
}

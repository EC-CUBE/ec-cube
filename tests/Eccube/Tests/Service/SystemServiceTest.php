<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service;

use Eccube\Service\SystemService;

final class SystemServiceTest extends AbstractServiceTestCase
{
    public function testgetDbversion()
    {
        $version = static::getContainer()->get(SystemService::class)->getDbversion();

        $this->assertNotNull($version);
        $this->assertMatchesRegularExpression('/mysql|postgresql|sqlite/', strtolower($version));
    }
}

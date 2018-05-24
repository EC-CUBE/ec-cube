<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service;

use Eccube\Service\SystemService;

class SystemServiceTest extends AbstractServiceTestCase
{
    public function testgetDbversion()
    {
        $version = $this->container->get(SystemService::class)->getDbversion();

        $this->assertNotNull($version);
        $this->assertRegExp('/mysql|postgresql|sqlite/', strtolower($version));
    }
}

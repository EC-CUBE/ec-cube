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

use Eccube\Service\OrderHelper;
use Eccube\Tests\EccubeTestCase;

class OrderHelperTest extends EccubeTestCase
{
    public function testNewInstance()
    {
        $this->assertInstanceOf(OrderHelper::class, $this->helper = $this->container->get(OrderHelper::class));
    }
}

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

namespace Eccube\Tests\Web\Block;

use Eccube\Tests\Web\AbstractWebTestCase;

class CartControllerTest extends AbstractWebTestCase
{
    public function testRoutingCart()
    {
        $this->client->request('GET', '/block/cart');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}

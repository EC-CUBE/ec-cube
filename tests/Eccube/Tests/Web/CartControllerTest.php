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

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;

class CartControllerTest extends AbstractWebTestCase
{
    public function testRoutingCart()
    {
        $this->client->request('GET', '/cart');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingCartUp()
    {
        $this->client->request('PUT', '/cart/up/1',
            [Constant::TOKEN_NAME => 'dummy']
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    public function testRoutingCartDown()
    {
        $this->client->request('PUT', '/cart/down/1',
            [Constant::TOKEN_NAME => 'dummy']
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    public function testRoutingCartRemove()
    {
        $this->client->request('PUT', '/cart/remove/1',
            [Constant::TOKEN_NAME => 'dummy']
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }
}

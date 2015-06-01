<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Web;

class CartControllerTest extends AbstractWebTestCase
{

    public function testRoutingCart()
    {
        $client = $this->createClient();
        $client->request('GET', '/cart');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingCartAdd()
    {
        self::markTestSkipped();

        $client = $this->createClient();
        $client->request('POST', '/cart/add', array('product_class_id' => 1));

        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testRoutingCartUp()
    {
        self::markTestSkipped();

        $client = $this->createClient();
        $client->request('GET', '/cart/up/1');
        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testRoutingCartDown()
    {
        $client = $this->createClient();
        $client->request('GET', '/cart/down/1');
        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testRoutingCartSetQuantity()
    {
        self::markTestSkipped();

        $client = $this->createClient();
        $client->request('GET', '/cart/setQuantity/2/1');
        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testRoutingCartRemove()
    {
        $client = $this->createClient();
        $client->request('GET', '/cart/remove/1');
        $this->assertTrue($client->getResponse()->isRedirection());
    }
}

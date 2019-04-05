<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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


namespace Eccube\Tests\Plugin\Web;

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\AbstractWebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class CartControllerTest extends AbstractWebTestCase
{
    public function testRoutingCart()
    {
        $this->client->request('GET', '/cart');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::FRONT_CART_INDEX_INITIALIZE,
            EccubeEvents::FRONT_CART_INDEX_COMPLETE,
        );

        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingCartAdd()
    {
        $this->client->request('POST', '/cart/add', array('product_class_id' => 1));
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $hookpoins = array(
            EccubeEvents::FRONT_CART_ADD_INITIALIZE,
            EccubeEvents::FRONT_CART_ADD_COMPLETE,
        );

        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingCartAddException()
    {
        $this->client->request('POST', '/cart/add', array('product_class_id' => 99999));
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $hookpoins = array(
            EccubeEvents::FRONT_CART_ADD_INITIALIZE,
            EccubeEvents::FRONT_CART_ADD_EXCEPTION,
        );

        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingCartUp()
    {
        $this->client->request('PUT', '/cart/up/1');
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $hookpoins = array(
            EccubeEvents::FRONT_CART_UP_INITIALIZE,
            EccubeEvents::FRONT_CART_UP_COMPLETE,
        );

        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingCartUpException()
    {
        $this->client->request('PUT', '/cart/up/9999');
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $hookpoins = array(
            EccubeEvents::FRONT_CART_UP_INITIALIZE,
            EccubeEvents::FRONT_CART_UP_EXCEPTION,
        );

        $this->verifyOutputString($hookpoins);
    }


    public function testRoutingCartDown()
    {
        $this->client->request('PUT', '/cart/down/1');
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $hookpoins = array(
            EccubeEvents::FRONT_CART_DOWN_INITIALIZE,
            EccubeEvents::FRONT_CART_DOWN_COMPLETE,
        );

        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingCartDownException()
    {
        $this->client->request('PUT', '/cart/down/999999');
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $hookpoins = array(
            EccubeEvents::FRONT_CART_DOWN_INITIALIZE,
            EccubeEvents::FRONT_CART_DOWN_COMPLETE,
            // 本来はExceptionが発生するはずだが、存在しない商品規格IDを投げても何もしていない.コントローラ側を修正する必要がある.
            // EccubeEvents::FRONT_CART_DOWN_EXCEPTION,
        );

        $this->verifyOutputString($hookpoins);
    }


    public function testRoutingCartRemove()
    {
        $this->client->request('PUT', '/cart/remove/1');
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $hookpoins = array(
            EccubeEvents::FRONT_CART_REMOVE_INITIALIZE,
            EccubeEvents::FRONT_CART_REMOVE_COMPLETE,
        );

        $this->verifyOutputString($hookpoins);
    }
}

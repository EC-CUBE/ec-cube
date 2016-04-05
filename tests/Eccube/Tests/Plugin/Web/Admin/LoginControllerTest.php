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

namespace Eccube\Tests\Plugin\Web\Admin;

use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\AbstractWebTestCase;

class LoginControllerTest extends AbstractWebTestCase
{

    public function testRoutingAdminLogin()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('admin_login'));

        // ログイン
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $hookpoins = array(
            EccubeEvents::ADMIN_ADMIM_LOGIN_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingAdminLoginCheck()
    {
        $crawler = $this->client->request(
            'POST', $this->app['url_generator']->generate('admin_login'),
            array(
                'login_id' => 'admin',
                'password' => 'password',
                '_csrf_token' => 'dummy'
            )
        );

        $this->assertNotNull($this->app['security']->getToken(), 'ログインしているかどうか');

        $hookpoins = array(
            EccubeEvents::ADMIN_ADMIM_LOGIN_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingAdminLogin_ログインしていない場合は401エラーがかえる()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('admin_homepage'));

        // ログイン
        $this->assertEquals(
            401,
            $this->client->getResponse()->getStatusCode()
        );

        $hookpoins = array(
            EccubeEvents::ADMIN_ADMIM_LOGIN_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

}

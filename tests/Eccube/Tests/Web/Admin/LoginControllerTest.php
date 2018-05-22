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

namespace Eccube\Tests\Web\Admin;

use Eccube\Tests\Web\AbstractWebTestCase;

class LoginControllerTest extends AbstractWebTestCase
{
    public function testRoutingAdminLogin()
    {
        $this->client->request('GET', $this->generateUrl('admin_login'));

        // ログイン
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testRoutingAdminLoginCheck()
    {
        // see https://stackoverflow.com/a/38661340/4956633
        $this->client->request(
            'POST', $this->generateUrl('admin_login'),
            [
                'login_id' => 'admin',
                'password' => 'password',
                '_csrf_token' => 'dummy',
            ]
        );

        $this->assertNotNull($this->container->get('security.token_storage')->getToken(), 'ログインしているかどうか');
    }

    public function testRoutingAdminLogin_ログインしていない場合は401エラーがかえる()
    {
        $this->client->request('GET', $this->generateUrl('admin_homepage'));

        // ログイン
        $this->assertEquals(
            401,
            $this->client->getResponse()->getStatusCode()
        );
    }
}

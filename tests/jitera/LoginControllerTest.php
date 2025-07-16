<?php

/*
 * This file is based on EC-CUBE's test as a template for Jitera environment.
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Jitera;

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

        $this->assertNotNull(static::getContainer()->get('security.token_storage')->getToken(), 'ログインしているかどうか');
    }

    public function testRoutingAdminLogin未ログインの場合は302エラーがかえる()
    {
        $this->client->request('GET', $this->generateUrl('admin_homepage'));

        // ログイン
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );
    }
}
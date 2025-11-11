<?php

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

namespace Eccube\Tests\Web\Admin;

use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoginControllerTest extends AbstractWebTestCase
{
    public function testRoutingAdminLogin()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_login'));

        // ログイン
        $this->assertSame(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            (string) $this->client->getResponse()->getContent()
        );
    }

    public function testRoutingAdminLoginCheck()
    {
        // see https://stackoverflow.com/a/38661340/4956633
        $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_login'),
            [
                'login_id' => 'admin',
                'password' => 'password',
                '_csrf_token' => 'dummy',
            ]
        );

        $this->assertNotNull(static::getContainer()->get(TokenStorageInterface::class)->getToken(), 'ログインしているかどうか');
    }

    public function testRoutingAdminLoginÃ�グインしていない場合は302エラーがかえる()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_homepage'));

        // ログイン
        $this->assertSame(
            Response::HTTP_FOUND,
            $this->client->getResponse()->getStatusCode(),
            (string) $this->client->getResponse()->getContent()
        );
    }
}

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

namespace Eccube\Tests\Web\Admin\OAuth2;

use Eccube\Common\Constant;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class OAuth2ControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRoutingAdminOauth2Authorize_ログインしている場合は権限移譲確認画面を表示()
    {
        $this->client->request('GET', $this->generateUrl('admin_oauth2_authorize'));

        // ログイン
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testRoutingAdminOauth2Authorize_権限移譲を許可()
    {
        $parameters = [
            'oauth_authorization' => [
                'client_id' => 'dummy',
                'client_secret' => 'dummy',
                'redirect_uri' => 'dummy',
                'response_type' => 'dummy',
                'state' => 'dummy',
                'scope' => 'dummy',
                Constant::TOKEN_NAME => 'dummy',
            ],
        ];

        $this->client->request(
            'POST', $this->generateUrl('admin_oauth2_authorize'),
            $parameters
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    public function testRoutingAdminOauth2Authorize_権限移譲を許可_パラメータが足りない場合()
    {
        $parameters = [
            'oauth_authorization' => [
                'client_id' => '',
                'client_secret' => '',
                'redirect_uri' => '',
                'response_type' => '',
                'state' => '',
                'scope' => '',
                Constant::TOKEN_NAME => '',
            ],
        ];

        $this->client->request(
            'POST', $this->generateUrl('admin_oauth2_authorize'),
            $parameters
        );

        $this->assertFalse($this->client->getResponse()->isRedirection());
    }
}

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

namespace Eccube\Tests\Web\Admin\Setting\System;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Trikoder\Bundle\OAuth2Bundle\Manager\Doctrine\ClientManager;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;

class OAuth2ClientControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var ClientManager
     */
    protected $clientManager;

    /**
     * @{@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->clientManager = $this->container->get(ClientManager::class);
    }

    public function testRoutingAdminSettingSystemOAuth2Client()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_system_oauth'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminSettingSystemOAuth2ClientCreate()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_oauth_create_client'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminSettingSystemOAuth2ClientDelete()
    {
        // before
        $identifier = hash('md5', random_bytes(16));
        $secret = hash('sha512', random_bytes(32));
        $client = new Client($identifier, $secret);
        $this->clientManager->save($client);

        // main
        $redirectUrl = $this->generateUrl('admin_setting_system_oauth');
        $this->client->request('DELETE',
            $this->generateUrl('admin_setting_oauth_delete_client', ['identifier' => $identifier])
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
        $this->assertNull($this->clientManager->find($identifier));

        $crawler = $this->client->followRedirect();
        $this->assertRegExp('/削除しました/u', $crawler->filter('div.alert-success')->text());
    }

    public function testOAuth2ClientCreateSubmit()
    {
        // before
        $formData = $this->createFormData();

        // main
        $this->client->request('POST',
            $this->generateUrl('admin_setting_oauth_create_client'),
            [
                'admin_client' => $formData,
            ]
        );

        $client = $this->clientManager->find($formData['identifier']);

        $redirectUrl = $this->generateUrl('admin_setting_system_oauth');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $client->getIdentifier();
        $this->expected = $formData['identifier'];
        $this->verify();

        $crawler = $this->client->followRedirect();
        $this->assertRegExp('/保存しました/u', $crawler->filter('div.alert-success')->text());
    }

    public function testOAuth2ClientCreateSubmitFail()
    {
        // before
        $formData = $this->createFormData();
        $formData['identifier'] = '';

        // main
        $crawler = $this->client->request('POST',
            $this->generateUrl('admin_setting_oauth_create_client'),
            [
                'admin_client' => $formData,
            ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertRegExp('/入力されていません。/u', $crawler->filter('span.form-error-message')->text());
    }

    public function testOAuth2ClientDeleteIdentifierNotFound()
    {
        // before
        $identifier = hash('md5', random_bytes(16));

        // main
        $redirectUrl = $this->generateUrl('admin_setting_system_oauth');
        $this->client->request('DELETE',
            $this->generateUrl('admin_setting_oauth_delete_client', ['identifier' => $identifier])
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $crawler = $this->client->followRedirect();
        $this->assertRegExp('/既に削除されています/u', $crawler->filter('div.alert-danger')->text());
    }

    protected function createFormData()
    {
        return [
            '_token' => 'dummy',
            'identifier' => hash('md5', random_bytes(16)),
            'secret' => hash('sha512', random_bytes(32)),
            'scopes' => 'read',
            'redirect_uris' => 'http://127.0.0.1:8000/',
            'grants' => 'authorization_code',
        ];
    }
}

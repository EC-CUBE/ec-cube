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

namespace Eccube\Tests\Web\Admin\Store;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * @group cache-clear
 */
class PluginControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingAuthentication()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_store_authentication_setting')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testSubmit()
    {
        $form = [
            '_token' => 'dummy',
            'authentication_key' => 'abcxyzABCXYZ123098',
            'php_path' => '/usr/bin/php',
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_store_authentication_setting'),
            [
                'admin_authentication' => $form,
            ]
        );

        $this->expected = $form['php_path'];
        $this->actual = $this->entityManager->getRepository(\Eccube\Entity\BaseInfo::class)->get()->getPhpPath();
        $this->verify();
    }

    /**
     * 異常系を確認。正常系のインストールはE2Eテストの方で実施
     *
     * @dataProvider OwnerStoreInstallParam
     * 
     */
    public function testFailureInstall($param1, $param2, $message)
    {
        $form = [
            'pluginCode' => $param1,
            'version' => $param2,
        ];

        $crawler = $this->client->request('POST',
            $this->generateUrl('admin_store_plugin_api_install', $form),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        //　ダウンロードできないことを確認
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        //　ログを確認
        $this->assertContains($message, json_decode($this->client->getResponse()->getContent())->log);
    }
    
    /**
     * 異常系を確認。正常系のアップデートはE2Eテストの方で実施
     *
     * @dataProvider OwnerStoreUpgradeParam
     * 
     */
    public function testFailureUpgrade($param1, $param2, $message)
    {
        $form = [
            'pluginCode' => $param1,
            'version' => $param2,
        ];

        $crawler = $this->client->request('POST',
            $this->generateUrl('admin_store_plugin_api_upgrade', $form),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        //　ダウンロードできないことを確認
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());

        //　ログを確認
        $this->assertStringContainsString($message, implode(',', json_decode($this->client->getResponse()->getContent())->log));
    }

    /**
     * 異常系のテストケース
     */
    public function OwnerStoreInstallParam()
    {
        return [
            ['api42+symfony/yaml:5.3', '4.3.0', '有効な値ではありません。'],
            ['', '4.3.0','入力されていません。'],
        ];
    }

    /**
     * 異常系のテストケース
     */
    public function OwnerStoreUpgradeParam()
    {
        return [
            ['api42+symfony/yaml:5.3', '4.3.0', '有効な値ではありません。'],
            ['api42', '4.3.0 symfony/yaml:5.3', '有効な値ではありません。'],
            ['api42', '','入力されていません。'],
            ['', '4.3.0','入力されていません。'],
        ];
    }
}

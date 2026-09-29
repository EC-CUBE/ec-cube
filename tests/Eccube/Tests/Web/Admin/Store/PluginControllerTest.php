<?php

declare(strict_types=1);

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

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Plugin;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Group('cache-clear')]
final class PluginControllerTest extends AbstractAdminWebTestCase
{
    /**
     * プラグインの有効化/無効化は処理前にメンテナンスモードへ入る.
     * 解除はブラウザ側の JS が行うためテストでは残るので, 後続のテストに影響しないよう削除する.
     */
    protected function tearDown(): void
    {
        $maintenanceFilePath = static::getContainer()->getParameter('eccube_content_maintenance_file_path');
        if (file_exists($maintenanceFilePath)) {
            unlink($maintenanceFilePath);
        }
        parent::tearDown();
    }

    public function testRoutingAuthentication()
    {
        $this->client->request(
            Request::METHOD_GET,
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
            Request::METHOD_POST,
            $this->generateUrl('admin_store_authentication_setting'),
            [
                'admin_authentication' => $form,
            ]
        );

        $this->expected = $form['php_path'];
        $this->actual = $this->entityManager->getRepository(BaseInfo::class)->get()->getPhpPath();
        $this->verify();
    }

    /**
     * 既に有効なプラグインを有効化してもメンテナンスモードが解除されることを確認
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/7078
     */
    public function testEnableAlreadyEnabledReleasesMaintenance(): void
    {
        $session = $this->createSession($this->client);
        $Plugin = $this->createPlugin('AlreadyEnabled', true);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_store_plugin_enable', ['id' => $Plugin->getId()])
        );

        $redirectUrl = $this->generateUrl('admin_store_plugin');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // 「既に有効です。」でもメンテナンスモード解除のフラッシュが設定される.
        // これが無いとメンテナンスモードが解除されず, フロントが 503 のまま残る.
        $this->assertNotEmpty($session->getFlashBag()->get('eccube.admin.disable_maintenance'));
    }

    /**
     * 既に無効なプラグインを無効化してもメンテナンスモードが解除されることを確認
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/7078
     */
    public function testDisableAlreadyDisabledReleasesMaintenance(): void
    {
        $session = $this->createSession($this->client);
        $Plugin = $this->createPlugin('AlreadyDisabled', false);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_store_plugin_disable', ['id' => $Plugin->getId()])
        );

        $redirectUrl = $this->generateUrl('admin_store_plugin');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        // 「既に無効です。」でもメンテナンスモード解除のフラッシュが設定される
        $this->assertNotEmpty($session->getFlashBag()->get('eccube.admin.disable_maintenance'));
    }

    /**
     * 「既に有効/無効」の分岐は $Plugin->isEnabled() の判定だけで完結するため、
     * プラグイン本体のファイルを設置しなくても検証できる.
     */
    private function createPlugin(string $code, bool $enabled): Plugin
    {
        $Plugin = new Plugin();
        $Plugin
            ->setName($code)
            ->setCode($code)
            ->setVersion('1.0.0')
            ->setSource('')
            ->setEnabled($enabled)
            ->setInitialized(true);

        $this->entityManager->persist($Plugin);
        $this->entityManager->flush();

        return $Plugin;
    }

    /**
     * 異常系を確認。正常系のインストールはE2Eテストの方で実施
     *
     * @param mixed $param1
     * @param mixed $param2
     * @param mixed $message
     */
    #[DataProvider(methodName: 'OwnerStoreInstallParam')]
    public function testFailureInstall($param1, $param2, $message)
    {
        $form = [
            'pluginCode' => $param1,
            'version' => $param2,
        ];

        $this->client->request(Request::METHOD_POST,
            $this->generateUrl('admin_store_plugin_api_install', $form),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        //　ダウンロードできないことを確認
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        //　ログを確認
        $this->assertContains($message, json_decode($this->client->getResponse()->getContent())->log);
    }

    /**
     * 異常系を確認。正常系のアップデートはE2Eテストの方で実施
     *
     * @param mixed $param1
     * @param mixed $param2
     * @param mixed $message
     */
    #[DataProvider(methodName: 'OwnerStoreUpgradeParam')]
    public function testFailureUpgrade($param1, $param2, $message)
    {
        $form = [
            'pluginCode' => $param1,
            'version' => $param2,
        ];

        $this->client->request(Request::METHOD_POST,
            $this->generateUrl('admin_store_plugin_api_upgrade', $form),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        //　ダウンロードできないことを確認
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());

        //　ログを確認
        $this->assertStringContainsString($message, implode(',', json_decode($this->client->getResponse()->getContent())->log));
    }

    /**
     * 異常系のテストケース
     */
    public static function OwnerStoreInstallParam(): \Iterator
    {
        yield ['api42+symfony/yaml:5.3', '4.3.0', '有効な値ではありません。'];
        yield ['', '4.3.0', '入力されていません。'];
    }

    /**
     * 異常系のテストケース
     */
    public static function OwnerStoreUpgradeParam(): \Iterator
    {
        yield ['api42+symfony/yaml:5.3', '4.3.0', '有効な値ではありません。'];
        yield ['api42', '4.3.0 symfony/yaml:5.3', '有効な値ではありません。'];
        yield ['api42', '', '入力されていません。'];
        yield ['', '4.3.0', '入力されていません。'];
    }
}

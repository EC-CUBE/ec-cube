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

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * 認証設定画面(authentication_setting.twig)の注意喚起モーダルの回帰テスト.
 *
 * オーナーズストアでの認証キー二重発行を防ぐため, 「認証キー新規発行」ボタンは
 * CAPTCHA モーダルを直接開かず, 先に注意喚起モーダルを開く.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6208
 */
final class AuthenticationSettingConfirmModalTest extends AbstractAdminWebTestCase
{
    public function testTriggerButtonOpensConfirmModalInsteadOfCaptcha(): void
    {
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_store_authentication_setting')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 「認証キー新規発行」トリガーボタンが注意喚起モーダルを開くこと
        $trigger = $crawler->filter('button[data-bs-target="#authentication_key_confirm"]');
        $this->assertCount(1, $trigger, '新規発行ボタンは注意喚起モーダルを開くべき');

        // CAPTCHA モーダルを直接開くトリガーは残っていないこと（注意喚起を経由するため）
        $this->assertCount(
            0,
            $crawler->filter('button[data-bs-target="#captcha"]'),
            'CAPTCHA モーダルを直接開くボタンは無いべき（注意喚起モーダル経由に変更）'
        );
    }

    public function testConfirmModalIsRendered(): void
    {
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_store_authentication_setting')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 注意喚起モーダル本体と「発行手続きへ進む」ボタンが描画されていること
        $this->assertCount(1, $crawler->filter('#authentication_key_confirm'), '注意喚起モーダルが描画されるべき');
        $this->assertCount(1, $crawler->filter('#proceed_to_captcha'), '発行手続きへ進むボタンが描画されるべき');

        // 注意喚起文（翻訳済み）が出力されていること
        $expected = trans('admin.store.setting.get_api_key_confirm_info');
        $this->assertStringContainsString(
            $expected,
            (string) $this->client->getResponse()->getContent(),
            '注意喚起メッセージが出力されるべき'
        );
    }
}

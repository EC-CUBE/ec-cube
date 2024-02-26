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

namespace Plugin;

use AcceptanceTester;
use Page\Admin\ApiOauthEditPage;
use Page\Admin\ApiOauthPage;
use Page\Admin\ApiWebHookEditPage;
use Page\Admin\ApiWebHookPage;

/**
 * @group plugin
 * @group vaddy
 */
class PL08ApiCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function OAuth(AcceptanceTester $I)
    {
        ApiOauthPage::go($I)
            ->新規登録();

        $baseUrl = $I->getBaseUrl();
        ApiOauthEditPage::at($I)
            ->入力_クライアントID('testclient')
            ->入力_クライアントシークレット('testsecret')
            ->入力_スコープread()
            ->入力_リダイレクトURI("${baseUrl}/callback")
            ->登録();

        $I->amOnPage("/admin/authorize?response_type=code&client_id=testclient&redirect_uri=${baseUrl}/callback&scope=read&state=test");
        $I->click(['id' => 'oauth_authorization_approve']);

        $redirectUrl = $I->grabFromCurrentUrl();
        $code = preg_replace('/.*code=(.*)&.*/', '$1', $redirectUrl);

        $tokens = $I->executeJS("
            res = await fetch('/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body:
                    'grant_type=authorization_code' +
                    '&client_id=testclient' +
                    '&client_secret=testsecret' +
                    '&redirect_uri=${baseUrl}/callback' +
                    '&code=${code}'
            });
            return await res.json();
        ");

        $url = '/api?query={ product(id: 1) { id, name } }';
        $data = $I->executeJS("
            res = await fetch('${url}', {
                headers: {
                    'Authorization': 'Bearer ${tokens['access_token']}'
                }
            });
            return await res.json();
        ");

        $I->assertEquals('彩のジェラートCUBE', $data['data']['product']['name']);

        ApiOauthPage::go($I)
            ->期限切れトークン削除();

        ApiOauthPage::go($I)->削除(1);
    }

    public function WebHook(AcceptanceTester $I)
    {
        ApiWebHookPage::go($I)
            ->新規登録();

        ApiWebHookEditPage::at($I)
            ->入力_PayloadURL('http://localhost/hook')
            ->入力_シークレット('abcdefghijklmnop')
            ->登録();

        ApiWebHookPage::go($I)->編集(1);
        ApiWebHookEditPage::at($I)->登録();

        ApiWebHookPage::go($I)->削除(1);
    }
}

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

use Page\Admin\PasswordChangePage;
use Page\Admin\SystemMemberEditPage;
use Page\Admin\SystemMemberManagePage;

class EA02AuthenticationCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function authentication_パスワード認証(AcceptanceTester $I)
    {
        $I->wantTo('EA0201-UC01-T01 パスワード認証');

        // _before()で正常系はテスト済み
        // 異常系のテスト
        $I->logoutAsAdmin();

        $I->submitForm('#form1', [
            'login_id' => 'invalid',
            'password' => 'invalidpassword',
        ]);

        $I->see('ログインできませんでした。', '#form1 > div:nth-child(5) > span');
    }

    public function authentication_最終ログイン日時確認(AcceptanceTester $I)
    {
        $I->wantTo('EA0201-UC01-T01 最終ログイン日時確認');

        $I->click('header.c-headerBar div.c-headerBar__container a.c-headerBar__userMenu');
        $loginText = $I->grabTextFrom(['css' => '#page_admin_homepage div.popover .popover-body > p']);

        // Format Y/m/d only
        $lastLogin = preg_replace('/.*(\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}).*/s', '$1', $loginText);
        // 表示されるログイン日時では秒数がわからないため、タイミングによっては1分ちょっと変わる
        $now = new DateTime();
        $I->assertTrue((strtotime($now->format('Y/m/d')) - strtotime($lastLogin)) < 70, '最終ログイン日時が正しい');
    }

    public function authentication_パスワード変更(AcceptanceTester $I)
    {
        $org_password = 'password';
        $new_password = 'new_password';

        $I->wantTo('EA0201-UC01-T02 パスワード認証機能(パスワード変更)');
        SystemMemberManagePage::go($I)
            ->編集(1);
        SystemMemberEditPage::at($I)
            ->入力_パスワード($new_password, $new_password)
            ->登録();
        $I->see('保存しました', '.alert-success');

        $I->logoutAsAdmin();
        $I->submitForm('#form1', [
            'login_id' => 'admin',
            'password' => 'password',
        ]);
        $I->see('ログインできませんでした。', '#form1 > div:nth-child(5) > span');

        $I->loginAsAdmin('admin', $new_password);

        $I->wantTo('EA0201-UC01-T03 パスワード認証機能(パスワード変更：画面右上)');

        PasswordChangePage::go($I)
            ->入力_パスワード($new_password, $org_password, $org_password)
            ->登録();
        $I->see('パスワードを更新しました', '.alert-success');

        $I->logoutAsAdmin();
        $I->submitForm('#form1', [
            'login_id' => 'admin',
            'password' => $new_password,
        ]);
        $I->see('ログインできませんでした。', '#form1 > div:nth-child(5) > span');

        $I->loginAsAdmin();
    }

    public function authentication_非稼働_削除(AcceptanceTester $I)
    {
        $I->wantTo('EA0201-UC01-T04_パスワード認証機能(非稼働)');
        // 非稼働ユーザ作成
        // ログインできないことを確認
        // 稼働にする
        // ログインできることを確認

        $I->wantTo('EA0201-UC01-T05_パスワード認証機能(メンバー削除)');
        // 削除
        // ログインできないことを確認
    }
}

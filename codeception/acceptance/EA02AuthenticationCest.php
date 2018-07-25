<?php

/**
 * @group admin
 * @group admin01
 * @group authentication
 * @group ea2
 */
class EA02AuthenticationCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function authentication_パスワード認証(\AcceptanceTester $I)
    {
        $I->wantTo('EA0201-UC01-T01 パスワード認証');

        // _before()で正常系はテスト済み
        // 異常系のテスト
        $I->logoutAsAdmin();

        $I->submitForm('#form1', [
            'login_id' => "invalid",
            'password' => "invalidpassword"
        ]);

        $I->see('ログインできませんでした。', '.login-box #form1 .text-danger');
    }

    public function authentication_最終ログイン日時確認(\AcceptanceTester $I)
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
}

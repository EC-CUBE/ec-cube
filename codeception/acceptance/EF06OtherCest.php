<?php

use Codeception\Util\Fixtures;

/**
 * @group front
 * @group other
 * @group ef6
 */
class EF06OtherCest
{
    public function _before(\AcceptanceTester $I)
    {
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function other_ログイン正常(\AcceptanceTester $I)
    {
        $I->wantTo('EF0601-UC01-T01 ログイン 正常パターン');
        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');
    }

    public function other_ログイン異常1(\AcceptanceTester $I)
    {
        $I->wantTo('EF0601-UC01-T02 ログイン 異常パターン(仮会員)');
        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer(null, false);

        $I->amOnPage('/mypage/login');
        $I->submitForm('#login_mypage', [
            'login_email' => $customer->getEmail(),
            'login_pass' => 'password'
        ]);

        $I->see('ログインできませんでした。', 'div.ec-login p.ec-errorMessage');
    }

    public function other_ログイン異常2(\AcceptanceTester $I)
    {
        $I->wantTo('EF0601-UC01-T03 ログイン 異常パターン(入力ミス)');
        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer(null, false);

        $I->amOnPage('/mypage/login');
        $I->submitForm('#login_mypage', [
            'login_email' => $customer->getEmail().'.bad',
            'login_pass' => 'password'
        ]);

        $I->see('ログインできませんでした。', 'div.ec-login p.ec-errorMessage');
    }

    public function other_パスワード再発行(\AcceptanceTester $I)
    {
        $I->wantTo('EF0602-UC01-T01 パスワード再発行');
        $I->logoutAsMember();

        // TOPページ→ログイン（「ログイン情報をお忘れですか？」リンクを押下する）→パスワード再発行
        $I->amOnPage('/mypage/login');
        //$I->click('ログイン情報をお忘れですか', '#login_mypage #login_box .btn_area ul li a');
        $I->amOnPage('/forgot');

        // TOPページ>ログイン>パスワード再発行
        $I->see('パスワードの再発行', 'div.ec-pageHeader h1');

        // メールアドレスを入力する
        // 「次のページへ」ボタンを押下する
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->resetEmails();
        $I->submitForm('#form1', [
            'login_email' => $customer->getEmail()
        ]);
        $I->see('パスワード発行メールの送信 完了', 'div.ec-pageHeader h1');

        $I->seeEmailCount(1);
        $I->seeInLastEmailSubjectTo($customer->getEmail(), 'パスワード変更のご確認');

        $url = $I->grabFromLastEmailTo($customer->getEmail(), '@/forgot/reset/(.*)@');

        $I->resetEmails();
        $I->amOnPage($url);
        $I->see('パスワード再発行(再設定ページ)', 'div.ec-pageHeader h1');

        $password = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 20);

        // メルアド・新パスワード設定
        $I->submitForm('#form1',[
            'login_email' => $customer->getEmail(),
            'password[first]' => $password,
            'password[second]' => $password
        ]);

        $I->see('ログイン', 'div.ec-pageHeader h1');
        $I->loginAsMember($customer->getEmail(), $password);
    }

    public function other_ログアウト(\AcceptanceTester $I)
    {
        $I->wantTo('EF0603-UC01-T01 ログアウト');
        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        $I->logoutAsMember();
    }

    public function other_当サイトについて(\AcceptanceTester $I)
    {
        $I->wantTo('EF0604-UC01-T01 当サイトについて');
        $I->amOnPage('/');

        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(1) a');
        $I->see('当サイトについて', 'div.ec-pageHeader h1');
        $baseinfo = Fixtures::get('baseinfo');
        $I->see($baseinfo->getShopName(), '#help_about_box__shop_name');
    }

    public function other_プライバシーポリシー(\AcceptanceTester $I)
    {
        $I->wantTo('EF0605-UC01-T01 プライバシーポリシー');
        $I->amOnPage('/');

        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(2) a');
        $I->see('プライバシーポリシー', 'div.ec-pageHeader h1');
        $I->see('個人情報保護の重要性に鑑み、「個人情報の保護に関する法律」及び本プライバシーポリシーを遵守し、お客さまのプライバシー保護に努めます。', 'div.ec-layoutRole__main p:nth-child(1)');
    }

    public function other_特定商取引法に基づく表記(\AcceptanceTester $I)
    {
        $I->wantTo('EF0606-UC01-T01 特定商取引法に基づく表記');
        $I->amOnPage('/');

        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(3) a');
        $I->see('特定商取引法に基づく表記', 'div.ec-pageHeader h1');
    }

    public function other_お問い合わせ1(\AcceptanceTester $I)
    {
        $I->wantTo('EF0607-UC01-T01 お問い合わせ');
        $I->amOnPage('/');
        $I->resetEmails();
        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;
        $BaseInfo = Fixtures::get('baseinfo');

        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(4) a');
        $I->see('お問い合わせ', 'div.ec-pageHeader h1');

        $I->fillField(['id' => 'contact_name_name01'], '姓');
        $I->fillField(['id' => 'contact_name_name02'], '名');
        $I->fillField(['id' => 'contact_kana_kana01'], 'セイ');
        $I->fillField(['id' => 'contact_kana_kana02'], 'メイ');
        $I->fillField(['id' => 'contact_postal_code'], '530-0001');
        $I->selectOption(['id' => 'contact_address_pref'], ['value' => '27']);
        $I->fillField(['id' => 'contact_address_addr01'], '大阪市北区');
        $I->fillField(['id' => 'contact_address_addr02'], '梅田2-4-9 ブリーゼタワー13F');
        $I->fillField(['id' => 'contact_phone_number'], '111-111-111');
        $I->fillField(['id' => 'contact_email'], $new_email);
        $I->fillField(['id' => 'contact_contents'], 'お問い合わせ内容の送信');
        $I->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');

        $I->see('お問い合わせ', 'div.ec-pageHeader h1');
        $I->click('div.ec-contactConfirmRole div.ec-RegisterRole__actions button.ec-blockBtn--action');

        // 完了ページ
        $I->see('お問い合わせ完了', 'div.ec-pageHeader h1');

        // メールチェック
        $I->seeEmailCount(2);
        foreach (array($new_email, $BaseInfo->getEmail01()) as $email) {
            $I->seeInLastEmailSubjectTo($email, 'お問い合わせを受け付けました');
            $I->seeInLastEmailTo($email, '姓 名 様');
            $I->seeInLastEmailTo($email, 'お問い合わせ内容の送信');
        }
    }
}

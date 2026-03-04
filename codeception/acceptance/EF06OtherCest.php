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

use Codeception\Util\Fixtures;
use Page\Admin\PageEditPage;
use Page\Admin\PageManagePage;
use Page\Admin\ProductEditPage;
use Page\Admin\ProductManagePage;
use Page\Admin\ShopSettingPage;

/**
 * @group front
 * @group other
 * @group ef6
 */
class EF06OtherCest
{
    public function other_ログイン正常(AcceptanceTester $I)
    {
        $I->wantTo('EF0601-UC01-T01 ログイン 正常パターン');
        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');
    }

    public function other_ログイン異常1(AcceptanceTester $I)
    {
        $I->wantTo('EF0601-UC01-T02 ログイン 異常パターン(仮会員)');
        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer(null, false);

        $I->amOnPage('/mypage/login');
        $I->submitForm('#login_mypage', [
            'login_email' => $customer->getEmail(),
            'login_pass' => 'password',
        ]);

        $I->see('ログインできませんでした。', 'div.ec-login p.ec-errorMessage');
    }

    public function other_ログイン異常2(AcceptanceTester $I)
    {
        $I->wantTo('EF0601-UC01-T03 ログイン 異常パターン(入力ミス)');
        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer(null, false);

        $I->amOnPage('/mypage/login');
        $I->submitForm('#login_mypage', [
            'login_email' => $customer->getEmail().'.bad',
            'login_pass' => 'password',
        ]);

        $I->see('ログインできませんでした。', 'div.ec-login p.ec-errorMessage');
    }

    /**
     * @group vaddy
     */
    public function other_パスワード再発行(AcceptanceTester $I)
    {
        $I->wantTo('EF0602-UC01-T01 パスワード再発行');
        $I->logoutAsMember();

        // TOPページ→ログイン（「ログイン情報をお忘れですか？」リンクを押下する）→パスワード再発行
        $I->amOnPage('/mypage/login');
        $I->click('#login_mypage a:first-child');

        // TOPページ>ログイン>パスワード再発行
        $I->see('パスワードの再発行', 'div.ec-pageHeader h1');

        // メールアドレスを入力する
        // 「次のページへ」ボタンを押下する
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->resetEmails();
        $I->submitForm('#form1', [
            'login_email' => $customer->getEmail(),
        ]);
        $I->see('パスワードの再発行(メール送信)', 'div.ec-pageHeader h1');

        $I->seeEmailCount(1);
        $I->seeInLastEmailSubjectTo($customer->getEmail(), 'パスワード変更のご確認');

        $url = $I->grabFromLastEmailTo($customer->getEmail(), '@/forgot/reset/(.*)@');

        $I->resetEmails();
        $I->amOnPage($url);
        $I->see('パスワード再発行(再設定)', 'div.ec-pageHeader h1');

        $password = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 20);

        // メルアド・新パスワード設定
        $I->submitForm('#form1', [
            'login_email' => $customer->getEmail(),
            'password[first]' => $password,
            'password[second]' => $password,
        ]);

        $I->see('ログイン', 'div.ec-pageHeader h1');
        $I->loginAsMember($customer->getEmail(), $password);
    }

    /**
     * @group vaddy
     */
    public function other_ログアウト(AcceptanceTester $I)
    {
        $I->wantTo('EF0603-UC01-T01 ログアウト');
        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        $I->logoutAsMember();
    }

    /**
     * @group vaddy
     */
    public function other_当サイトについて(AcceptanceTester $I)
    {
        $I->wantTo('EF0604-UC01-T01 当サイトについて');
        $I->amOnPage('/');

        $I->scrollTo('.ec-footerNavi .ec-footerNavi__link:nth-child(1) a', 0, 200);
        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(1) a');
        $I->see('当サイトについて', 'div.ec-pageHeader h1');
        $baseinfo = Fixtures::get('baseinfo');
        $I->see($baseinfo->getShopName(), '#help_about_box__shop_name');
    }

    /**
     * @group vaddy
     */
    public function other_プライバシーポリシー(AcceptanceTester $I)
    {
        $I->wantTo('EF0605-UC01-T01 プライバシーポリシー');
        $I->amOnPage('/');

        $I->scrollTo('.ec-footerNavi .ec-footerNavi__link:nth-child(2) a', 0, 200);
        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(2) a');
        $I->see('プライバシーポリシー', 'div.ec-pageHeader h1');
        $I->see('個人情報保護の重要性に鑑み、「個人情報の保護に関する法律」及び本プライバシーポリシーを遵守し、お客さまのプライバシー保護に努めます。', 'div.ec-layoutRole__main p:nth-child(1)');
    }

    /**
     * @group vaddy
     */
    public function other_特定商取引法に基づく表記(AcceptanceTester $I)
    {
        $I->wantTo('EF0606-UC01-T01 特定商取引法に基づく表記');
        $I->amOnPage('/');

        $I->scrollTo('.ec-footerNavi .ec-footerNavi__link:nth-child(3) a', 0, 200);
        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(3) a');
        $I->see('特定商取引法に基づく表記', 'div.ec-pageHeader h1');
    }

    /**
     * @group vaddy
     */
    public function other_お問い合わせ1(AcceptanceTester $I)
    {
        $I->wantTo('EF0607-UC01-T01 お問い合わせ');
        $I->amOnPage('/');
        $I->resetEmails();
        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;
        $BaseInfo = Fixtures::get('baseinfo');

        $I->scrollTo('.ec-footerNavi .ec-footerNavi__link:nth-child(4) a', 0, 200);
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
        $I->see('お問い合わせ(完了)', 'div.ec-pageHeader h1');

        // メールチェック
        $message = $I->lastMessage();
        $I->assertCount(2, $message->getRecipients(), 'Bcc で管理者にも送信するので宛先アドレスは2つ');
        $I->seeEmailCount(1);
        foreach ([$new_email, $BaseInfo->getEmail01()] as $email) {
            $I->seeInLastEmailSubjectTo($email, 'お問い合わせを受け付けました');
            $I->seeInLastEmailTo($email, '姓 名 様');
            $I->seeInLastEmailTo($email, 'お問い合わせ内容の送信');
        }
    }

    public function other_お問い合わせ2(AcceptanceTester $I)
    {
        $I->wantTo('EF0607-UC01-T02 お問い合わせ 戻るボタン');
        $I->amOnPage('/');
        $I->resetEmails();
        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;
        $BaseInfo = Fixtures::get('baseinfo');

        $I->scrollTo('.ec-footerNavi .ec-footerNavi__link:nth-child(4) a', 0, 200);
        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(4) a');
        $I->see('お問い合わせ', 'div.ec-pageHeader h1');

        $I->fillField(['id' => 'contact_name_name01'], '姓');
        $I->fillField(['id' => 'contact_name_name02'], '名');
        $I->fillField(['id' => 'contact_kana_kana01'], 'セイ');
        $I->fillField(['id' => 'contact_kana_kana02'], 'メイ');
        $I->fillField(['id' => 'contact_postal_code'], '530-0001');
        $I->selectOption(['id' => 'contact_address_pref'], ['value' => '27']);
        $I->wait(1); // 郵便番号の自動入力を待つ
        $I->fillField(['id' => 'contact_address_addr02'], '2-4-9 ブリーゼタワー13F');
        $I->fillField(['id' => 'contact_phone_number'], '111-111-111');
        $I->fillField(['id' => 'contact_email'], $new_email);
        $I->fillField(['id' => 'contact_contents'], 'お問い合わせ内容の送信');
        $I->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');

        // 確認画面 → 戻る
        $I->see('お問い合わせ', 'div.ec-pageHeader h1');
        $I->click('div.ec-contactConfirmRole div.ec-RegisterRole__actions button.ec-blockBtn--cancel');

        // 入力画面 → フォーム入力内容の再チェック
        $I->see('お問い合わせ', 'div.ec-pageHeader h1');
        $I->seeInFormFields('.ec-contactRole form', [
            'contact[name][name01]' => '姓',
            'contact[name][name02]' => '名',
            'contact[postal_code]' => '5300001',
            'contact[address][pref]' => 27,
            'contact[address][addr01]' => '大阪市北区梅田',
            'contact[phone_number]' => '111111111',
            'contact[contents]' => 'お問い合わせ内容の送信',
        ]);
        $I->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');

        // 確認画面 → 送信
        $I->see('お問い合わせ', 'div.ec-pageHeader h1');
        $I->click('div.ec-contactConfirmRole div.ec-RegisterRole__actions button.ec-blockBtn--action');

        // 完了ページ
        $I->see('お問い合わせ(完了)', 'div.ec-pageHeader h1');

        // メールチェック
        $message = $I->lastMessage();
        $I->assertCount(2, $message->getRecipients(), 'Bcc で管理者にも送信するので宛先アドレスは2つ');
        $I->seeEmailCount(1);
        foreach ([$new_email, $BaseInfo->getEmail01()] as $email) {
            $I->seeInLastEmailSubjectTo($email, 'お問い合わせを受け付けました');
            $I->seeInLastEmailTo($email, '姓 名 様');
            $I->seeInLastEmailTo($email, 'お問い合わせ内容の送信');
        }
    }

    public function other_お問い合わせ_異常(AcceptanceTester $I)
    {
        $I->wantTo('EF0607-UC01-T03 お問い合わせ 異常');
        $I->amOnPage('/');

        $I->scrollTo('.ec-footerNavi .ec-footerNavi__link:nth-child(4) a', 0, 200);
        $I->click('.ec-footerNavi .ec-footerNavi__link:nth-child(4) a');
        $I->see('お問い合わせ', 'div.ec-pageHeader h1');

        $I->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');

        $I->see('入力されていません', '.ec-contactRole .error .ec-errorMessage:last-child');
    }

    public function other_サイトマップ(AcceptanceTester $I)
    {
        $I->wantTo('EF0608-UC01-T01_サイトマップ');
        $I->amOnPage('/sitemap.xml');

        $I->see('/sitemap_page.xml');
        $I->see('/sitemap_category.xml');
        $I->see('/sitemap_product_1.xml');
    }

    public function other_サイトマップ_ページ(AcceptanceTester $I)
    {
        $I->wantTo('EF0608-UC01-T03_サイトマップ(ページ)');
        $I->loginAsAdmin();

        $sitemapUrl = '/sitemap_page.xml';
        $topPageLoc = '<loc>'.$I->getBaseUrl().'/</loc>';

        // 表示確認
        $I->amOnPage($sitemapUrl);
        $I->see($topPageLoc);

        // メタ設定 → robots noindex → 非表示になる
        PageManagePage::go($I)->ページ編集('TOPページ');
        PageEditPage::at($I)->入力_メタ_robot('noindex')->登録();
        $I->amOnPage($sitemapUrl);
        $I->dontSee($topPageLoc);

        // メタ設定 → robots none → 非表示になる
        PageManagePage::go($I)->ページ編集('TOPページ');
        PageEditPage::at($I)->入力_メタ_robot('none')->登録();
        $I->amOnPage($sitemapUrl);
        $I->dontSee($topPageLoc);

        // メタ設定 → robots 解除 → 表示される
        PageManagePage::go($I)->ページ編集('TOPページ');
        PageEditPage::at($I)->入力_メタ_robot('')->登録();
        $I->amOnPage($sitemapUrl);
        $I->see($topPageLoc);
    }

    public function other_サイトマップ_カテゴリ(AcceptanceTester $I)
    {
        $I->wantTo('EF0608-UC01-T03_サイトマップ(カテゴリ)');
        $I->amOnPage('/sitemap_category.xml');

        $I->see('/products/list?category_id=1');
    }

    public function other_サイトマップ_商品(AcceptanceTester $I)
    {
        $I->wantTo('EF0608-UC01-T04_サイトマップ(商品)');
        $I->loginAsAdmin();

        ProductManagePage::go($I);
        $productId = 2;
        $productLoc = '<loc>'.$I->getBaseUrl().'/products/detail/'.$productId.'</loc>';
        $productEditUrl = "/admin/product/product/{$productId}/edit";
        $sitemapUrl = '/sitemap_product_1.xml';

        // 非公開商品は表示されない
        $I->amOnPage($productEditUrl);
        ProductEditPage::at($I)->入力_非公開()->登録();
        $I->amOnPage($sitemapUrl);
        $I->dontSee($productLoc);

        // 廃止商品は表示されない
        $I->amOnPage($productEditUrl);
        ProductEditPage::at($I)->入力_廃止()->登録();
        $I->amOnPage($sitemapUrl);
        $I->dontSee($productLoc);

        // 在庫なし商品の準備
        $I->amOnPage($productEditUrl);
        ProductEditPage::at($I)->入力_在庫数(0)->登録();
        $I->see('保存しました', ProductEditPage::$登録結果メッセージ);

        // 公開・在庫切れ商品を表示しない
        $I->amOnPage($productEditUrl);
        ProductEditPage::at($I)->入力_公開()->登録();
        // 在庫切れ商品の非表示設定
        $page = ShopSettingPage::go($I)
            ->設定_在庫切れ商品の非表示(true);
        $I->amOnPage($sitemapUrl);
        $I->dontSee($productLoc);

        // 公開・在庫切れ商品は表示する
        $page = ShopSettingPage::go($I)
            ->設定_在庫切れ商品の非表示(false);
        $I->amOnPage($sitemapUrl);
        $I->see($productLoc);
    }
}

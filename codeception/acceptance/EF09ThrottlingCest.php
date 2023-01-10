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
use Page\Front\CartPage;
use Page\Front\ProductDetailPage;
use Page\Front\ShoppingConfirmPage;
use Page\Front\ShoppingLoginPage;
use Page\Front\ShoppingNonmemberPage;
use Page\Front\ShoppingPage;

/**
 * @group throttling
 * @group ef9
 *
 * テスト間での影響を避けるため、各テストはそれぞれ単体で実行してください。
 * また、テスト実行前に、スロットリングの結果をクリアしてください。
 * bin/console cache:pool:clear rate_limiter.cache cache.rate_limiter --env=<APP_ENV>
 */
class EF09ThrottlingCest
{
    public function フロント画面ログイン_IP(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T01_フロント画面ログイン(IP)');

        $faker = Fixtures::get('faker');

        for ($i = 0; $i < 25; $i++) {
            $I->expect('ログインに失敗します：'.$i);
            $email = microtime(true).'.'.$faker->safeEmail;
            $this->failLogin($I, $email, 'password');
            $I->see('ログインできませんでした。', 'p.ec-errorMessage');
            $I->see('入力内容に誤りがないかご確認ください。', 'p.ec-errorMessage');
        }

        $I->expect('試行回数上限を超過します');
        $email = microtime(true).'.'.$faker->safeEmail;
        $this->failLogin($I, $email, 'password');
        $I->see('ログイン試行回数が多すぎます。30分後に再度お試しください。', 'p.ec-errorMessage');
    }

    public function フロント画面ログイン_会員(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T02_フロント画面ログイン(会員)');

        $faker = Fixtures::get('faker');
        $email = microtime(true).'.'.$faker->safeEmail;

        for ($i = 0; $i < 5; $i++) {
            $I->expect('ログインに失敗します：'.$i);
            $this->failLogin($I, $email, 'password');
            $I->see('ログインできませんでした。', 'p.ec-errorMessage');
            $I->see('入力内容に誤りがないかご確認ください。', 'p.ec-errorMessage');
        }

        $I->expect('試行回数上限を超過します');
        $this->failLogin($I, $email, 'password');
        $I->see('ログイン試行回数が多すぎます。30分後に再度お試しください。', 'p.ec-errorMessage');
    }

    private function failLogin(AcceptanceTester $I, $email, $password)
    {
        $I->amOnPage('/mypage/login');
        $I->submitForm('#login_mypage', [
            'login_email' => $email,
            'login_pass' => $password,
        ]);
    }

    public function 管理画面ログイン_IP(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T03_管理画面ログイン(IP)');

        $faker = Fixtures::get('faker');

        for ($i = 0; $i < 25; $i++) {
            $I->expect('ログインに失敗します：'.$i);
            $email = microtime(true).'.'.$faker->safeEmail;
            $this->failLoginAsAdmin($I, $email, 'password');
            $I->see('ログインできませんでした。', 'span.text-danger');
            $I->see('入力内容に誤りがないかご確認ください。', 'span.text-danger');
        }

        $I->expect('試行回数上限を超過します');
        $email = microtime(true).'.'.$faker->safeEmail;
        $this->failLoginAsAdmin($I, $email, 'password');
        $I->see('ログイン試行回数が多すぎます。30分後に再度お試しください。', 'span.text-danger');
    }

    public function 管理画面ログイン_会員(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T04_管理画面ログイン(会員)');

        $faker = Fixtures::get('faker');
        $email = microtime(true).'.'.$faker->safeEmail;

        for ($i = 0; $i < 5; $i++) {
            $I->expect('ログインに失敗します：'.$i);
            $this->failLoginAsAdmin($I, $email, 'password');
            $I->see('ログインできませんでした。', 'span.text-danger');
            $I->see('入力内容に誤りがないかご確認ください。', 'span.text-danger');
        }

        $I->expect('試行回数上限を超過します');
        $this->failLoginAsAdmin($I, $email, 'password');
        $I->see('ログイン試行回数が多すぎます。30分後に再度お試しください。', 'span.text-danger');
    }

    private function failLoginAsAdmin(AcceptanceTester $I, $loginId, $password)
    {
        $I->goToAdminPage();

        $I->submitForm('#form1', [
            'login_id' => $loginId,
            'password' => $password,
        ]);
    }

    public function 会員登録(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T05_会員登録');

        for ($i = 0; $i < 5; $i++) {
            $I->expect('会員登録を行います：'.$i);
            \Page\Front\EntryPage::go($I)
                ->新規会員登録();
            $I->see('現在、仮会員の状態です。', 'p.ec-reportDescription');
        }

        $I->expect('試行回数上限を超過します');
        \Page\Front\EntryPage::go($I)
            ->新規会員登録();
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    public function 問い合わせ(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T06_問い合わせ');

        for ($i = 0; $i < 5; $i++) {
            $I->expect('問い合わせを行います：'.$i);
            $this->contact($I);
            $this->contactConfirm($I);
            $this->contactComplete($I);
            $I->see('お問い合わせ(完了)', 'div.ec-pageHeader h1');
        }

        $I->expect('試行回数上限を超過します');
        $this->contact($I);
        $this->contactConfirm($I);
        $this->contactComplete($I);
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    private function contact(AcceptanceTester $I)
    {
        $I->amOnPage('/contact');
    }

    private function contactConfirm(AcceptanceTester $I)
    {
        $faker = Fixtures::get('faker');
        $email = microtime(true).'.'.$faker->safeEmail;

        $I->fillField(['id' => 'contact_name_name01'], '姓');
        $I->fillField(['id' => 'contact_name_name02'], '名');
        $I->fillField(['id' => 'contact_kana_kana01'], 'セイ');
        $I->fillField(['id' => 'contact_kana_kana02'], 'メイ');
        $I->fillField(['id' => 'contact_postal_code'], '530-0001');
        $I->selectOption(['id' => 'contact_address_pref'], ['value' => '27']);
        $I->fillField(['id' => 'contact_address_addr01'], '大阪市北区');
        $I->fillField(['id' => 'contact_address_addr02'], '梅田2-4-9 ブリーゼタワー13F');
        $I->fillField(['id' => 'contact_phone_number'], '111-111-111');
        $I->fillField(['id' => 'contact_email'], $email);
        $I->fillField(['id' => 'contact_contents'], 'お問い合わせ内容の送信');
        $I->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');
    }

    private function contactComplete(AcceptanceTester $I)
    {
        $I->click('div.ec-contactConfirmRole div.ec-RegisterRole__actions button.ec-blockBtn--action');
    }

    public function パスワード再発行(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T07_パスワード再発行');

        $I->logoutAsMember();

        for ($i = 0; $i < 5; $i++) {
            $I->expect('パスワード再発行を行います：'.$i);
            $I->amOnPage('/forgot');
            $I->fillField('login_email', 'test@example.com');
            $I->click('次へ');
            $I->see('パスワードの再発行(メール送信)', 'div.ec-pageHeader h1');
        }

        $I->expect('試行回数上限を超過します');
        $I->amOnPage('/forgot');
        $I->fillField('login_email', 'test@example.com');
        $I->click('次へ');
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    public function 非会員購入(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T08_非会員購入');

        $I->logoutAsMember();

        for ($i = 0; $i < 25; $i++) {
            $I->expect('非会員購入を行います：'.$i);
            // カートへ進む
            ProductDetailPage::go($I, 2)->カートに入れる(1)->カートへ進む();
            // レジに進む
            CartPage::go($I)->レジに進む();
            // 非会員情報入力
            $ShoppingPage = ShoppingLoginPage::at($I)->ゲスト購入();
            $this->inputGuestInfo($ShoppingPage)->次へ();
            // 注文確認画面へ
            ShoppingPage::at($I)->確認する();
            // 注文完了
            ShoppingConfirmPage::at($I)->注文する();
            $I->see('ご注文完了', 'div.ec-pageHeader h1');
        }

        $I->expect('試行回数上限を超過します');
        // カートへ進む
        ProductDetailPage::go($I, 2)->カートに入れる(1)->カートへ進む();
        // レジに進む
        CartPage::go($I)->レジに進む();
        // 非会員情報入力
        $ShoppingPage = ShoppingLoginPage::at($I)->ゲスト購入();
        $this->inputGuestInfo($ShoppingPage)->次へ();
        // 注文確認画面へ
        ShoppingPage::at($I)->確認する();
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    private function inputGuestInfo(ShoppingNonmemberPage $page)
    {
        $page
            ->入力_姓('姓03')
            ->入力_名('名03')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_Eメール('test@example.com')
            ->入力_Eメール確認('test@example.com');

        return $page;
    }

    public function 会員購入(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T09_会員購入');

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        for ($i = 0; $i < 10; $i++) {
            $I->expect('会員購入を行います：'.$i);
            // カートへ進む
            ProductDetailPage::go($I, 2)->カートに入れる(1)->カートへ進む();
            // レジに進む
            CartPage::go($I)->レジに進む();
            // 注文確認画面へ
            ShoppingPage::at($I)->確認する();
            // 注文完了
            ShoppingConfirmPage::at($I)->注文する();
            $I->see('ご注文完了', 'div.ec-pageHeader h1');
        }

        $I->expect('試行回数上限を超過します');
        // カートへ進む
        ProductDetailPage::go($I, 2)->カートに入れる(1)->カートへ進む();
        // レジに進む
        CartPage::go($I)->レジに進む();
        // 注文確認画面へ
        ShoppingPage::at($I)->確認する();
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }
}

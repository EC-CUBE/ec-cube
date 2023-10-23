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
use Page\Front\CustomerAddressAddPage;
use Page\Front\CustomerAddressEditPage;
use Page\Front\CustomerAddressListPage;
use Page\Front\CustomerAddressChangePage;
use Page\Front\MultipleShippingPage;
use Page\Front\MyPage;
use Page\Front\ProductDetailPage;
use Page\Front\ShoppingConfirmPage;
use Page\Front\ShoppingLoginPage;
use Page\Front\ShoppingNonmemberPage;
use Page\Front\ShoppingPage;
use Page\Front\EntryPage;


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
            EntryPage::go($I)
                ->フォーム入力()
                ->同意する()
                ->登録する();
            $I->see('現在、仮会員の状態です。', 'p.ec-reportDescription');
        }

        $I->expect('試行回数上限を超過します');
        EntryPage::go($I)
            ->フォーム入力()
            ->同意する()
            ->登録する();
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

    public function 注文確認_非会員購入(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T08_注文確認_非会員購入');

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

    public function 注文確認_会員購入(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T09_注文確認_会員購入');

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

    /**
     * checkoutでのスロットリングのテスト
     * confirmでの制限に引っかかるため、confirmLimiterの上限値を変更してから実施してください。
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function 注文完了_非会員購入(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T10_注文完了_非会員購入');

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
        // 注文完了
        ShoppingConfirmPage::at($I)->注文する();
        $I->see('購入処理で予期しないエラーが発生しました。恐れ入りますがお問い合わせページよりご連絡ください。', 'div.ec-cartRole__error');
    }

    /**
     * checkoutでのスロットリングのテスト
     * confirmでの制限に引っかかるため、confirmLimiterの上限値を変更してから実施してください。
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function 注文完了_会員購入(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T11_注文完了_会員購入');

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
        // 注文完了
        ShoppingConfirmPage::at($I)->注文する();
        $I->see('購入処理で予期しないエラーが発生しました。恐れ入りますがお問い合わせページよりご連絡ください。', 'div.ec-cartRole__error');
    }

    /**
     * @param AcceptanceTester $I
     * @return void
     */
    public function 会員情報編集(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T12_会員情報編集');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');
        $form = [];

        for ($i = 0; $i < 10; $i++) {
            $I->expect('会員情報を編集します。：'.$i);
            MyPage::go($I)->会員情報編集();
            // 会員情報・編集画面にて、登録ボタンをクリック
            $I->submitForm('div.ec-editRole form', $form);

            // 会員情報編集（完了）画面が表示される
            $I->see('会員情報編集(完了)', 'div.ec-pageHeader h1');
        }

        $I->expect('試行回数上限を超過します');

        MyPage::go($I)->会員情報編集();

        // 会員情報・編集画面にて、登録ボタンをクリック
        $I->submitForm('div.ec-editRole form', $form);

        // 会員情報・編集画面にて、登録ボタンをクリック
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    /**
     * @param AcceptanceTester $I
     * @return void
     */
    public function 配送先情報_追加(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T13_配送先情報_追加');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        for ($i = 0; $i < 10; $i++) {
            $I->expect('お届け先を追加します。：'.$i);
            $I->wait(10);
            // お届先作成
            // TOPページ>マイページ>お届け先編集
            MyPage::go($I)
                ->お届け先編集()
                ->追加();

            // 入力 & submit
            CustomerAddressEditPage::at($I)
                ->入力_姓('姓05')
                ->入力_名('名05')
                ->入力_セイ('セイ')
                ->入力_メイ('メイ')
                ->入力_郵便番号('530-0001')
                ->入力_都道府県(['value' => '27'])
                ->入力_市区町村名('大阪市北区')
                ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
                ->入力_電話番号('111-111-111')
                ->登録する();
        }

        $I->expect('試行回数上限を超過します');
        $I->wait(10);

        MyPage::go($I)
            ->お届け先編集()
            ->追加();

        // 入力 & submit
        CustomerAddressEditPage::at($I)
            ->入力_姓('姓05')
            ->入力_名('名05')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->登録する();

        // 会員情報・編集画面にて、登録ボタンをクリック
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    /**
     * @param AcceptanceTester $I
     * @return void
     */
    public function 配送先情報_編集(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T14_配送先情報_編集');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        MyPage::go($I)
            ->お届け先編集()
            ->追加();

        // 入力 & submit
        CustomerAddressEditPage::at($I)
            ->入力_姓('姓05')
            ->入力_名('名05')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->登録する();

        for ($i = 0; $i < 10; $i++) {
            $I->expect('お届け先を編集します。：'.$i);
            $I->wait(10);

            // お届先編集
            // TOPページ>マイページ>お届け先編集
            MyPage::go($I)
                ->お届け先編集()
                ->変更(1);

            CustomerAddressEditPage::at($I)
                ->入力_姓('姓05')
                ->入力_名('名05')
                ->入力_セイ('セイ')
                ->入力_メイ('メイ')
                ->入力_郵便番号('530-0001')
                ->入力_都道府県(['value' => '27'])
                ->入力_市区町村名('大阪市南区')
                ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
                ->入力_電話番号('111-111-111')
                ->登録する();
            }

        $I->expect('試行回数上限を超過します');
        $I->wait(10);

        // お届先編集
        // TOPページ>マイページ>お届け先編集
        MyPage::go($I)
            ->お届け先編集()
            ->変更(1);

        CustomerAddressEditPage::at($I)
            ->入力_姓('姓05')
            ->入力_名('名05')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市南区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->登録する();

        // 会員情報・編集画面にて、登録ボタンをクリック
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    /**
     * customer_delivery_deleteでのスロットリングのテスト
     * customer_delivery_newでの制限に引っかかるため、customer_delivery_newのlimiter上限値を変更してから実施してください。
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function 配送先情報_削除(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T15_配送先情報_削除');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        for ($i = 0; $i < 10; $i++) {
            $I->expect('お届け先を追加します。：'.$i);
            $I->wait(10);

            // TOPページ>マイページ>お届け先編集
            MyPage::go($I)->お届け先編集()->追加();

            CustomerAddressEditPage::at($I)
                ->入力_姓('姓0501')
                ->入力_名('名0501')
                ->入力_セイ('セイ')
                ->入力_メイ('メイ')
                ->入力_郵便番号('530-0001')
                ->入力_都道府県(['value' => '27'])
                ->入力_市区町村名('大阪市西区')
                ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
                ->入力_電話番号('111-111-111')
                ->登録する();
         }

        for ($i = 0; $i < 10; $i++) {
            $I->expect('お届け先を削除します。：'.$i);
            CustomerAddressListPage::at($I)
                ->削除(1);
        }

        $I->expect('試行回数上限を超過します');
        $I->wait(10);

        // TOPページ>マイページ>お届け先編集
        MyPage::go($I)->お届け先編集()->追加();

        CustomerAddressEditPage::at($I)
            ->入力_姓('姓0501')
            ->入力_名('名0501')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市西区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->登録する();

        $I->wait(1);

        $I->expect('お届け先を削除します。');
        CustomerAddressListPage::at($I)
            ->削除(1);

        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    /**
     * @param AcceptanceTester $I
     * @return void
     */
    public function order_お届け先追加(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T16_order_お届け先追加');
        $I->logoutAsMember();
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $nameSei = 'あいおい0302';
        $nameMei = '名0302';

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());
        ShoppingPage::at($I)->お届け先追加();

        for ($i = 0; $i < 10; $i++) {
            $I->expect('お届け先を追加します。：'.$i);
            // 新規お届け先追加
            MultipleShippingPage::at($I)->新規お届け先を追加する();

            CustomerAddressAddPage::at($I)
                ->入力_姓($nameSei)
                ->入力_名($nameMei)
                ->入力_セイ('セイ')
                ->入力_メイ('メイ')
                ->入力_郵便番号('530-0001')
                ->入力_都道府県(['value' => '27'])
                ->入力_市区町村名('大阪市北区2')
                ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F2')
                ->入力_電話番号('222-222-222')
                ->登録する();

            $I->wait(1);
        }

        // 新規お届け先追加
        MultipleShippingPage::at($I)->新規お届け先を追加する();

        CustomerAddressAddPage::at($I)
            ->入力_姓($nameSei)
            ->入力_名($nameMei)
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区2')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F2')
            ->入力_電話番号('222-222-222')
            ->登録する();

        $I->wait(1);
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    /**
     * @param AcceptanceTester $I
     * @return void
     */
    public function order_お届け先変更(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T17_order_お届け先変更');
        $I->logoutAsMember();
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $nameSei = 'あいおい0302';
        $nameMei = '名0302';

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());

        for ($i = 0; $i < 10; $i++) {
            $I->expect('お届け先を変更します。：'.$i);
            // 新規お届け先追加
            ShoppingPage::at($I)->お届け先変更();

            CustomerAddressChangePage::at($I)->go($I)
                ->入力_姓($nameSei)
                ->入力_名($nameMei)
                ->入力_セイ('セイ')
                ->入力_メイ('メイ')
                ->入力_郵便番号('530-0001')
                ->入力_都道府県(['value' => '27'])
                ->入力_市区町村名('大阪市北区2')
                ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F2')
                ->入力_電話番号('222-222-222')
                ->登録する();

            $I->wait(1);
        }

        // 新規お届け先追加
        ShoppingPage::at($I)->お届け先変更();

        CustomerAddressChangePage::at($I)->go($I)
            ->入力_姓($nameSei)
            ->入力_名($nameMei)
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区2')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F2')
            ->入力_電話番号('222-222-222')
            ->登録する();

        $I->wait(1);
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    public function 新規会員登録_入力(AcceptanceTester $I)
    {
        $I->wantTo('EF0901-UC01-T18_会員登録_入力');

        for ($i = 0; $i < 25; $i++) {
            $I->expect('会員登録を行います：'.$i);
            EntryPage::go($I)
                ->フォーム入力()
                ->登録する();
        }

        EntryPage::go($I)
                ->フォーム入力()
                ->登録する();
        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p.ec-reportDescription');
    }

    public function 管理画面二段階認証(AcceptanceTester $I)
    {
        $I->loginAsAdmin();

        // 二段階認証を有効にしてメンバーを新規作成
        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/setting/system/member/new');
        $I->see('メンバー登録システム設定', '.c-pageTitle');

        $login_id = 'admin_'.\Eccube\Util\StringUtil::random(6);
        $password = 'password1234';
        $I->fillField(['id' => 'admin_member_name'], '管理者');
        $I->fillField(['id' => 'admin_member_department'], 'admin_throttling');
        $I->fillField(['id' => 'admin_member_login_id'], $login_id);
        $I->fillField(['id' => 'admin_member_plain_password_first'], $password);
        $I->fillField(['id' => 'admin_member_plain_password_second'], $password);
        $I->selectOption(['id' => 'admin_member_Authority'], 'システム管理者');
        $I->click("label[for='admin_member_Work_1']"); // 稼働
        $I->click("label[for='admin_member_two_factor_auth_enabled']"); // 有効
        $I->click('#member_form .c-conversionArea__container button');
        $I->see('保存しました', '.c-contentsArea .alert-success');

        $I->logoutAsAdmin();

        // 作成したメンバーでログイン
        $I->submitForm('#form1', [
            'login_id' => $login_id,
            'password' => $password,
        ]);

        // 二段階認証のセットアップ
        $secret = $I->executeJS('return $("#admin_two_factor_auth_auth_key").val();');
        $tfa = new \RobThree\Auth\TwoFactorAuth();
        $code = $tfa->getCode($secret);
        $I->fillField(['id' => 'admin_two_factor_auth_device_token'], $code);
        $I->click('登録');
        $I->see('ホーム', '.c-contentsArea .c-pageTitle > .c-pageTitle__titles');

        // ログアウトし、二段階認証を試行する
        $I->logoutAsAdmin();

        $I->resetCookie('eccube_2fa'); // 2要素認証のクッキーを削除

        $I->submitForm('#form1', [
            'login_id' => $login_id,
            'password' => $password,
        ]);

        // トークン入力画面で5回入力
        for ($i = 0; $i < 5; $i++) {
            $I->fillField(['id' => 'admin_two_factor_auth_device_token'], 'aaaaa'.$i);
            $I->click('認証');
            $I->waitForText('トークンに誤りがあります。再度入力してください。');
        }

        // トークン入力の試行回数制限を超過
        $I->fillField(['id' => 'admin_two_factor_auth_device_token'], 'aaaaaa');
        $I->click('認証');

        $I->see('試行回数の上限を超過しました。しばらくお待ちいただき、再度お試しください。', 'p');
    }
}

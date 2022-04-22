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

use Carbon\Carbon;
use Codeception\Util\Fixtures;
use Page\Admin\CalendarSettingsPage;
use Page\Admin\CsvSettingsPage;
use Page\Admin\CustomerManagePage;
use Page\Admin\DeliveryEditPage;
use Page\Admin\DeliveryManagePage;
use Page\Admin\LayoutEditPage;
use Page\Admin\LayoutManagePage;
use Page\Admin\MailSettingsPage;
use Page\Admin\OrderEditPage;
use Page\Admin\OrderManagePage;
use Page\Admin\OrderStatusSettingsPage;
use Page\Admin\PageEditPage;
use Page\Admin\PageManagePage;
use Page\Admin\PaymentEditPage;
use Page\Admin\PaymentManagePage;
use Page\Admin\ProductEditPage;
use Page\Admin\ProductManagePage;
use Page\Admin\ShopSettingPage;
use Page\Admin\TaxManagePage;
use Page\Front\CartPage;
use Page\Front\EntryPage;
use Page\Front\HistoryPage;
use Page\Front\MyPage;
use Page\Front\ProductDetailPage;
use Page\Front\ShoppingConfirmPage;
use Page\Front\ShoppingPage;
use Page\Front\TopPage;

/**
 * @group admin
 * @group admin03
 * @group basicinformation
 * @group ea7
 */
class EA07BasicinfoCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @group vaddy
     * @group basicsetting
     */
    public function basicinfo_基本設定(AcceptanceTester $I)
    {
        $I->wantTo('EA0701-UC01-T01 基本設定');

        $page = ShopSettingPage::go($I)
            ->入力_会社名('サンプル会社名')
            ->入力_店名('サンプルショップ')
            ->入力_郵便番号('100-0001')
            ->入力_電話番号('050-5555-5555');
        $I->wait(1);
        $page->登録();

        $I->see('保存しました', ShopSettingPage::$登録完了メッセージ);

        $I->amOnPage('/help/about');
        $I->see('サンプル会社名', '#help_about_box__company_name dd');
        $I->see('サンプルショップ', '#help_about_box__shop_name dd');
        $I->see('1000001', '#help_about_box__address dd');
        $I->see('東京都千代田区千代田', '#help_about_box__address dd');
        $I->see('05055555555', '#help_about_box__phone_number dd');
    }

    public function basicinfo_会員設定_仮会員機能(AcceptanceTester $I)
    {
        $I->wantTo('EA0701-UC01-T06_会員設定の設定、編集(仮会員機能：無効)');

        $page = ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_仮会員機能, false)
            ->登録();

        // 会員登録
        $faker = Fixtures::get('faker');
        $email = microtime(true).'.'.$faker->safeEmail;
        EntryPage::go($I)->新規会員登録([
            'entry[email][first]' => $email,
            'entry[email][second]' => $email,
        ]);

        // 会員ステータスのチェック
        $page = CustomerManagePage::go($I);
        $I->fillField('#admin_search_customer_multi', $email);
        $page->詳細検索_本会員();
        $I->see('検索結果：1件が該当しました', CustomerManagePage::$検索結果メッセージ);
        $I->see($email);

        $I->wantTo('EA0701-UC01-T05_会員設定の設定、編集(仮会員機能：有効)');

        $page = ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_仮会員機能, true)
            ->登録();

        // 会員登録
        $I->logoutAsMember();
        $email = microtime(true).'.'.$faker->safeEmail;
        EntryPage::go($I)->新規会員登録([
            'entry[email][first]' => $email,
            'entry[email][second]' => $email,
        ]);
        $I->logoutAsMember();

        // 会員ステータスのチェック
        $I->loginAsAdmin();
        $page = CustomerManagePage::go($I);
        $I->fillField('#admin_search_customer_multi', $email);
        $page->詳細検索_仮会員();
        $I->see('検索結果：1件が該当しました', CustomerManagePage::$検索結果メッセージ);
        $I->see($email);
    }

    public function basicinfo_会員設定_マイページ注文状況(AcceptanceTester $I)
    {
        $I->wantTo('EA0701-UC01-T08_会員設定の設定、編集(マイページに注文状況を表示：無効)');

        $entityManager = Fixtures::get('entityManager');
        $customer = $entityManager->getRepository('Eccube\Entity\Customer')->find(1);
        $page = ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_マイページに注文状況を表示, false)
            ->登録();

        $I->loginAsMember($customer->getEmail(), 'password');
        MyPage::go($I)->注文履歴();
        $I->dontSee('ご注文状況', '.ec-historyRole');

        $I->wantTo('EA0701-UC01-T07_会員設定の設定、編集(マイページに注文状況を表示：有効)');

        $page = ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_マイページに注文状況を表示, true)
            ->登録();

        MyPage::go($I)->注文履歴();
        $I->see('ご注文状況', '.ec-historyRole');
    }

    public function basicinfo_会員設定_お気に入り(AcceptanceTester $I)
    {
        $I->wantTo('EA0701-UC01-T10_会員設定の設定、編集(お気に入り商品機能：無効)');

        $page = ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_お気に入り商品機能, false)
            ->登録();

        $I->amOnPage('/');
        $I->dontSee('お気に入り', '.ec-headerNav');

        $I->amOnPage('/products/detail/1');
        $I->dontSee('お気に入りに追加', '.ec-productRole__btn');

        $I->wantTo('EA0701-UC01-T09_会員設定の設定、編集(お気に入り商品機能：有効)');

        $page = ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_お気に入り商品機能, true)
            ->登録();

        $I->amOnPage('/');
        $I->see('お気に入り', '.ec-headerNav');

        $I->amOnPage('/products/detail/1');
        $I->see('お気に入りに追加', '.ec-productRole__btn');
    }

    public function basicinfo_会員設定_自動ログイン(AcceptanceTester $I)
    {
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $I->wantTo('EA0701-UC01-T12_会員設定の設定、編集(自動ログイン機能：無効)');

        $page = ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_自動ログイン機能, false)
            ->登録();

        $I->logoutAsMember();
        $I->amOnPage('/mypage/login');
        $I->dontSee('次回から自動的にログインする', '#login_mypage');

        $I->wantTo('EA0701-UC01-T011_会員設定の設定、編集(自動ログイン機能：有効)');

        $page = ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_自動ログイン機能, true)
            ->登録();

        $I->amOnPage('/mypage/login');
        $I->see('次回から自動的にログインする', '#login_mypage');
        $I->checkOption('#login_memory');
        $I->submitForm('#login_mypage', [
            'login_email' => $customer->getEmail(),
            'login_pass' => 'password',
        ]);
        $I->amOnPage('/mypage');

        $I->seeCookie('eccube_remember_me');
        $I->see('ログアウト', '.ec-headerNaviRole');
        $I->dontSee('ログイン', '.ec-headerNaviRole');

        $I->logoutAsMember();
    }

    public function basicinfo_商品設定の設定、編集_在庫切れ商品の非表示(AcceptanceTester $I)
    {
        // 在庫なし商品の準備
        ProductManagePage::go($I)
            ->検索('チェリーアイスサンド')
            ->検索結果_選択(1);

        ProductEditPage::at($I)
            ->入力_在庫数(0)
            ->登録();
        $I->see('保存しました', ProductEditPage::$登録結果メッセージ);

        $I->wantTo('EA0701-UC01-T13 商品設定の設定、編集(在庫切れ商品の非表示：有効)');

        // 在庫切れ商品の非表示設定
        $page = ShopSettingPage::go($I)
            ->設定_在庫切れ商品の非表示(true);

        // 表示確認
        $topPage = TopPage::go($I);
        $I->fillField(['class' => 'search-name'], 'チェリーアイスサンド');
        $topPage->検索();
        $I->see('お探しの商品は見つかりませんでした');

        $I->wantTo('EA0701-UC01-T14 商品設定の設定、編集(在庫切れ商品の非表示：無効)');

        // 在庫切れ商品の表示設定
        $page = ShopSettingPage::go($I)
            ->設定_在庫切れ商品の非表示(false);

        // 表示確認
        $topPage = TopPage::go($I);
        $I->fillField(['class' => 'search-name'], 'チェリーアイスサンド');
        $topPage->検索();
        $I->see('チェリーアイスサンド', '.ec-shelfGrid');
    }

    public function basicinfo_税設定(AcceptanceTester $I)
    {
        $I->wantTo('EA0701-UC01-T15 税設定');

        $I->amGoingTo('税設定を有効化');
        ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_商品別税率機能, true)
            ->登録();

        $I->expect('商品登録画面で、税率の入力欄が表示されている');
        ProductEditPage::go($I);
        $I->see('税率', '.c-contentsArea');
        $I->seeElement('#admin_product_class_tax_rate');

        $I->amGoingTo('税設定を無効化');
        ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_商品別税率機能, false)
            ->登録();

        $I->expect('商品登録画面で、税率の入力欄が表示されていない');
        ProductEditPage::go($I);
        $I->dontSee('税率', '.c-contentsArea');
        $I->dontSeeElement('#admin_product_class_tax_rate');
    }

    public function basicinfo_ポイント設定_有効(AcceptanceTester $I)
    {
        // "ポイント付与率"に任意の値を設定し、ポイント加算の確認を行う
        $I->wantTo('EA0701-UC01-T16 ポイント設定(有効) ポイント付与率');

        $price = 2800; // 購入商品の金額
        $point_rate = 2; // 付与率
        $point_conversion_rate = 5; // 換算レート
        $expected_point = floor($price * $point_rate / 100); // 付与されるポイント
        $expected_point_text = number_format($expected_point).' pt';
        $expected_discount = '-￥'.number_format($point_conversion_rate * $expected_point);

        $I->amGoingTo('商品を準備');
        ProductManagePage::go($I)
            ->検索('チェリーアイスサンド')
            ->検索結果_選択(1);
        ProductEditPage::at($I)
            ->入力_販売価格(2800)
            ->入力_在庫数(1000)
            ->登録();
        $I->see('保存しました', ProductEditPage::$登録結果メッセージ);

        $I->amGoingTo('会員を作成');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $customerPoint = $customer->getPoint();

        $I->amGoingTo('ポイント機能を有効化・付与率を設定');
        ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_ポイント機能, true)
            ->入力_ポイント付与率($point_rate)
            ->入力_ポイント換算レート($point_conversion_rate)
            ->登録();

        $I->amGoingTo('フロントにて注文手続き画面へ');
        $I->loginAsMember($customer->getEmail(), 'password');
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        $I->expect('注文手続き画面・確認画面にて、加算ポイントが表示されていること');
        CartPage::go($I)->レジに進む();
        $I->see('加算ポイント');
        $I->see($expected_point_text, CartPage::$加算ポイント);

        ShoppingPage::at($I)->確認する();
        $I->see('加算ポイント');
        $I->see($expected_point_text, CartPage::$加算ポイント);

        $I->amGoingTo('注文完了');
        ShoppingConfirmPage::at($I)->注文する();
        $I->see('ご注文ありがとうございました');

        $I->expect('マイベージ 注文詳細にて、加算ポイントが表示されていること');
        MyPage::go($I)->注文履歴詳細(0);
        HistoryPage::at($I);
        $I->see('加算ポイント');
        $I->see($expected_point_text, HistoryPage::$加算ポイント);

        $I->expect('管理画面・受注管理にて、加算ポイントが表示されていること');
        OrderManagePage::go($I)
            ->検索($customer->getEmail())
            ->一覧_編集(1);
        $I->see($expected_point, OrderEditPage::$加算ポイント);

        $I->amGoingTo('発送済みにする (ポイントが付与される)');
        OrderEditPage::at($I)
            ->入力_受注ステータス('発送済み')
            ->受注情報登録();
        $customerPoint += $expected_point;

        $I->expect('管理画面・会員管理にて、ポイントが付与されていること');
        CustomerManagePage::go($I)
            ->検索($customer->getEmail())
            ->一覧_編集(1);
        $I->seeInField(CustomerManagePage::$ポイント, $customerPoint);

        $I->expect('マイベージにて、ポイントが付与されていること');
        MyPage::go($I);
        $I->see('現在の所持ポイントは '.number_format($customerPoint).'pt です。');

        // "ポイント換算レート"に任意の値を設定し、ポイント利用の確認を行う
        $I->wantTo('EA0701-UC01-T16 ポイント設定(有効) ポイント換算レート');

        $I->amGoingTo('フロントにて注文手続き画面へ');
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        $I->expect('所持ポイントが表示されていること');
        CartPage::go($I)->レジに進む();
        $I->see('利用ポイント');
        $I->see(number_format($customerPoint).' pt が利用可能です。');

        $I->amGoingTo('利用ポイントを設定');
        ShoppingPage::at($I)
            ->入力_利用ポイント($expected_point)
            ->確認する();

        $I->expect('利用ポイントが正しく計算されていること');
        $I->see($expected_discount, ShoppingPage::$ポイント値引き額);
        $I->see($expected_point_text, ShoppingPage::$利用ポイント);

        $I->amGoingTo('注文完了 (ポイントが減算される)');
        ShoppingConfirmPage::at($I)->注文する();
        $I->see('ご注文ありがとうございました');
        $customerPoint -= $expected_point;

        $I->expect('管理画面・受注管理にて、利用ポイントが計算されていること');
        OrderManagePage::go($I)
            ->検索($customer->getEmail())
            ->一覧_編集(1);
        $I->see($expected_discount, OrderEditPage::$ポイント値引き額);
        $I->seeInField(OrderEditPage::$利用ポイント, $expected_point);

        $I->expect('管理画面・会員管理にて、ポイントが減少していること');
        CustomerManagePage::go($I)
            ->検索($customer->getEmail())
            ->一覧_編集(1);
        $I->seeInField(CustomerManagePage::$ポイント, $customerPoint);

        $I->amGoingTo('マイベージ 注文詳細にて、利用ポイントが計算されていること');
        MyPage::go($I)->注文履歴詳細(0);
        HistoryPage::at($I);
        $I->see($expected_discount, HistoryPage::$ポイント値引き額);
        $I->see($expected_point_text, HistoryPage::$利用ポイント);

        $I->expect('マイベージにて、ポイントが減少していること');
        MyPage::go($I);
        $I->see('現在の所持ポイントは '.number_format($customerPoint).'pt です。');
    }

    public function basicinfo_ポイント設定_無効(AcceptanceTester $I)
    {
        $I->wantTo('EA0701-UC01-T17 ポイント設定(無効)');

        $I->amGoingTo('会員を作成');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();

        $I->amGoingTo('ポイント機能を無効化');
        ShopSettingPage::go($I)
            ->入力_チェックボックス(ShopSettingPage::$チェックボックス_ポイント機能, false)
            ->登録();

        $I->amGoingTo('フロントにて注文手続き画面へ');
        $I->loginAsMember($customer->getEmail(), 'password');
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        $I->expect('注文手続き画面・確認画面にて、加算ポイントが表示されていないこと');
        CartPage::go($I)->レジに進む();
        $I->dontSee('加算ポイント');

        ShoppingPage::at($I)->確認する();
        $I->dontSee('加算ポイント');

        $I->amGoingTo('注文完了');
        ShoppingConfirmPage::at($I)->注文する();
        $I->see('ご注文ありがとうございました');

        $I->expect('マイベージにて、加算ポイントが表示されていないこと');
        MyPage::go($I)->注文履歴詳細(0);
        HistoryPage::at($I);
        $I->dontSee('加算ポイント');

        $I->expect('管理画面・受注管理にて、加算ポイントが 0であること');
        OrderManagePage::go($I)
            ->検索($customer->getEmail())
            ->一覧_編集(1);
        $I->assertEquals('0', $I->grabTextFrom(OrderEditPage::$加算ポイント));
    }

    public function basicinfo_特定商取引法(AcceptanceTester $I)
    {
        $I->wantTo('EA0702-UC01-T01 特定商取引法の設定');

        PageManagePage::go($I);
        $I->click(['xpath' => '//a[contains(text(), "特定商取引")]']);

        $test_text = uniqid('テストテキスト');
        $before = PageEditPage::at($I)->出力_内容();
        $after = preg_replace('/(<\/h1>.*?\n)/', "</h1>{$test_text}\n", $before);
        PageEditPage::at($I)
            ->入力_内容($after)
            ->登録();

        $I->see('保存しました', PageEditPage::$登録完了メッセージ);

        $I->amOnPage('/help/tradelaw');
        $I->see($test_text);
    }

    public function basicinfo_会員規約(AcceptanceTester $I)
    {
        $I->wantTo('EA0703-UC01-T01 会員規約の設定');

        PageManagePage::go($I);
        $I->click(['xpath' => '//a[contains(text(), "利用規約")]']);

        $test_text = uniqid('テストテキスト');
        $before = PageEditPage::at($I)->出力_内容();
        $after = preg_replace('/(<\/h1>.*?\n)/', "</h1>{$test_text}\n", $before);
        PageEditPage::at($I)
            ->入力_内容($after)
            ->登録();

        $I->see('保存しました', PageEditPage::$登録完了メッセージ);

        $I->amOnPage('/help/agreement');
        $I->see($test_text);
    }

    public function basicinfo_支払方法一覧(AcceptanceTester $I)
    {
        $I->wantTo('EA0704-UC01-T01 支払方法 一覧');

        // 表示
        $PaymentManagePage = PaymentManagePage::go($I);

        $I->see('郵便振替', $PaymentManagePage->一覧_支払方法(1));
    }

    /**
     * @group vaddy
     * @group paymentmethod
     */
    public function basicinfo_支払方法入れ替え(AcceptanceTester $I)
    {
        $I->wantTo('EA0704-UC02-T01 支払方法 入れ替え');

        // 表示
        $PaymentManagePage = PaymentManagePage::go($I);

        // 入れ替え
        $I->see('郵便振替', $PaymentManagePage->一覧_支払方法(1));
        $PaymentManagePage->一覧_下に(1);

        $PaymentManagePage = PaymentManagePage::go($I);
        $I->see('郵便振替', $PaymentManagePage->一覧_支払方法(2));

        $PaymentManagePage->一覧_上に(2);
        $PaymentManagePage = PaymentManagePage::go($I);
        $I->see('郵便振替', $PaymentManagePage->一覧_支払方法(1));
    }

    /**
     * @group vaddy
     * @group paymentmethod
     */
    public function basicinfo_支払方法登録(AcceptanceTester $I)
    {
        $I->wantTo('EA0705-UC01-T01 支払方法 登録');

        // 表示
        // 登録フォーム
        PaymentManagePage::go($I)
            ->新規入力();

        // 登録
        PaymentEditPage::at($I)
            ->入力_支払方法('payment method1')
            ->入力_手数料('100')
            ->入力_利用条件下限('1')
            ->登録();

        PaymentEditPage::at($I);
        $I->see('保存しました', PaymentEditPage::$登録完了メッセージ);

        $PaymentManagePage = PaymentManagePage::go($I);
        $I->see('payment method1', $PaymentManagePage->一覧_支払方法(1));
    }

    /**
     * @group vaddy
     * @group paymentmethod
     */
    public function basicinfo_支払方法編集(AcceptanceTester $I)
    {
        $I->wantTo('EA0705-UC02-T01 支払方法 編集');

        // 表示
        PaymentManagePage::go($I)
            ->一覧_編集(1);

        // 編集
        PaymentEditPage::at($I)
            ->入力_支払方法('payment method2')
            ->入力_手数料('1000')
            ->登録();

        PaymentEditPage::at($I);
        $I->see('保存しました', PaymentEditPage::$登録完了メッセージ);

        $PaymentManagePage = PaymentManagePage::go($I);
        $I->see('payment method2', $PaymentManagePage->一覧_支払方法(1));
    }

    /**
     * @group vaddy
     * @group paymentmethod
     */
    public function basicinfo_支払方法削除(AcceptanceTester $I)
    {
        $I->wantTo('EA0704-UC03-T01 支払方法 削除');

        // 削除用の支払い方法の登録
        PaymentManagePage::go($I)
            ->新規入力();
        PaymentEditPage::at($I)
            ->入力_支払方法('dummy payment')
            ->登録();

        // 削除
        $page = PaymentManagePage::go($I);
        $before = $page->一覧_件数取得();
        $page->一覧_削除(1);
        $I->see('削除しました', PaymentEditPage::$登録完了メッセージ);

        $after = PaymentManagePage::go($I)->一覧_件数取得();
        $I->assertEquals($before - 1, $after);
    }

    public function basicinfo_配送方法一覧(AcceptanceTester $I)
    {
        $I->wantTo('EA0706-UC01-T01 配送方法 一覧');

        // 表示
        $DeliveryManagePage = DeliveryManagePage::go($I);

        $I->see('サンプル宅配', $DeliveryManagePage->一覧_名称(2));
    }

    /**
     * @group vaddy
     * @group delivery
     */
    public function basicinfo_配送方法登録(AcceptanceTester $I)
    {
        $I->wantTo('EA0707-UC01-T01 配送方法 登録');

        // 表示
        DeliveryManagePage::go($I)
            ->新規登録();

        // 登録
        DeliveryEditPage::at($I)
            ->入力_配送業者名('配送業者名')
            ->入力_名称('名称')
            ->入力_支払方法選択(['1', '4'])
            ->入力_全国一律送料('100')
            ->登録();

        DeliveryEditPage::at($I);
        $I->see('保存しました', DeliveryEditPage::$登録完了メッセージ);

        $DeliveryManagePage = DeliveryManagePage::go($I);
        $I->see('配送業者名', $DeliveryManagePage->一覧_名称(2));
    }

    /**
     * @group vaddy
     * @group delivery
     */
    public function basicinfo_配送方法編集(AcceptanceTester $I)
    {
        $I->wantTo('EA0707-UC02-T01 配送方法 編集');

        // 表示
        DeliveryManagePage::go($I)
            ->一覧_編集(2);

        // 編集
        DeliveryEditPage::at($I)
            ->入力_配送業者名('配送業者名1')
            ->登録();

        DeliveryEditPage::at($I);
        $I->see('保存しました', DeliveryEditPage::$登録完了メッセージ);

        $DeliveryManagePage = DeliveryManagePage::go($I);
        $I->see('配送業者名1', $DeliveryManagePage->一覧_名称(2));
    }

    /**
     * @group vaddy
     * @group delivery
     */
    public function basicinfo_配送方法削除(AcceptanceTester $I)
    {
        $I->wantTo('EA0706-UC03-T01 配送方法 削除');

        // 削除
        $page = DeliveryManagePage::go($I);
        $before = $page->一覧_件数取得();
        $page->一覧_削除(2);
        $I->see('削除しました', DeliveryManagePage::$登録完了メッセージ);

        $after = DeliveryManagePage::go($I)->一覧_件数取得();
        $I->assertEquals($before - 1, $after);
    }

    /**
     * @group vaddy
     * @group delivery
     */
    public function basicinfo_配送方法一覧順序変更(AcceptanceTester $I)
    {
        $I->wantTo('EA0706-UC02-T01 配送方法一覧順序変更');

        $DeliveryManagePage = DeliveryManagePage::go($I);
        $I->see('サンプル宅配 / サンプル宅配', $DeliveryManagePage->一覧_名称(2));
        $I->see('サンプル業者 / サンプル業者', $DeliveryManagePage->一覧_名称(3));

        $DeliveryManagePage->一覧_下に(2);
        $I->see('サンプル業者 / サンプル業者', $DeliveryManagePage->一覧_名称(2));
        $I->see('サンプル宅配 / サンプル宅配', $DeliveryManagePage->一覧_名称(3));

        $DeliveryManagePage->一覧_上に(3);
        $I->see('サンプル宅配 / サンプル宅配', $DeliveryManagePage->一覧_名称(2));
        $I->see('サンプル業者 / サンプル業者', $DeliveryManagePage->一覧_名称(3));
    }

    /**
     * @group vaddy
     * @group taxrule
     */
    public function basicinfo_税率設定(AcceptanceTester $I)
    {
        $I->wantTo('EA0708-UC01-T01 税率設定');

        // 表示
        $TaxManagePage = TaxManagePage::go($I);

        // 一覧
        $I->see('税率設定', '#page_admin_setting_shop_tax > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div.card-header');
        $I->see('10%', '#ex-tax_rule-1 > td.align-middle.text-right');

        // 登録
        $TaxManagePage
            ->入力_消費税率(1, '8')
            ->入力_適用日(date('Y'), date('n'), date('j'))
            ->入力_適用時(date('G'), (int) date('i'))
            ->共通税率設定_登録();
        $I->see('8%', $TaxManagePage->一覧_税率(2));

        // edit
        $TaxManagePage
            ->一覧_編集(2)
            ->入力_消費税率(2, 12)
            ->決定(2);

        $I->see('保存しました', TaxManagePage::$登録完了メッセージ);
        $I->see('12%', $TaxManagePage->一覧_税率(2));

        // 削除
        $TaxManagePage->一覧_削除(2);
        $I->see('削除しました', TaxManagePage::$登録完了メッセージ);
    }

    /**
     * @group vaddy
     * @group mailsetting
     */
    public function basicinfo_メール設定(AcceptanceTester $I)
    {
        $I->wantTo('EA0709-UC02-T01 メール設定');

        // 表示
        $title = '商品出荷のお知らせ '.uniqid();
        MailSettingsPage::go($I)
            ->入力_テンプレート('出荷通知メール')
            ->入力_件名($title)
            ->登録();

        $I->see('保存しました', MailSettingsPage::$登録完了メッセージ);

        // 結果確認
        $I->resetEmails();

        OrderManagePage::go($I)
            ->一覧_メール通知(1);

        $I->seeInLastEmailSubject("[サンプルショップ] {$title}");
    }

    /**
     * @group vaddy
     * @group csvsetting
     */
    public function basicinfo_CSV出力項目(AcceptanceTester $I)
    {
        $I->wantTo('EA0710-UC01-T01 CSV出力項目設定');

        // 表示
        CsvSettingsPage::go($I)
            ->入力_CSVタイプ('受注CSV')
            ->選択_出力項目('誕生日')
            ->削除()
            ->設定();

        $I->see('保存しました', CsvSettingsPage::$登録完了メッセージ);

        // CSVダウンロード
        OrderManagePage::go($I)->受注CSVダウンロード実行();
        $I->wait(10);
        $csv = $I->getLastDownloadFile('/^order_\d{14}\.csv$/');
        $csvHeader = mb_convert_encoding(file($csv)[0], 'UTF-8', 'SJIS-win');
        $I->assertContains('注文ID', $csvHeader);
        $I->assertNotContains('誕生日', $csvHeader);
    }

    /**
     * @group vaddy
     * @group orderstatus
     */
    public function basicinfo_受注対応状況設定(AcceptanceTester $I)
    {
        $I->wantTo('EA0711-UC01-T01 受注対応状況設定');

        // 表示
        OrderStatusSettingsPage::go($I)
            ->入力_名称_管理('新規受付')
            ->入力_名称_マイページ('注文受付')
            ->入力_色('#19406C')
            ->登録();

        $I->see('保存しました', OrderStatusSettingsPage::$登録完了メッセージ);

        OrderStatusSettingsPage::go($I);
        $I->seeInField(OrderStatusSettingsPage::$名称_マイページ, '注文受付');
        $I->seeInField(OrderStatusSettingsPage::$名称_管理, '新規受付');
    }

    /**
     * @group vaddy
     * @group calendar
     */
    public function basicinfo_定休日カレンダー_表示(AcceptanceTester $I)
    {
        $I->wantTo('EA0712-UC01-T01 定休日カレンダー_表示');
        $I->wantTo('EA0712-UC01-T02 定休日カレンダー_設定');

        // 定休日を設定
        $holidays = [
            '定休日1' => Carbon::now()->day(1), // 今月1日
            '定休日2' => Carbon::now()->startOfMonth()->addMonth(1)->day(28), // 翌月28日
        ];

        foreach ($holidays as $title => $date) {
            CalendarSettingsPage::go($I)
                ->入力_タイトル($title)
                ->入力_日付($date->format('Y-m-d'))
                ->登録();
            $I->see('保存しました', CalendarSettingsPage::$登録完了メッセージ);
        }

        // レイアウト設定でカレンダーブロックを登録
        LayoutManagePage::go($I)->レイアウト編集('トップページ用レイアウト');
        LayoutEditPage::at($I)
            ->ブロックを移動('カレンダー', '#position_7')
            ->登録();
        $I->see('保存しました', LayoutEditPage::$登録完了メッセージ);

        // フロント画面でカレンダーが表示されていることを確認
        $I->amOnPage('/');
        $I->see('カレンダー', ['class' => 'ec-layoutRole__mainBottom']);

        // フロント画面で定休日にクラス .ec-calendar__holiday が設定されていることを確認
        $I->seeElement(['xpath' => '//table[@id="this-month-table"]//td[contains(@class,"ec-calendar__holiday")][text()="'.$holidays['定休日1']->format('j').'"]']);
        $I->seeElement(['xpath' => '//table[@id="next-month-table"]//td[contains(@class,"ec-calendar__holiday")][text()="'.$holidays['定休日2']->format('j').'"]']);

        // 今日の日付にクラス .ec-calendar__today が設定されていることを確認
        $I->seeElement(['xpath' => '//table[@id="this-month-table"]//td[contains(@class,"ec-calendar__today")][text()="'.Carbon::now()->format('j').'"]']);
    }

    /**
     * EA10PluginCestではテストが失敗するため、ここでテストを行う
     *
     * @group vaddy
     * @group pluginauth
     */
    public function basicinfo_認証キー設定(AcceptanceTester $I)
    {
        $I->wantTo('EA1101-UC01-T01_認証キー設定');

        $config = Fixtures::get('config');
        $I->amOnPage('/'.$config['eccube_admin_route'].'/store/plugin/authentication_setting');

        // 認証キーの新規発行については、認証キーの発行数が増加する為省略(以下は一度実行済み)
        // $I->expect('認証キーの新規発行ボタンのクリックします。');
        // $I->click('認証キー新規発行');

        $I->expect('認証キーの入力を行います。');
        $I->fillField(['id' => 'admin_authentication_authentication_key'], '1111111111111111111111111111111111111111');

        $I->expect('認証キーの登録ボタンをクリックします。');
        $I->click(['css' => '.btn-ec-conversion']);
        $I->see('保存しました');
    }
}

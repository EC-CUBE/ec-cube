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
use Eccube\Entity\Customer;
use Page\Admin\OrderEditPage;
use Page\Admin\ShippingEditPage;
use Page\Admin\ShopSettingPage;
use Page\Front\CartPage;
use Page\Front\CustomerAddressAddPage;
use Page\Front\CustomerAddressListPage;
use Page\Front\EntryConfirmPage;
use Page\Front\EntryPage;
use Page\Front\ProductDetailPage;
use Page\Front\ShoppingLoginPage;
use Page\Front\ShoppingPage;

/**
 * @group admin
 * @group admin03
 * @group basicoption
 * @group ea7
 */
class EF07BasicCustomerOptionCest
{
    protected function disableOptionRequireKana(\AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        ShopSettingPage::go($I)
            ->切替_カナ必須項目(false)
            ->登録();
    }

    /**
     * @group require_kana
     * @before disableOptionRequireKana
     */
    public function test_新規会員登録(\AcceptanceTester  $I)
    {
        $I->wantTo('カナ入力なしで会員登録できる');

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;

        // 新規会員登録
        EntryPage::go($I)
            ->入力_姓('姓')
            ->入力_名('名')
            ->入力_姓カナ('')
            ->入力_名カナ('')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村('大阪市北区')
            ->入力_住所('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_メールアドレス($new_email)
            ->入力_メールアドレス確認($new_email)
            ->入力_パスワード('password')
            ->入力_パスワード確認('password')
            ->入力_職業(['value' => '1'])
            ->入力_利用規約同意()
            ->同意して登録();

        EntryConfirmPage::at($I);
    }

    /**
     * @group require_kana
     * @before disableOptionRequireKana
     */
    public function test_お届け先追加(\AcceptanceTester $I)
    {
        $I->wantTo('カナ入力なしでお届け先を追加できる');

        $I->logoutAsMember();

        $createCustomer = Fixtures::get('createCustomer');
        /** @var Customer $customer */
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');
        CustomerAddressAddPage::go($I)
            ->入力_姓('性')
            ->入力_名('名')
            ->入力_セイ('')
            ->入力_メイ('')
            ->入力_郵便番号('060-0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_電話番号('111-111-111')
            ->登録する();

        CustomerAddressListPage::at($I);
    }

    /**
     * @group require_kana
     * @before disableOptionRequireKana
     */
    public function test_ゲスト購入(\AcceptanceTester $I)
    {
        $I->wantTo('カナ入力なしでゲスト購入できる');

        $I->logoutAsMember();

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;
        $BaseInfo = Fixtures::get('baseinfo');

        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        $ShoppingLoginPage = ShoppingLoginPage::at($I)->ゲスト購入();
        $ShoppingLoginPage
            ->入力_姓('姓')
            ->入力_名('名')
            ->入力_セイ('')
            ->入力_メイ('')
            ->入力_郵便番号('530-0001')
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_Eメール($new_email)
            ->入力_Eメール確認($new_email)
            ->次へ();

        ShoppingPage::at($I);
    }

    /**
     * @group require_kana
     * @before disableOptionRequireKana
     */
    public function test_受注出荷登録(\AcceptanceTester $I)
    {
        $I->wantTo('カナ入力なしで受注/出荷登録できる');

        // 受注登録
        $OrderEditPage = OrderEditPage::go($I)
            ->入力_支払方法(['4' => '郵便振替'])
            ->入力_姓('order1')
            ->入力_名('order1')
            ->入力_セイ('')
            ->入力_メイ('')
            ->入力_郵便番号('060-0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_Eメール('test@test.com')
            ->入力_電話番号('111-111-111')
            ->注文者情報をコピー()
            ->入力_配送業者([1 => 'サンプル業者'])
            ->商品検索('チェリーアイスサンド')
            ->商品検索結果_選択(1)
            ->受注情報登録();

        $I->see('保存しました', OrderEditPage::$登録完了メッセージ);

        // 出荷登録
        $OrderEditPage->お届け先の追加();

        ShippingEditPage::at($I)
            ->入力_姓('aaa')
            ->入力_セイ('')
            ->入力_メイ('')
            ->入力_郵便番号('060-0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_電話番号('111-111-111')
            ->入力_番地_ビル名('address 2')
            ->出荷情報登録();

        $I->see('保存しました', ShippingEditPage::$登録完了メッセージ);
    }
}

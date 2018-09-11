<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Codeception\Util\Fixtures;
use Eccube\Entity\Customer;
use Page\Front\CartPage;
use Page\Front\CustomerAddressAddPage;
use Page\Front\MultipleShippingPage;
use Page\Front\ProductDetailPage;
use Page\Front\ShippingEditPage;
use Page\Front\ShoppingCompletePage;
use Page\Front\ShoppingConfirmPage;
use Page\Front\ShoppingLoginPage;
use Page\Front\ShoppingPage;
use Page\Front\TopPage;

/**
 * @group front
 * @group order
 * @group ef3
 */
class EF03OrderCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->setStock(2, 20);
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function order_カート買い物を続ける(\AcceptanceTester $I)
    {
        $I->wantTo('EF0301-UC01-T01 カート 買い物を続ける');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->お買い物を続ける();

        // トップページ
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function order_一覧からカートに入れる(\AcceptanceTester $I)
    {
        $I->wantTo('EF0301-UC01-T02 カート 買い物を続ける');

        $ProductListPage = TopPage::go($I)
            ->検索('彩のジェラートCUBE');

        $CartPage = $ProductListPage
            ->カートに入れる(1, 1, [3 => 'チョコ'], [6 => '16mm × 16mm'])
            ->カートへ進む();

        $I->assertEquals(1, $CartPage->明細数());
        $I->assertContains('彩のジェラートCUBE', $CartPage->商品名(1));
        $I->assertContains('チョコ', $CartPage->商品名(1));
        $I->assertContains('16mm × 16mm', $CartPage->商品名(1));
        $I->assertEquals(1, $CartPage->商品数量(1));
    }

    public function order_カート削除(\AcceptanceTester $I)
    {
        $I->wantTo('EF0301-UC01-T02 カート 削除');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->商品削除(1);
    }

    public function order_カート数量増やす(\AcceptanceTester $I)
    {
        $I->wantTo('EF0301-UC01-T03 カート 数量増やす');

        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        $cartPage = CartPage::go($I)
            ->商品数量増やす(1);

        // 確認
        $I->assertEquals('2', $cartPage->商品数量(1));
    }

    public function order_カート数量減らす(\AcceptanceTester $I)
    {
        $I->wantTo('EF0301-UC01-T04 カート 数量減らす');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(2)
            ->カートへ進む();

        $cartPage = CartPage::go($I)
            ->商品数量減らす(1);

        // 確認
        $I->assertEquals('1', $cartPage->商品数量(1));
    }

    public function order_ログインユーザ購入(\AcceptanceTester $I)
    {
        $I->wantTo('EF0302-UC01-T01 ログインユーザ購入');
        $I->logoutAsMember();
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $BaseInfo = Fixtures::get('baseinfo');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());

        $I->resetEmails();

        ShoppingPage::at($I)->確認する();
        ShoppingConfirmPage::at($I)->注文する();

        $I->wait(1);

        // メール確認
        $I->seeEmailCount(2);
        foreach ([$customer->getEmail(), $BaseInfo->getEmail01()] as $email) {
            // TODO 注文した商品の内容もチェックしたい
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, $customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前：'.$customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前(カナ)：'.$customer->getKana01().' '.$customer->getKana02().' 様');
            $I->seeInLastEmailTo($email, '郵便番号：〒'.$customer->getPostalCode());
            $I->seeInLastEmailTo($email, '住所：'.$customer->getPref()->getName().$customer->getAddr01().$customer->getAddr02());
            $I->seeInLastEmailTo($email, '電話番号：'.$customer->getPhoneNumber());
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$customer->getEmail());
        }

        // 完了画面 -> topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function order_ゲスト購入(\AcceptanceTester $I)
    {
        $I->wantTo('EF0302-UC02-T01 ゲスト購入');
        $I->logoutAsMember();

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;
        $BaseInfo = Fixtures::get('baseinfo');

        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        $ShoppingPage = ShoppingLoginPage::at($I)->ゲスト購入();
        $ShoppingPage
            ->入力_姓('姓03')
            ->入力_名('名03')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001');

        // TODO: 郵便番号入力後のcodeceptionの入力後にJSが走ってしまい「梅田」が2重で入力されてしまう。
        // 上記を回避するためにwait関数を入れる。
        // こちらは本体のmasterブランチで修正されているので、master -> sf マージ後には不要になる見込み。
        $I->wait(5);

        $ShoppingPage
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_Eメール($new_email)
            ->入力_Eメール確認($new_email)
            ->次へ();

        $I->resetEmails();

        ShoppingPage::at($I)->確認する();
        ShoppingConfirmPage::at($I)->注文する();

        $I->wait(1);

        // 確認
        $I->seeEmailCount(2);
        foreach ([$new_email, $BaseInfo->getEmail01()] as $email) {
            // TODO 注文した商品の内容もチェックしたい
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, '姓03 名03 様');
            $I->seeInLastEmailTo($email, 'お名前：姓03 名03 様');
            $I->seeInLastEmailTo($email, 'お名前(カナ)：セイ メイ 様');
            $I->seeInLastEmailTo($email, '郵便番号：〒5300001');
            $I->seeInLastEmailTo($email, '住所：大阪府大阪市北区梅田2-4-9 ブリーゼタワー13F');
            $I->seeInLastEmailTo($email, '電話番号：111111111');
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$new_email);
        }

        // 完了画面 -> topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function order_ゲスト購入情報変更(\AcceptanceTester $I)
    {
        $I->wantTo('EF0305-UC02-T01 ゲスト購入 情報変更'); // EF0305-UC04-T01も一緒にテスト
        $I->logoutAsMember();

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;
        $BaseInfo = Fixtures::get('baseinfo');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        $ShoppingPage = ShoppingLoginPage::at($I)->ゲスト購入();
        $ShoppingPage
            ->入力_姓('姓03')
            ->入力_名('名03')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号('530-0001');

        // TODO: 郵便番号入力後のcodeceptionの入力後にJSが走ってしまい「梅田」が2重で入力されてしまう。
        // 上記を回避するためにwait関数を入れる。
        // こちらは本体のmasterブランチで修正されているので、master -> sf マージ後には不要になる見込み。
        $I->wait(5);

        $ShoppingPage
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号('111-111-111')
            ->入力_Eメール($new_email)
            ->入力_Eメール確認($new_email)
            ->次へ();

        // 確認
        $ShoppingPage = ShoppingPage::at($I)
            ->お客様情報変更()
            ->入力_姓('姓0301')
            ->お客様情報変更OK();

        // 確認
        $I->see('姓0301', '#shopping-form .customer-name01');

        // 配送情報
        $ShoppingPage->お届け先変更();

        ShippingEditPage::at($I)
            ->入力_姓('姓0302')
            ->登録する();

        $I->see('姓0302', 'div.ec-orderRole div.ec-orderDelivery__address');

        $I->resetEmails();

        ShoppingPage::at($I)->確認する();
        ShoppingConfirmPage::at($I)->注文する();

        $I->seeEmailCount(2);
        foreach ([$new_email, $BaseInfo->getEmail01()] as $email) {
            // TODO 注文した商品の内容もチェックしたい
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, '姓0301 名03 様');
            $I->seeInLastEmailTo($email, 'お名前：姓0302 名03 様', '変更後のお届け先');
            $I->seeInLastEmailTo($email, '郵便番号：〒5300001');
            $I->seeInLastEmailTo($email, '住所：大阪府大阪市北区梅田2-4-9 ブリーゼタワー13F');
            $I->seeInLastEmailTo($email, '電話番号：111111111');
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$new_email);
        }

        // topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    /**
     * @see https://github.com/EC-CUBE/ec-cube/pull/3133
     */
    public function order_ログインしてカートをマージ(\AcceptanceTester $I)
    {
        $I->wantTo('EF0305-UC07-T01 ログインしてカートをマージ');
        $I->logoutAsMember();
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $BaseInfo = Fixtures::get('baseinfo');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());

        $I->resetEmails();

        ShoppingPage::at($I)->確認する();
        $I->logoutAsMember();

        // 商品詳細ジェラート カートへ
        ProductDetailPage::go($I, 1)
            ->規格選択(['チョコ', '16mm × 16mm'])
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());

        ShoppingPage::at($I)->確認する();
        ShoppingConfirmPage::at($I)->注文する();

        $I->wait(1);

        // メール確認
        $I->seeEmailCount(2);
        foreach ([$customer->getEmail(), $BaseInfo->getEmail01()] as $email) {
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, $customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前：'.$customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前(カナ)：'.$customer->getKana01().' '.$customer->getKana02().' 様');
            $I->seeInLastEmailTo($email, '郵便番号：〒'.$customer->getPostalCode());
            $I->seeInLastEmailTo($email, '住所：'.$customer->getPref()->getName().$customer->getAddr01().$customer->getAddr02());
            $I->seeInLastEmailTo($email, '電話番号：'.$customer->getPhoneNumber());
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$customer->getEmail());

            $I->seeInLastEmailTo($email, '商品名：チェリーアイスサンド');
            $I->seeInLastEmailTo($email, '商品名：彩のジェラートCUBE  チョコ  16mm × 16mm');
        }

        // 完了画面 -> topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function order_ログインユーザ購入複数配送(\AcceptanceTester $I)
    {
        // チェック用変数
        // 追加するお届け作の名前
        $nameSei = 'あいおい0302';
        $nameMei = '名0302';
        // カートへ入れる商品の数
        $cart_quantity = 1;
        // お届け先ごとに設定する商品の数
        $shipping1_quantity = 2;
        $shipping2_quantity = 3;

        $I->wantTo('EF0305-UC05-T01 お届け先の追加');
        $I->logoutAsMember();
        $createCustomer = Fixtures::get('createCustomer');
        /** @var \Eccube\Entity\CustomerAddress $customer */
        $customer = $createCustomer();
        $BaseInfo = Fixtures::get('baseinfo');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる($cart_quantity)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());

        $I->resetEmails();

        // -------- EF0305-UC05-T01_お届け先の追加 --------
        ShoppingPage::at($I)->お届け先追加();

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

        // 新規お届け先が追加されていることを確認
        $I->see($nameSei, '#form_shipping_multiple_0_shipping_0_customer_address > option:nth-child(2)');

        // -------- EF0305-UC06-T01_複数配送 - 同じ商品種別（同一配送先） --------
        // 複数配送設定
        MultipleShippingPage::at($I)
            ->お届け先追加()
            ->入力_お届け先('0', '0', $customer->getName01())
            ->入力_お届け先('0', '1', $customer->getName01())
            ->入力_数量('0', '0', $shipping1_quantity)
            ->入力_数量('0', '1', $shipping2_quantity)
            ->選択したお届け先に送る()
        ;

        // 配送先が１個なので数量をまとめる
        $sum_quantity = $shipping1_quantity + $shipping2_quantity;

        // 複数配送設定がされておらず、個数が１明細にまとめられていることを確認
        $I->see('お届け先', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->dontSee('お届け先(1)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->dontSee('お届け先(2)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(6)');
        $I->see(' × '.$sum_quantity, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(3) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($customer->getName01(), '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(4) > p:nth-child(1)');

        // -------- EF0305-UC06-T02_複数配送 - 同じ商品種別（別配送先） --------

        ShoppingPage::at($I)->お届け先追加();

        // 複数配送設定
        MultipleShippingPage::at($I)
            ->お届け先追加()
            ->入力_お届け先('0', '0', $customer->getName01())
            ->入力_お届け先('0', '1', $nameSei)
            ->入力_数量('0', '0', $shipping1_quantity)
            ->入力_数量('0', '1', $shipping2_quantity)
            ->選択したお届け先に送る()
        ;

        // 名前を比較してお届け先が上下どちらに表示されるか判断
        $compared = strnatcmp($customer->getName01(), $nameSei);
        if ($compared === 0) {
            $compared = strnatcmp($customer->getName02(), $nameMei);
        }
        // 上下それぞれで名前、商品個数を設定
        if ($compared < 0) {
            $quantity1 = $shipping1_quantity;
            $quantity2 = $shipping2_quantity;
            $name1 = $customer->getName01();
            $name2 = $nameSei;
        } else {
            $quantity1 = $shipping2_quantity;
            $quantity2 = $shipping1_quantity;
            $name1 = $nameSei;
            $name2 = $customer->getName01();
        }

        // 複数配送設定ができていることを確認
        $I->see('お届け先(1)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->see(' × '.$quantity1, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(3) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($name1, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(4) > p:nth-child(1)');
        $I->see('お届け先(2)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(6)');
        $I->see(' × '.$quantity2, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(7) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($name2, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(8) > p:nth-child(1)');

        ShoppingPage::at($I)->確認する();
        ShoppingConfirmPage::at($I)->注文する();

        $I->wait(1);

        // メール確認
        $I->seeEmailCount(2);
        foreach ([$customer->getEmail(), $BaseInfo->getEmail01()] as $email) {
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, $customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前：'.$customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前(カナ)：'.$customer->getKana01().' '.$customer->getKana02().' 様');
            $I->seeInLastEmailTo($email, '郵便番号：〒'.$customer->getPostalCode());
            $I->seeInLastEmailTo($email, '住所：'.$customer->getPref()->getName().$customer->getAddr01().$customer->getAddr02());
            $I->seeInLastEmailTo($email, '電話番号：'.$customer->getPhoneNumber());
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$customer->getEmail());
            $I->seeInLastEmailTo($email, '◎お届け先1');
            $I->seeInLastEmailTo($email, 'お名前：'.$nameSei);
            $I->seeInLastEmailTo($email, '数量：3');
            $I->seeInLastEmailTo($email, '◎お届け先2');
            $I->seeInLastEmailTo($email, '数量：2');
        }

        // 完了画面 -> topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function order_ログイン後に複数カートになればカートに戻す(\AcceptanceTester $I)
    {
        $I->wantTo('EF0303-UC01-T01_購入フローでログインしたタイミングで複数カートになったらログイン後にカート画面に戻す');
        $I->logoutAsMember();
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログアウト
        $I->logoutAsMember();

        $createProduct = Fixtures::get('createProduct');
        $Product = $createProduct();

        $entityManager = Fixtures::get('entityManager');
        $ProductClass = $Product->getProductClasses()[0];
        $SaleType = $entityManager->find(\Eccube\Entity\Master\SaleType::class, 2);
        $ProductClass->setSaleType($SaleType);
        $entityManager->persist($ProductClass);
        $entityManager->flush();

        if ($ProductClass->getClassCategory2()) {
            // 商品詳細
            ProductDetailPage::go($I, $Product->getId())
                ->規格選択([$ProductClass->getClassCategory1(), $ProductClass->getClassCategory2()])
                ->カートに入れる(1)
                ->カートへ進む();
        } else {
            ProductDetailPage::go($I, $Product->getId())
                ->規格選択([$ProductClass->getClassCategory1()])
                ->カートに入れる(1)
                ->カートへ進む();
        }

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());

        $I->see('同時購入できない商品がカートに含まれています', '.ec-alert-warning__text');
    }

    /**
     * カートに変更が無ければ、お届け先の設定が引き継がれる.
     */
    public function order_購入確認画面からカートに戻る(\AcceptanceTester $I)
    {
        // チェック用変数
        // 追加するお届け作の名前
        $nameSei = 'あいおい0302';
        $nameMei = '名0302';
        // カートへ入れる商品の数
        $cart_quantity = 1;
        // お届け先ごとに設定する商品の数
        $shipping1_quantity = 2;
        $shipping2_quantity = 3;

        $I->wantTo('EF0305-UC08-T01 購入確認画面からカートに戻る');
        $I->logoutAsMember();
        $createCustomer = Fixtures::get('createCustomer');
        /** @var \Eccube\Entity\CustomerAddress $customer */
        $customer = $createCustomer();
        $BaseInfo = Fixtures::get('baseinfo');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる($cart_quantity)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());

        $I->resetEmails();

        // -------- EF0305-UC05-T01_お届け先の追加 --------
        ShoppingPage::at($I)->お届け先追加();

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

        // 新規お届け先が追加されていることを確認
        $I->see($nameSei, '#form_shipping_multiple_0_shipping_0_customer_address > option:nth-child(2)');

        // -------- EF0305-UC06-T01_複数配送 - 同じ商品種別（同一配送先） --------
        // 複数配送設定
        MultipleShippingPage::at($I)
            ->お届け先追加()
            ->入力_お届け先('0', '0', $customer->getName01())
            ->入力_お届け先('0', '1', $customer->getName01())
            ->入力_数量('0', '0', $shipping1_quantity)
            ->入力_数量('0', '1', $shipping2_quantity)
            ->選択したお届け先に送る()
        ;

        // 配送先が１個なので数量をまとめる
        $sum_quantity = $shipping1_quantity + $shipping2_quantity;

        // 複数配送設定がされておらず、個数が１明細にまとめられていることを確認
        $I->see('お届け先', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->dontSee('お届け先(1)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->dontSee('お届け先(2)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(6)');
        $I->see(' × '.$sum_quantity, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(3) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($customer->getName01(), '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(4) > p:nth-child(1)');

        // -------- EF0305-UC06-T02_複数配送 - 同じ商品種別（別配送先） --------

        ShoppingPage::at($I)->お届け先追加();

        // 複数配送設定
        MultipleShippingPage::at($I)
            ->お届け先追加()
            ->入力_お届け先('0', '0', $customer->getName01())
            ->入力_お届け先('0', '1', $nameSei)
            ->入力_数量('0', '0', $shipping1_quantity)
            ->入力_数量('0', '1', $shipping2_quantity)
            ->選択したお届け先に送る()
        ;

        // 名前を比較してお届け先が上下どちらに表示されるか判断
        $compared = strnatcmp($customer->getName01(), $nameSei);
        if ($compared === 0) {
            $compared = strnatcmp($customer->getName02(), $nameMei);
        }
        // 上下それぞれで名前、商品個数を設定
        if ($compared < 0) {
            $quantity1 = $shipping1_quantity;
            $quantity2 = $shipping2_quantity;
            $name1 = $customer->getName01();
            $name2 = $nameSei;
        } else {
            $quantity1 = $shipping2_quantity;
            $quantity2 = $shipping1_quantity;
            $name1 = $nameSei;
            $name2 = $customer->getName01();
        }

        // 複数配送設定ができていることを確認
        $I->see('お届け先(1)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->see(' × '.$quantity1, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(3) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($name1, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(4) > p:nth-child(1)');
        $I->see('お届け先(2)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(6)');
        $I->see(' × '.$quantity2, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(7) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($name2, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(8) > p:nth-child(1)');

        // 一旦カートに戻る
        CartPage::go($I)
            ->レジに進む();

        // お届け先が復元されている
        $I->see('お届け先(1)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->see(' × '.$quantity1, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(3) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($name1, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(4) > p:nth-child(1)');
        $I->see('お届け先(2)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(6)');
        $I->see(' × '.$quantity2, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(7) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($name2, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(8) > p:nth-child(1)');

        ShoppingPage::at($I)->確認する();
        ShoppingConfirmPage::at($I)->注文する();

        $I->wait(1);

        // メール確認
        $I->seeEmailCount(2);
        foreach ([$customer->getEmail(), $BaseInfo->getEmail01()] as $email) {
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, $customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前：'.$customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前(カナ)：'.$customer->getKana01().' '.$customer->getKana02().' 様');
            $I->seeInLastEmailTo($email, '郵便番号：〒'.$customer->getPostalCode());
            $I->seeInLastEmailTo($email, '住所：'.$customer->getPref()->getName().$customer->getAddr01().$customer->getAddr02());
            $I->seeInLastEmailTo($email, '電話番号：'.$customer->getPhoneNumber());
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$customer->getEmail());
            $I->seeInLastEmailTo($email, '◎お届け先1');
            $I->seeInLastEmailTo($email, 'お名前：'.$nameSei);
            $I->seeInLastEmailTo($email, '数量：3');
            $I->seeInLastEmailTo($email, '◎お届け先2');
            $I->seeInLastEmailTo($email, '数量：2');
        }

        // 完了画面 -> topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    /**
     * カートに変更があれば、お届け先の設定は初期化される.
     */
    public function order_購入確認画面からカートに戻るWithお届け先初期化(\AcceptanceTester $I)
    {
        // チェック用変数
        // 追加するお届け作の名前
        $nameSei = 'あいおい0302';
        $nameMei = '名0302';
        // カートへ入れる商品の数
        $cart_quantity = 1;
        // お届け先ごとに設定する商品の数
        $shipping1_quantity = 1;
        $shipping2_quantity = 2;

        $I->wantTo('EF0305-UC08-T02 購入確認画面からカートに戻る(お届け先初期化)');
        $I->logoutAsMember();
        $createCustomer = Fixtures::get('createCustomer');
        /** @var \Eccube\Entity\CustomerAddress $customer */
        $customer = $createCustomer();
        $BaseInfo = Fixtures::get('baseinfo');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる($cart_quantity)
            ->カートへ進む();

        CartPage::go($I)
            ->レジに進む();

        // ログイン
        ShoppingLoginPage::at($I)->ログイン($customer->getEmail());

        $I->resetEmails();

        // -------- EF0305-UC05-T01_お届け先の追加 --------
        ShoppingPage::at($I)->お届け先追加();

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

        // 新規お届け先が追加されていることを確認
        $I->see($nameSei, '#form_shipping_multiple_0_shipping_0_customer_address > option:nth-child(2)');

        // -------- EF0305-UC06-T01_複数配送 - 同じ商品種別（同一配送先） --------
        // 複数配送設定
        MultipleShippingPage::at($I)
            ->お届け先追加()
            ->入力_お届け先('0', '0', $customer->getName01())
            ->入力_お届け先('0', '1', $customer->getName01())
            ->入力_数量('0', '0', $shipping1_quantity)
            ->入力_数量('0', '1', $shipping2_quantity)
            ->選択したお届け先に送る()
        ;

        // 配送先が１個なので数量をまとめる
        $sum_quantity = $shipping1_quantity + $shipping2_quantity;

        // 複数配送設定がされておらず、個数が１明細にまとめられていることを確認
        $I->see('お届け先', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->dontSee('お届け先(1)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->dontSee('お届け先(2)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(6)');
        $I->see(' × '.$sum_quantity, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(3) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($customer->getName01(), '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(4) > p:nth-child(1)');

        // -------- EF0305-UC06-T02_複数配送 - 同じ商品種別（別配送先） --------

        ShoppingPage::at($I)->お届け先追加();

        // 複数配送設定
        MultipleShippingPage::at($I)
            ->お届け先追加()
            ->入力_お届け先('0', '0', $customer->getName01())
            ->入力_お届け先('0', '1', $nameSei)
            ->入力_数量('0', '0', $shipping1_quantity)
            ->入力_数量('0', '1', $shipping2_quantity)
            ->選択したお届け先に送る()
        ;

        // 名前を比較してお届け先が上下どちらに表示されるか判断
        $compared = strnatcmp($customer->getName01(), $nameSei);
        if ($compared === 0) {
            $compared = strnatcmp($customer->getName02(), $nameMei);
        }
        // 上下それぞれで名前、商品個数を設定
        if ($compared < 0) {
            $quantity1 = $shipping1_quantity;
            $quantity2 = $shipping2_quantity;
            $name1 = $customer->getName01();
            $name2 = $nameSei;
        } else {
            $quantity1 = $shipping2_quantity;
            $quantity2 = $shipping1_quantity;
            $name1 = $nameSei;
            $name2 = $customer->getName01();
        }

        // 複数配送設定ができていることを確認
        $I->see('お届け先(1)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->see(' × '.$quantity1, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(3) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($name1, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(4) > p:nth-child(1)');
        $I->see('お届け先(2)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(6)');
        $I->see(' × '.$quantity2, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(7) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($name2, '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(8) > p:nth-child(1)');

        // 商品詳細パーコレータ カートへ
        ProductDetailPage::go($I, 2)
            ->カートに入れる($cart_quantity)
            ->カートへ進む();

        // 一旦カートに戻る
        CartPage::go($I)
            ->レジに進む();

        // カートに変更があったため、お届け先を初期化
        $I->see('お届け先', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->dontSee('お届け先(1)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(2)');
        $I->dontSee('お届け先(2)', '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(6)');
        $I->see(' × '.($sum_quantity + $cart_quantity), '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(3) > ul > li:nth-child(1) > div > div.ec-imageGrid__content > p:nth-child(2)');
        $I->see($customer->getName01(), '#shopping-form > div > div.ec-orderRole__detail > div.ec-orderDelivery > div:nth-child(4) > p:nth-child(1)');

        ShoppingPage::at($I)->確認する();
        ShoppingConfirmPage::at($I)->注文する();

        $I->wait(1);

        // メール確認
        $I->seeEmailCount(2);
        foreach ([$customer->getEmail(), $BaseInfo->getEmail01()] as $email) {
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, $customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前：'.$customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前(カナ)：'.$customer->getKana01().' '.$customer->getKana02().' 様');
            $I->seeInLastEmailTo($email, '郵便番号：〒'.$customer->getPostalCode());
            $I->seeInLastEmailTo($email, '住所：'.$customer->getPref()->getName().$customer->getAddr01().$customer->getAddr02());
            $I->seeInLastEmailTo($email, '電話番号：'.$customer->getPhoneNumber());
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$customer->getEmail());
            $I->seeInLastEmailTo($email, '◎お届け先');
            $I->seeInLastEmailTo($email, 'お名前：'.$customer->getName01());
            $I->seeInLastEmailTo($email, '数量：'.($sum_quantity + $cart_quantity));
        }

        // 完了画面 -> topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-secHeading__ja');
    }

    public function order_複数配送設定画面での販売制限エラー(\AcceptanceTester $I)
    {
        /* @var Customer $Customer */
        $Customer = (Fixtures::get('createCustomer'))();

        ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        CartPage::at($I)
            ->レジに進む();

        ShoppingLoginPage::at($I)
            ->ログイン($Customer->getEmail());

        ShoppingPage::at($I)
            ->お届け先追加();

        MultipleShippingPage::at($I)
            ->入力_数量('0', '0', 100)
            ->選択したお届け先に送る();

        ShoppingPage::at($I);

        $I->see('「チェリーアイスサンド」の在庫が不足しております。一度に在庫数を超える購入はできません。', 'div:nth-child(2) > div > div.ec-alert-warning__text');
    }

    public function order_複数ブラウザでログインしてカートに追加する(\AcceptanceTester $I)
    {
        $I->logoutAsMember();
        $I->saveSessionSnapshot('not_login');

        $createCustomer = Fixtures::get('createCustomer');
        /** @var Customer $customer */
        $customer = $createCustomer();

        // ブラウザ1ログイン
        $I->loginAsMember($customer->getEmail(), 'password');
        $I->saveSessionSnapshot('browser1');

        // ブラウザ2ログイン
        $I->loadSessionSnapshot('not_login');
        $I->loginAsMember($customer->getEmail(), 'password');
        $I->saveSessionSnapshot('browser2');

        /*
         * ブラウザ1でカートに商品を入れる
         */
        $I->loadSessionSnapshot('browser1');

        $CartPage = ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        $I->assertEquals(1, $CartPage->明細数());
        $I->assertEquals('チェリーアイスサンド', $CartPage->商品名(1));

        /*
         * ブラウザ2にのカートにも反映されている
         */
        $I->loadSessionSnapshot('browser2');

        $CartPage = CartPage::go($I);

        $I->assertEquals(1, $CartPage->明細数());
        $I->assertEquals('チェリーアイスサンド', $CartPage->商品名(1));
    }

    public function order_複数ブラウザ_片方でログインしてカートに追加しもう一方にログインして別の商品を追加する(\AcceptanceTester $I)
    {
        $I->logoutAsMember();
        $I->saveSessionSnapshot('not_login');

        $createCustomer = Fixtures::get('createCustomer');
        /** @var Customer $customer */
        $customer = $createCustomer();

        // ブラウザ1ログイン
        $I->loginAsMember($customer->getEmail(), 'password');
        $I->saveSessionSnapshot('browser1');

        /*
         * ブラウザ1でカートに商品を入れる
         */
        $I->loadSessionSnapshot('browser1');

        $CartPage = ProductDetailPage::go($I, 2)
            ->カートに入れる(1)
            ->カートへ進む();

        $I->assertEquals(1, $CartPage->明細数());
        $I->assertContains('チェリーアイスサンド', $CartPage->商品名(1));

        /*
         * ブラウザ2で未ログインのまま別の商品を入れる
         */
        $I->loadSessionSnapshot('not_login');
        $CartPage = ProductDetailPage::go($I, 1)
            ->カートに入れる(1, ['1' => 'バニラ'], ['4' => '64cm × 64cm'])
            ->カートへ進む();

        $I->assertEquals(1, $CartPage->明細数());
        $I->assertContains('彩のジェラートCUBE', $CartPage->商品名(1));

        /*
         * ブラウザ2でログインするとブラウザ1のカートとマージされている
         */
        $I->loginAsMember($customer->getEmail(), 'password');

        $CartPage = CartPage::go($I);
        $I->assertEquals(2, $CartPage->明細数());
        $itemNames = $I->grabMultiple(['css' => '.ec-cartRow__name a']);
        $I->assertContains('彩のジェラートCUBE', $itemNames);
        $I->assertContains('チェリーアイスサンド', $itemNames);

        /*
         * ブラウザ1のカートもマージされている
         */
        $I->loadSessionSnapshot('browser1');

        $CartPage = CartPage::go($I);
        $I->assertEquals(2, $CartPage->明細数());
        $itemNames = $I->grabMultiple(['css' => '.ec-cartRow__name a']);
        $I->assertContains('彩のジェラートCUBE', $itemNames);
        $I->assertContains('チェリーアイスサンド', $itemNames);
    }
}

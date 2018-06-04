<?php

use Codeception\Util\Fixtures;
use Page\Front\CartPage;
use Page\Front\ProductDetailPage;
use Page\Front\ShippingEditPage;
use Page\Front\ShoppingCompletePage;
use Page\Front\ShoppingConfirmPage;
use Page\Front\ShoppingLoginPage;
use Page\Front\ShoppingPage;

/**
 * @group front
 * @group order
 * @group ef3
 */
class EF03OrderCest
{
    public function _before(\AcceptanceTester $I)
    {
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
            ->カートに入れる(1);

        $I->acceptPopup();

        CartPage::go($I)
            ->お買い物を続ける();

        // トップページ
        $I->see('新着情報', '.ec-news__title');
    }

    public function order_カート削除(\AcceptanceTester $I)
    {
        $I->wantTo('EF0301-UC01-T02 カート 削除');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $I->loginAsMember($customer->getEmail(), 'password');

        ProductDetailPage::go($I, 2)
            ->カートに入れる(1);

        $I->acceptPopup();

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
            ->カートに入れる(1);

        $I->acceptPopup();

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
            ->カートに入れる(2);

        $I->acceptPopup();

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
            ->カートに入れる(1);

        $I->acceptPopup();

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
        foreach (array($customer->getEmail(), $BaseInfo->getEmail01()) as $email) {
            // TODO 注文した商品の内容もチェックしたい
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, $customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前　：'.$customer->getName01().' '.$customer->getName02().' 様');
            $I->seeInLastEmailTo($email, 'お名前(フリガナ)：'.$customer->getKana01().' '.$customer->getKana02().' 様');
            $I->seeInLastEmailTo($email, '郵便番号：〒'.$customer->getZip01().'-'.$customer->getZip02());
            $I->seeInLastEmailTo($email, '住所　　：'.$customer->getPref()->getName().$customer->getAddr01().$customer->getAddr02());
            $I->seeInLastEmailTo($email, '電話番号：'.$customer->getTel01().'-'.$customer->getTel02().'-'.$customer->getTel03());
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$customer->getEmail());
        }

        // 完了画面 -> topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-news__title');
    }

    public function order_ゲスト購入(\AcceptanceTester $I)
    {
        $I->wantTo('EF0302-UC02-T01 ゲスト購入');
        $I->logoutAsMember();

        $faker = Fixtures::get('faker');
        $new_email = microtime(true).'.'.$faker->safeEmail;
        $BaseInfo = Fixtures::get('baseinfo');

        ProductDetailPage::go($I, 2)
            ->カートに入れる(1);

        $I->acceptPopup();

        CartPage::go($I)
            ->レジに進む();

        $ShoppingPage = ShoppingLoginPage::at($I)->ゲスト購入();
        $ShoppingPage
            ->入力_姓('姓03')
            ->入力_名('名03')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号1('530')
            ->入力_郵便番号2('0001');

        // TODO: 郵便番号入力後のcodeceptionの入力後にJSが走ってしまい「梅田」が2重で入力されてしまう。
        // 上記を回避するためにwait関数を入れる。
        // こちらは本体のmasterブランチで修正されているので、master -> sf マージ後には不要になる見込み。
        $I->wait(5);

        $ShoppingPage
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号1('111')
            ->入力_電話番号2('111')
            ->入力_電話番号3('111')
            ->入力_Eメール($new_email)
            ->入力_Eメール確認($new_email)
            ->次へ();

        $I->resetEmails();

        ShoppingPage::at($I)->確認する();
        ShoppingConfirmPage::at($I)->注文する();

        $I->wait(1);

        // 確認
        $I->seeEmailCount(2);
        foreach (array($new_email, $BaseInfo->getEmail01()) as $email) {
            // TODO 注文した商品の内容もチェックしたい
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, '姓03 名03 様');
            $I->seeInLastEmailTo($email, 'お名前　：姓03 名03 様');
            $I->seeInLastEmailTo($email, 'お名前(フリガナ)：セイ メイ 様');
            $I->seeInLastEmailTo($email, '郵便番号：〒530-0001');
            $I->seeInLastEmailTo($email, '住所　　：大阪府大阪市北区梅田2-4-9 ブリーゼタワー13F');
            $I->seeInLastEmailTo($email, '電話番号：111-111-111');
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$new_email);
        }

        // 完了画面 -> topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-news__title');
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
            ->カートに入れる(1);

        $I->acceptPopup();

        CartPage::go($I)
            ->レジに進む();

        $ShoppingPage = ShoppingLoginPage::at($I)->ゲスト購入();
        $ShoppingPage
            ->入力_姓('姓03')
            ->入力_名('名03')
            ->入力_セイ('セイ')
            ->入力_メイ('メイ')
            ->入力_郵便番号1('530')
            ->入力_郵便番号2('0001');

        // TODO: 郵便番号入力後のcodeceptionの入力後にJSが走ってしまい「梅田」が2重で入力されてしまう。
        // 上記を回避するためにwait関数を入れる。
        // こちらは本体のmasterブランチで修正されているので、master -> sf マージ後には不要になる見込み。
        $I->wait(5);

        $ShoppingPage
            ->入力_都道府県(['value' => '27'])
            ->入力_市区町村名('大阪市北区')
            ->入力_番地_ビル名('梅田2-4-9 ブリーゼタワー13F')
            ->入力_電話番号1('111')
            ->入力_電話番号2('111')
            ->入力_電話番号3('111')
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
        foreach (array($new_email, $BaseInfo->getEmail01()) as $email) {
            // TODO 注文した商品の内容もチェックしたい
            $I->seeInLastEmailSubjectTo($email, 'ご注文ありがとうございます');
            $I->seeInLastEmailTo($email, '姓0301 名03 様');
            $I->seeInLastEmailTo($email, 'お名前　：姓0302 名03 様', '変更後のお届け先');
            $I->seeInLastEmailTo($email, '郵便番号：〒530-0001');
            $I->seeInLastEmailTo($email, '住所　　：大阪府大阪市北区梅田2-4-9 ブリーゼタワー13F');
            $I->seeInLastEmailTo($email, '電話番号：111-111-111');
            $I->seeInLastEmailTo($email, 'メールアドレス：'.$new_email);
        }

        // topへ
        ShoppingCompletePage::at($I)->TOPへ();
        $I->see('新着情報', '.ec-news__title');
    }
}

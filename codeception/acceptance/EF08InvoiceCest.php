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
use Page\Admin\PaymentEditPage;
use Page\Admin\PaymentManagePage;
use Page\Admin\ProductEditPage;
use Page\Admin\ShopSettingPage;
use Page\Front\CartPage;
use Page\Front\ShoppingConfirmPage;
use Page\Front\ShoppingPage;
use Page\Front\TopPage;

/**
 * @group front
 * @group invoice
 * @group ef8
 */
class EF08InvoiceCest
{
    public function _before(AcceptanceTester $I)
    {
        $entityManager = Fixtures::get('entityManager');
        $I->logoutAsMember();
        $I->loginAsAdmin();

        $I->expect('会員を準備します');
        $createCustomer = Fixtures::get('createCustomer');
        $customer = $createCustomer();
        $customer->setPoint(10000);
        $entityManager->flush();
        $I->loginAsMember($customer->getEmail(), 'password');

        $I->expect('店舗設定をします');

        $ShopSetting = ShopSettingPage::go($I);
        $I->scrollTo(['css' => 'label[for='.ShopSettingPage::$チェックボックス_商品別税率機能.']'], 0, 100);
        $I->wait(2);
        $ShopSetting->入力_チェックボックス(ShopSettingPage::$チェックボックス_商品別税率機能, true)
            ->登録();

        $I->expect('支払方法の設定をします');
        PaymentManagePage::go($I)
            ->一覧_編集(1);
        PaymentEditPage::at($I)
            ->入力_手数料('2187')
            ->登録();

        $I->expect('商品登録をします');
        ProductEditPage::go($I)
            ->入力_商品名('チョコ')
            ->入力_販売価格('71141')
            ->入力_カテゴリ(1)
            ->入力_公開()
            ->登録();
        $I->waitForText('保存しました');

        ProductEditPage::go($I)
            ->入力_商品名('バニラ')
            ->入力_販売価格('92778')
            ->入力_カテゴリ(1)
            ->入力_公開()
            ->登録();
        $I->waitForText('保存しました');

        ProductEditPage::go($I)
            ->入力_商品名('抹茶')
            ->入力_販売価格('15221')
            ->入力_カテゴリ(1)
            ->入力_税率(8)
            ->入力_公開()
            ->登録();
        $I->waitForText('保存しました');
    }

    public function invoice_商品購入_税額確認(AcceptanceTester $I)
    {
        $I->wantTo('EF0801-UC01-T01_商品購入_税額確認');

        $I->expect('商品をカートに入れます');
        TopPage::go($I)
            ->検索('チョコ')
            ->カートに入れる(1, 4);
        TopPage::go($I)
            ->検索('バニラ')
            ->カートに入れる(1, 4);
        TopPage::go($I)
            ->検索('抹茶')
            ->カートに入れる(1, 4);

        $Cart = CartPage::go($I);
        $I->scrollTo(['css' => '.ec-blockBtn--action'], 0, 200);
        $I->wait(2);
        $Cart->レジに進む();
        ShoppingPage::at($I)
            ->選択_配送方法(1)
            ->入力_利用ポイント(1000);

        $I->expect('価格を確認します');
        $I->see('￥787,000', ['xpath' => ShoppingPage::$小計]);
        $I->see('￥2,187', ['xpath' => ShoppingPage::$手数料]);
        $I->see('￥1,000', ['xpath' => ShoppingPage::$送料]);
        $I->see('￥790,187', ['xpath' => ShoppingPage::$合計]);
        $I->see('-￥1,000', ['xpath' => ShoppingPage::$ポイント値引き額]);
        $I->see('￥789,187', ['xpath' => ShoppingPage::$お支払い合計]);
        $I->see('￥65,673 (内消費税 ￥4,865)', ['xpath' => ShoppingPage::$税率8パーセント対象]);
        $I->see('￥723,514 (内消費税 ￥65,774)', ['xpath' => ShoppingPage::$税率10パーセント対象]);

        $I->scrollTo('#shopping-form div.ec-orderRole__summary div.ec-totalBox button');
        $I->wait(2);
        ShoppingPage::at($I)->確認する();
        $I->waitForText('ご注文内容のご確認', 10, 'div.ec-pageHeader h1');

        $I->expect('注文内容を確認します');
        $I->see('￥787,000', ['xpath' => ShoppingPage::$小計]);
        $I->see('￥2,187', ['xpath' => ShoppingPage::$手数料]);
        $I->see('￥1,000', ['xpath' => ShoppingPage::$送料]);
        $I->see('￥790,187', ['xpath' => ShoppingPage::$合計]);
        $I->see('-￥1,000', ['xpath' => ShoppingPage::$ポイント値引き額]);
        $I->see('￥789,187', ['xpath' => ShoppingPage::$お支払い合計]);
        $I->see('￥65,673 (内消費税 ￥4,865)', ['xpath' => ShoppingPage::$税率8パーセント対象]);
        $I->see('￥723,514 (内消費税 ￥65,774)', ['xpath' => ShoppingPage::$税率10パーセント対象]);

        $I->scrollTo('#shopping-form div.ec-orderRole__summary div.ec-totalBox button');
        $I->wait(2);

        ShoppingConfirmPage::at($I)->注文する();
    }
}

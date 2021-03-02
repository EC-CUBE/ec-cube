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

use Page\Admin\CsvSettingsPage;
use Page\Admin\DeliveryEditPage;
use Page\Admin\DeliveryManagePage;
use Page\Admin\MailSettingsPage;
use Page\Admin\OrderStatusSettingsPage;
use Page\Admin\PaymentEditPage;
use Page\Admin\PaymentManagePage;
use Page\Admin\ShopSettingPage;
use Page\Admin\TaxManagePage;
use Codeception\Util\Fixtures;

/**
 * @group admin
 * @group admin03
 * @group basicinformation
 * @group ea7
 */
class EA07BasicinfoCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function basicinfo_基本設定(\AcceptanceTester $I)
    {
        $I->wantTo('EA0701-UC01-T01 基本設定');

        ShopSettingPage::go($I)
            ->入力_会社名('会社名')
            ->登録();

        $I->see('保存しました', ShopSettingPage::$登録完了メッセージ);
    }

    public function basicinfo_支払方法一覧(\AcceptanceTester $I)
    {
        $I->wantTo('EA0704-UC01-T01 支払方法 一覧');

        // 表示
        $PaymentManagePage = PaymentManagePage::go($I);

        $I->see('郵便振替', $PaymentManagePage->一覧_支払方法(1));
    }

    public function basicinfo_支払方法入れ替え(\AcceptanceTester $I)
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

    public function basicinfo_支払方法登録(\AcceptanceTester $I)
    {
        $I->getScenario()->skip('EA0705-UC01-T01 支払方法 登録');
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

    public function basicinfo_支払方法編集(\AcceptanceTester $I)
    {
        $I->getScenario()->skip('EA0705-UC01-T01 支払方法 登録');
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

    public function basicinfo_支払方法削除(\AcceptanceTester $I)
    {
        $I->wantTo('EA0704-UC03-T01 支払方法 削除');

        // 表示
        // 削除
        PaymentManagePage::go($I)
            ->一覧_削除(1);
    }

    public function basicinfo_配送方法一覧(\AcceptanceTester $I)
    {
        $I->wantTo('EA0706-UC01-T01 配送方法 一覧');

        // 表示
        $DeliveryManagePage = DeliveryManagePage::go($I);

        $I->see('サンプル宅配', $DeliveryManagePage->一覧_名称(2));
    }

    public function basicinfo_配送方法登録(\AcceptanceTester $I)
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

    public function basicinfo_配送方法編集(\AcceptanceTester $I)
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

    public function basicinfo_配送方法削除(\AcceptanceTester $I)
    {
        $I->wantTo('EA0706-UC03-T01 配送方法 削除');

        DeliveryManagePage::go($I)
            ->一覧_削除(2);
    }

    public function basicinfo_配送方法一覧順序変更(\AcceptanceTester $I)
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

    public function basicinfo_税率設定(\AcceptanceTester $I)
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

    public function basicinfo_メール設定(\AcceptanceTester $I)
    {
        $I->wantTo('EA0709-UC02-T01  メール設定'); // EA0709-UC01-T01 はメールテンプレート登録機能がないのでテスト不可

        // 表示
        MailSettingsPage::go($I)
            ->入力_テンプレート('注文受付メール')
            ->入力_件名('ご注文有難うございました')
            ->登録();

        $I->see('保存しました', MailSettingsPage::$登録完了メッセージ);
    }

    public function basicinfo_CSV出力項目(\AcceptanceTester $I)
    {
        $I->wantTo('EA0710-UC01-T01  CSV出力項目設定');

        // 表示
        CsvSettingsPage::go($I)
            ->入力_CSVタイプ('受注CSV')
            ->選択_出力項目('誕生日')
            ->削除()
            ->設定();

        $I->see('保存しました', CsvSettingsPage::$登録完了メッセージ);
    }

    public function basicinfo_受注対応状況設定(\AcceptanceTester $I)
    {
        $I->wantTo('EA0711-UC01-T01  受注対応状況設定');

        // 表示
        OrderStatusSettingsPage::go($I)
            ->入力_名称_管理('新規受付')
            ->入力_名称_マイページ('注文受付')
            ->入力_色("#19406C")
            ->登録();

        $I->see('保存しました', OrderStatusSettingsPage::$登録完了メッセージ);
    }

    /**
     * EA10PluginCestではテストが失敗するため、ここでテストを行う
     */
    public function basicinfo_認証キー設定(\AcceptanceTester $I)
    {
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

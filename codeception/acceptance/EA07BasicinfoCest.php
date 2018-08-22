<?php

use Page\Admin\CsvSettingsPage;
use Page\Admin\DeliveryEditPage;
use Page\Admin\DeliveryManagePage;
use Page\Admin\MailSettingsPage;
use Page\Admin\PaymentEditPage;
use Page\Admin\PaymentManagePage;
use Page\Admin\ShopSettingPage;
use Page\Admin\TaxManagePage;

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

    public function basicinfo_ショップマスター(\AcceptanceTester $I)
    {
        $I->wantTo('EA0701-UC01-T01 ショップマスター');

        ShopSettingPage::go($I)
            ->入力_会社名('会社名')
            ->登録();

        $I->see('登録が完了しました。', ShopSettingPage::$登録完了メッセージ);
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

        $PaymentManagePage = PaymentManagePage::at($I);
        $I->see('登録が完了しました。', PaymentManagePage::$登録完了メッセージ);
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

        $PaymentManagePage = PaymentManagePage::at($I);
        $I->see('登録が完了しました。', PaymentManagePage::$登録完了メッセージ);
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

        $I->see('サンプル宅配', $DeliveryManagePage->一覧_名称(1));
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

        $DeliveryManagePage = DeliveryManagePage::at($I);
        $I->see('登録が完了しました。', DeliveryManagePage::$登録完了メッセージ);
        $I->see('配送業者名', $DeliveryManagePage->一覧_名称(1));
    }

    public function basicinfo_配送方法編集(\AcceptanceTester $I)
    {
        $I->wantTo('EA0707-UC02-T01 配送方法 編集');

        // 表示
        DeliveryManagePage::go($I)
            ->一覧_編集(1);

        // 編集
        DeliveryEditPage::at($I)
            ->入力_配送業者名('配送業者名1')
            ->登録();

        $DeliveryManagePage = DeliveryManagePage::at($I);
        $I->see('登録が完了しました。', DeliveryManagePage::$登録完了メッセージ);
        $I->see('配送業者名1', $DeliveryManagePage->一覧_名称(1));
    }

    public function basicinfo_配送方法削除(\AcceptanceTester $I)
    {
        $I->wantTo('EA0706-UC03-T01 配送方法 削除');

        DeliveryManagePage::go($I)
            ->一覧_削除(1);
    }

    public function basicinfo_配送方法一覧順序変更(\AcceptanceTester $I)
    {
        $I->wantTo('EA0706-UC02-T01 配送方法一覧順序変更');

        $DeliveryManagePage = DeliveryManagePage::go($I);
        $I->see('サンプル宅配 / サンプル宅配', $DeliveryManagePage->一覧_名称(1));
        $I->see('サンプル業者 / サンプル業者', $DeliveryManagePage->一覧_名称(2));

        $DeliveryManagePage->一覧_下に(1);
        $I->see('サンプル業者 / サンプル業者', $DeliveryManagePage->一覧_名称(1));
        $I->see('サンプル宅配 / サンプル宅配', $DeliveryManagePage->一覧_名称(2));

        $DeliveryManagePage->一覧_上に(2);
        $I->see('サンプル宅配 / サンプル宅配', $DeliveryManagePage->一覧_名称(1));
        $I->see('サンプル業者 / サンプル業者', $DeliveryManagePage->一覧_名称(2));
    }

    public function basicinfo_税率設定(\AcceptanceTester $I)
    {
        $I->wantTo('EA0708-UC01-T01 税率設定');

        // 表示
        $TaxManagePage = TaxManagePage::go($I);

        // 一覧
        $I->see('共通税率設定', '#page_admin_setting_shop_tax > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div.card-header');
        $I->see('8%', '#ex-tax_rule-1 > td.align-middle.text-right');

        // 登録
        $TaxManagePage
            ->入力_消費税率(1, '10')
            ->入力_適用日(date('Y-m-d'))
            ->入力_適用時(date('H:i'))
            ->共通税率設定_登録();
        $I->see('10%', $TaxManagePage->一覧_税率(2));

        // edit
        $TaxManagePage
            ->一覧_編集(2)
            ->入力_消費税率(2, 12)
            ->決定(2);

        $I->see('税率設定情報を保存しました。', TaxManagePage::$登録完了メッセージ);
        $I->see('12%', $TaxManagePage->一覧_税率(2));

        // 削除
        $TaxManagePage->一覧_削除(2);
        $I->see('税率設定情報を削除しました。', TaxManagePage::$登録完了メッセージ);
    }

    public function basicinfo_メール設定(\AcceptanceTester $I)
    {
        $I->wantTo('EA0709-UC02-T01  メール設定'); // EA0709-UC01-T01 はメールテンプレート登録機能がないのでテスト不可

        // 表示
        MailSettingsPage::go($I)
            ->入力_テンプレート('注文受付メール')
            ->入力_件名('ご注文有難うございました')
            ->登録();

        $I->see('メールテンプレート情報を保存しました。', MailSettingsPage::$登録完了メッセージ);
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

        $I->see('CSV出力を設定しました。', CsvSettingsPage::$登録完了メッセージ);
    }
}

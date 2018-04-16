<?php

use Codeception\Util\Fixtures;
use Page\Admin\CsvSettingsPage;
use Page\Admin\ShippingManagePage;
use Page\Admin\ShippingEditPage;
use Page\Admin\OrderEditPage;
use Eccube\Entity\Master\ShippingStatus;

/**
 * @group admin
 * @group admin01
 * @group shipping
 * @group ea9
 */
class EA09ShippingCest
{
    public function _before(\AcceptanceTester $I)
    {
        // すべてのテストケース実施前にログインしておく
        // ログイン後は管理アプリのトップページに遷移している
        $I->loginAsAdmin();
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function shipping出荷検索(\AcceptanceTester $I)
    {
        $I->wantTo('EA0901-UC01-T01(& UC01-T02) 出荷検索');

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();
        ShippingManagePage::go($I);
        $I->see('検索結果 : '.count($Shippings).' 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        ShippingManagePage::go($I)->検索('gege@gege.com');
        $I->see('検索結果 : 0 件が該当しました', ShippingManagePage::$検索結果_メッセージ);
    }

    /**
     * @env firefox
     * @env chrome
     */
    public function shipping出荷CSVダウンロード(\AcceptanceTester $I)
    {
        $I->wantTo('EA0901-UC02-T01 出荷CSVダウンロード');

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();
        $ShippingListPage = ShippingManagePage::go($I);
        $I->see('検索結果 : '.count($Shippings).' 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        $ShippingListPage->出荷CSVダウンロード実行();
        // make sure wait to download file completely
        $I->wait(10);
        $ShippingCSV = $I->getLastDownloadFile('/^shipping_\d{14}\.csv$/');
        $I->assertGreaterOrEquals(count($Shippings), count(file($ShippingCSV)), '検索結果以上の行数があるはず');
    }

    public function shipping出荷情報のCSV出力項目変更設定(\AcceptanceTester $I)
    {
        $I->wantTo('EA0901-UC02-T02 出荷情報のCSV出力項目変更設定');

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();
        $ShippingListPage = ShippingManagePage::go($I);
        $I->see('検索結果 : '.count($Shippings).' 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        /* 項目設定 */
        $ShippingListPage->出荷CSV出力項目設定();

        CsvSettingsPage::at($I);
        $value = $I->grabValueFrom(CsvSettingsPage::$CSVタイプ);
        $I->assertEquals(4, $value);
    }

    public function shipping出荷編集(\AcceptanceTester $I)
    {
        $I->wantTo('EA0901-UC03-T01(& UC03-T02) 出荷編集');

        $I->resetEmails();

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();
        $ShippingListPage = ShippingManagePage::go($I);
        $I->see('検索結果 : '.count($Shippings).' 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        /* 編集 */
        $ShippingListPage->一覧_編集(1);

        $ShippingRegisterPage = ShippingEditPage::at($I)
            ->お届け先編集()
            ->入力_姓('')
            ->出荷情報登録();

        /* 異常系 */
        // FIXME お届け先編集が閉じてしまうため、エラーメッセージが表示されない
        // $I->see('入力されていません。', ShippingEditPage::$姓_エラーメッセージ);

        /* 正常系 */
        $ShippingRegisterPage
            ->お届け先編集()
            ->入力_姓('aaa')
            ->入力_セイ('アアア')
            ->入力_メイ('アアア')
            ->入力_郵便番号1('060')
            ->入力_郵便番号2('0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_電話番号1('111')
            ->入力_電話番号2('111')
            ->入力_電話番号3('111')
            ->入力_番地_ビル名('address 2')
            ->出荷情報登録();

        $I->see('出荷情報を登録しました。', ShippingEditPage::$登録完了メッセージ);

        /* ステータス変更 */
        $ShippingRegisterPage
            ->入力_出荷ステータス(['2' => '出荷済み'])
            ->出荷情報登録()
            ->変更を確定();
        $I->wait(1);
        $I->see('出荷情報を登録しました。', ShippingEditPage::$登録完了メッセージ);

        $I->wait(3);
        $I->seeEmailCount(2);
    }

    public function shipping出荷削除(\AcceptanceTester $I)
    {
        $I->wantTo('EA0901-UC04-T01(& UC04-T02) 出荷削除');

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();
        $ShippingListPage = ShippingManagePage::go($I);
        $I->see('検索結果 : '.count($Shippings).' 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        // 削除
        $ShippingListPage->一覧_チェックボックス(1);
        $ShippingListPage->一覧_削除();

        $I->waitForElementVisible(['xpath' => '//*[@id="page_admin_shipping"]/div[1]/div[3]/div[2]/span']);
        $I->see('出荷情報を削除しました。', ['xpath' => '//*[@id="page_admin_shipping"]/div[1]/div[3]/div[2]/span']);

        // 削除キャンセル
        $ShippingListPage->一覧_チェックボックス(1);
        $ShippingListPage->一覧_削除キャンセル();
    }

    public function shipping一括発送済み更新(\AcceptanceTester $I)
    {
        $I->wantTo('EA0902-UC01-T01 一括発送済み更新');

        $I->resetEmails();

        $config = Fixtures::get('config');
        // ステータスを出荷準備中にリセット
        $resetShippingStatusPrepared = Fixtures::get('resetShippingStatusPrepared'); // Closure
        $resetShippingStatusPrepared();

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();
        $ShippingListPage = ShippingManagePage::go($I);
        $I->see('検索結果 : '.count($Shippings).' 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        $ShippingListPage
            ->一覧_全選択()
            ->一括発送済み更新();

        $I->wait(5);
        $I->waitForElementVisible(['xpath' => '//*[@id="sentUpdateModal"]/div/div/div[2]/p']);
        $I->see('処理完了', ['xpath' => '//*[@id="sentUpdateModal"]/div/div/div[2]/p']);
        $I->seeEmailCount(18);  // XXX 何故か travis では 18 になる

        $I->click(['id' => 'bulkChangeComplete']);
    }

    public function shipping出荷登録(\AcceptanceTester $I)
    {
        $I->wantTo('EA0903-UC01-T01(& UC01-T02) 出荷登録');

        $OrderRegisterPage = OrderEditPage::go($I)->受注情報登録();

        /* 正常系 */
        $OrderRegisterPage
            ->入力_受注ステータス(['1' => '新規受付'])
            ->入力_姓('order1')
            ->入力_名('order1')
            ->入力_セイ('アアア')
            ->入力_メイ('アアア')
            ->入力_郵便番号1('060')
            ->入力_郵便番号2('0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_Eメール('test@test.com')
            ->入力_電話番号1('111')
            ->入力_電話番号2('111')
            ->入力_電話番号3('111')
            ->商品検索('パーコレーター')
            ->商品検索結果_選択(1)
            ->入力_支払方法(['4'=> '郵便振替'])
            ->受注情報登録();

        $ShippingRegisterPage = ShippingEditPage::go($I)->出荷情報登録();

        /* 異常系 */
        $I->dontSee('出荷情報を保存しました。', ShippingEditPage::$登録完了メッセージ);


        /* 正常系 */
        $ShippingRegisterPage
            ->入力_姓('shipping1')
            ->入力_名('shipping1')
            ->入力_セイ('アアア')
            ->入力_メイ('アアア')
            ->入力_郵便番号1('060')
            ->入力_郵便番号2('0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_電話番号1('111')
            ->入力_電話番号2('111')
            ->入力_電話番号3('111')
            ->入力_出荷伝票番号('1111-1111-1111')
            ->入力_配送業者([1 => 'サンプル業者'])
            ->商品検索()
            ->商品検索結果_選択(1)
            ->出荷情報登録();

        $I->see('出荷情報を登録しました。', ShippingEditPage::$登録完了メッセージ);
    }
}

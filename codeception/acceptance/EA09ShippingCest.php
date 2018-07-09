<?php

use Codeception\Util\Fixtures;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Page\Admin\CsvSettingsPage;
use Page\Admin\OrderEditPage;
use Page\Admin\ShippingCsvUploadPage;
use Page\Admin\ShippingEditPage;
use Page\Admin\ShippingManagePage;

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
        $I->wantTo('EA0901-UC01-T01(& UC01-T02, UC01-T3) 出荷検索');

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();
        ShippingManagePage::go($I);
        $I->see('検索結果 : '.count($Shippings).' 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        ShippingManagePage::go($I)->検索('gege@gege.com');
        $I->see('検索結果 : 0 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        ShippingManagePage::go($I)->詳細検索_電話番号('あああ');
        $I->see('検索条件に誤りがあります', ShippingManagePage::$検索結果_エラーメッセージ);
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
            ->入力_郵便番号('060-0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_電話番号('111-111-111')
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

        // 一括操作用の受注を生成しておく
        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        $createOrders($createCustomer(), 10, array());

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
        $I->see('処理完了。10件のメールを送信しました', ['xpath' => '//*[@id="sentUpdateModal"]/div/div/div[2]/p']);
        $I->seeEmailCount(20);

        $I->click(['id' => 'bulkChangeComplete']);
    }

    public function shipping一括発送済みメール送信(\AcceptanceTester $I)
    {
        $I->wantTo('EA0902-UC02-T01 一括発送済みメール送信');

        // 一括操作用の受注を生成しておく
        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        $createOrders($createCustomer(), 10, array());

        $I->resetEmails();

        $config = Fixtures::get('config');
        // ステータスを出荷済みにリセット
        $resetShippingStatusShipped = Fixtures::get('resetShippingStatusShipped'); // Closure
        $resetShippingStatusShipped();

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();
        $ShippingListPage = ShippingManagePage::go($I);
        $I->see('検索結果 : '.count($Shippings).' 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        $ShippingListPage
            ->一覧_全選択()
            ->一括発送済みメール送信();

        $I->wait(5);
        $I->waitForElementVisible(['xpath' => '//*[@id="sentUpdateModal"]/div/div/div[2]/p']);
        $I->see('処理完了。10件のメールを送信しました', ['xpath' => '//*[@id="sentUpdateModal"]/div/div/div[2]/p']);
        $I->seeEmailCount(20);

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
            ->入力_郵便番号('060-0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_Eメール('test@test.com')
            ->入力_電話番号('111-111-111')
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
            ->入力_郵便番号('060-0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_電話番号('111-111-111')
            ->入力_出荷伝票番号('1111-1111-1111')
            ->入力_配送業者([1 => 'サンプル業者'])
            ->入力_配達用メモ('メモ')
            ->商品検索()
            ->商品検索結果_選択(1)
            ->出荷情報登録();

        $I->see('出荷情報を登録しました。', ShippingEditPage::$登録完了メッセージ);
    }

    public function shipping_出荷CSV登録(\AcceptanceTester $I)
    {
        $I->wantTo('EA0903-UC04-T01 出荷CSV登録');

        /* @var Customer $Customer */
        $Customer = (Fixtures::get('createCustomer'))();
        /* @var Order[] $Orders */
        $Orders = (Fixtures::get('createOrders'))($Customer, 3);

        /*
         * 出荷再検索 出荷日/伝票番号が登録されていないことを確認
         */

        $ShippingManagePage = ShippingManagePage::go($I)
            ->詳細検索設定()
            ->入力_ご注文者お名前($Customer->getName01().$Customer->getName02())
            ->入力_ご注文者お名前フリガナ($Customer->getKana01().$Customer->getKana02())
            ->検索();

        $I->see('検索結果 : 3 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

        $I->assertEquals('未登録', $ShippingManagePage->取得_出荷伝票番号(1));
        $I->assertEquals('未登録', $ShippingManagePage->取得_出荷伝票番号(2));
        $I->assertEquals('未登録', $ShippingManagePage->取得_出荷伝票番号(3));
        $I->assertEquals('-', $ShippingManagePage->取得_出荷日(1));
        $I->assertEquals('-', $ShippingManagePage->取得_出荷日(2));
        $I->assertEquals('-', $ShippingManagePage->取得_出荷日(3));

        /*
         * 出荷CSV登録
         */

        $csv = implode(PHP_EOL, [
            '出荷ID,出荷伝票番号,出荷日',
            $Orders[0]->getShippings()[0]->getId().',00001,2018-01-01',
            $Orders[1]->getShippings()[0]->getId().',00002,2018-02-02',
            $Orders[2]->getShippings()[0]->getId().',00003,2018-03-03',
        ]);

        $csvFileName = codecept_data_dir().'/shipping.csv';
        file_put_contents($csvFileName, $csv);

        try {

            ShippingCsvUploadPage::go($I)
                ->入力_CSVファイル('shipping.csv')
                ->CSVアップロード();

            $I->see('出荷登録CSVファイルをアップロードしました。', ShippingCsvUploadPage::$完了メッセージ);

            /*
             * 出荷再検索 出荷日/伝票番号が登録されたことを確認
             */

            $ShippingManagePage = ShippingManagePage::go($I)
                ->詳細検索設定()
                ->入力_ご注文者お名前($Customer->getName01().$Customer->getName02())
                ->入力_ご注文者お名前フリガナ($Customer->getKana01().$Customer->getKana02())
                ->検索();

            $I->see('検索結果 : 3 件が該当しました', ShippingManagePage::$検索結果_メッセージ);

            $I->assertEquals('00003', $ShippingManagePage->取得_出荷伝票番号(1));
            $I->assertEquals('00002', $ShippingManagePage->取得_出荷伝票番号(2));
            $I->assertEquals('00001', $ShippingManagePage->取得_出荷伝票番号(3));
            $I->assertEquals('2018/03/03', $ShippingManagePage->取得_出荷日(1));
            $I->assertEquals('2018/02/02', $ShippingManagePage->取得_出荷日(2));
            $I->assertEquals('2018/01/01', $ShippingManagePage->取得_出荷日(3));

        } finally {
            if (file_exists($csvFileName)) {
                unlink($csvFileName);
            }
        }
    }

    public function shipping_出荷CSV雛形ファイルダウンロード(\AcceptanceTester $I)
    {
        $I->wantTo('EA0093-UC04-T02 出荷CSV雛形ファイルのダウンロード');

        ShippingCsvUploadPage::go($I)->雛形ダウンロード();
        $csv = $I->getLastDownloadFile('/^shipping\.csv$/');
        $I->assertEquals(mb_convert_encoding(file_get_contents($csv), 'UTF-8', 'Shift_JIS'), '出荷ID,出荷伝票番号,出荷日'.PHP_EOL);
    }
}

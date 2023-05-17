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
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Page\Admin\OrderEditPage;
use Page\Admin\OrderManagePage;
use Page\Admin\ShippingCsvUploadPage;
use Page\Admin\ShippingEditPage;

/**
 * @group admin
 * @group admin01
 * @group shipping
 * @group ea9
 */
class EA09ShippingCest
{
    public function _before(AcceptanceTester $I)
    {
        // すべてのテストケース実施前にログインしておく
        // ログイン後は管理アプリのトップページに遷移している
        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function shipping出荷編集(AcceptanceTester $I)
    {
        $I->wantTo('EA0901-UC03-T01(& UC03-T02) 出荷編集');

        $I->resetEmails();

        // 対応中ステータスの受注を作る
        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        /** @var Order[] $newOrders */
        $newOrders = $createOrders($createCustomer(), 1, [], OrderStatus::IN_PROGRESS);

        $OrderListPage = OrderManagePage::go($I)->検索($newOrders[0]->getOrderNo());

        $I->see('検索結果：1件が該当しました', OrderManagePage::$検索結果_メッセージ);

        /* 編集 */
        $OrderListPage->一覧_編集(1);

        $OrderRegisterPage = OrderEditPage::at($I)
            ->お届け先の追加();

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();

        $ShippingRegisterPage = ShippingEditPage::at($I)
            ->入力_姓('')
            ->出荷情報登録();

        /* 異常系 */
        $I->see('入力されていません。', ShippingEditPage::$姓_エラーメッセージ);

        /* 正常系 */
        $ShippingRegisterPage
            // ->お届け先編集()
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

        $I->see('保存しました', ShippingEditPage::$登録完了メッセージ);

        $I->wait(10);

        // 出荷済みに変更
        $ShippingRegisterPage
            ->出荷完了にする()
            ->変更を確定()
            ->出荷日を確認();
    }

    /**
     * @group vaddy
     */
    public function shippingお届け先追加(AcceptanceTester $I)
    {
        $I->wantTo('EA0901-UC03-T03 お届け先追加');

        $I->resetEmails();

        // 対応中ステータスの受注を作る
        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        /** @var Order[] $newOrders */
        $newOrders = $createOrders($createCustomer(), 1, [], OrderStatus::IN_PROGRESS);

        $OrderListPage = OrderManagePage::go($I)->検索($newOrders[0]->getOrderNo());

        $I->see('検索結果：1件が該当しました', OrderManagePage::$検索結果_メッセージ);

        /* 編集 */
        $OrderListPage->一覧_編集(1);

        $OrderRegisterPage = OrderEditPage::at($I)
            ->お届け先の追加();

        $TargetShippings = Fixtures::get('findShippings'); // Closure
        $Shippings = $TargetShippings();

        $ShippingRegisterPage = ShippingEditPage::at($I);
        $ShippingRegisterPage
            ->出荷先を追加()
            ->商品検索('チェリーアイスサンド')
            ->商品検索結果_選択(1);

        /* 正常系 */
        $ShippingRegisterPage
            // ->お届け先編集()
            ->入力_姓('aaa', 1)
            ->入力_名('bbb', 1)
            ->入力_セイ('アアア', 1)
            ->入力_メイ('アアア', 1)
            ->入力_郵便番号('060-0000', 1)
            ->入力_都道府県(['1' => '北海道'], 1)
            ->入力_市区町村名('bbb', 1)
            ->入力_番地_ビル名('bbb', 1)
            ->入力_電話番号('111-111-111', 1)
            ->入力_番地_ビル名('address 2', 1)
            ->出荷情報登録();

        $I->see('保存しました', ShippingEditPage::$登録完了メッセージ);

        $I->wait(10);
        // 出荷済みに変更
        $ShippingRegisterPage
            ->出荷完了にする()
            ->変更を確定()
            ->出荷日を確認();

        // 出荷済みに変更
        $ShippingRegisterPage
            ->出荷完了にする(1)
            ->変更を確定(1)
            ->出荷日を確認(1);
    }

    public function shipping_出荷CSV登録(AcceptanceTester $I)
    {
        $I->wantTo('EA0903-UC04-T01 出荷CSV登録');

        $entityManager = Fixtures::get('entityManager');
        /* @var Customer $Customer */
        $Customer = (Fixtures::get('createCustomer'))();
        /* @var Order[] $Orders */
        $Orders = (Fixtures::get('createOrders'))($Customer, 3);
        // 入金済みに更新しておく
        $Status = $entityManager->getRepository('Eccube\Entity\Master\OrderStatus')->find(OrderStatus::PAID);
        foreach ($Orders as $newOrder) {
            $newOrder->setOrderStatus($Status);
        }
        $entityManager->flush();

        /*
         * 出荷再検索 出荷日/伝票番号が登録されていないことを確認
         */

        $OrderManagePage = OrderManagePage::go($I)
            ->詳細検索設定()
            ->入力_ご注文者お名前($Customer->getName01().$Customer->getName02())
            ->入力_ご注文者お名前フリガナ($Customer->getKana01().$Customer->getKana02())
            ->検索();

        $I->see('検索結果：3件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $I->assertEmpty($OrderManagePage->取得_出荷伝票番号(1));
        $I->assertEmpty($OrderManagePage->取得_出荷伝票番号(2));
        $I->assertEmpty($OrderManagePage->取得_出荷伝票番号(3));
        $I->assertEquals('未出荷', $OrderManagePage->取得_出荷日(1));
        $I->assertEquals('未出荷', $OrderManagePage->取得_出荷日(2));
        $I->assertEquals('未出荷', $OrderManagePage->取得_出荷日(3));

        /*
         * 出荷CSV登録
         */

        $csv = implode(PHP_EOL, [
            '出荷ID,お問い合わせ番号,出荷日',
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

            $I->see('CSVファイルをアップロードしました', ShippingCsvUploadPage::$完了メッセージ);

            /*
             * 出荷再検索 出荷日/伝票番号が登録されたことを確認
             */

            $OrderManagePage = OrderManagePage::go($I)
            ->詳細検索設定()
            ->入力_ご注文者お名前($Customer->getName01().$Customer->getName02())
            ->入力_ご注文者お名前フリガナ($Customer->getKana01().$Customer->getKana02())
            ->検索();

            $I->see('検索結果：3件が該当しました', OrderManagePage::$検索結果_メッセージ);

            $I->assertEquals('00003', $OrderManagePage->取得_出荷伝票番号(1));
            $I->assertEquals('00002', $OrderManagePage->取得_出荷伝票番号(2));
            $I->assertEquals('00001', $OrderManagePage->取得_出荷伝票番号(3));
            $I->assertEquals('2018/03/03', $OrderManagePage->取得_出荷日(1));
            $I->assertEquals('2018/02/02', $OrderManagePage->取得_出荷日(2));
            $I->assertEquals('2018/01/01', $OrderManagePage->取得_出荷日(3));
            $I->assertEquals('発送済み', $OrderManagePage->取得_ステータス(1));
            $I->assertEquals('発送済み', $OrderManagePage->取得_ステータス(2));
            $I->assertEquals('発送済み', $OrderManagePage->取得_ステータス(3));
        } finally {
            if (file_exists($csvFileName)) {
                unlink($csvFileName);
            }
        }
    }

    public function shipping_出荷CSV登録失敗(AcceptanceTester $I)
    {
        $I->wantTo('EA0903-UC04-T02 出荷CSV登録失敗');

        $entityManager = Fixtures::get('entityManager');
        /* @var Customer $Customer */
        $Customer = (Fixtures::get('createCustomer'))();
        /* @var Order[] $Orders */
        $Orders = (Fixtures::get('createOrders'))($Customer, 3);
        // キャンセルに更新しておく
        $Status = $entityManager->getRepository('Eccube\Entity\Master\OrderStatus')->find(OrderStatus::CANCEL);
        foreach ($Orders as $newOrder) {
            $newOrder->setOrderStatus($Status);
        }
        $entityManager->flush();

        /*
         * 出荷再検索 出荷日/伝票番号が登録されていないことを確認
         */

        $OrderManagePage = OrderManagePage::go($I)
            ->詳細検索設定()
            ->入力_ご注文者お名前($Customer->getName01().$Customer->getName02())
            ->入力_ご注文者お名前フリガナ($Customer->getKana01().$Customer->getKana02())
            ->検索();

        $I->see('検索結果：3件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $I->assertEmpty($OrderManagePage->取得_出荷伝票番号(1));
        $I->assertEmpty($OrderManagePage->取得_出荷伝票番号(2));
        $I->assertEmpty($OrderManagePage->取得_出荷伝票番号(3));
        $I->assertEquals('未出荷', $OrderManagePage->取得_出荷日(1));
        $I->assertEquals('未出荷', $OrderManagePage->取得_出荷日(2));
        $I->assertEquals('未出荷', $OrderManagePage->取得_出荷日(3));

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

            $I->see(sprintf('%s: %s から %s にはステータス変更できません', $Orders[0]->getShippings()[0]->getId(), '注文取消し', '発送済み'),
                    '#upload-form > div:nth-child(4)');
            $I->see(sprintf('%s: %s から %s にはステータス変更できません', $Orders[1]->getShippings()[0]->getId(), '注文取消し', '発送済み'),
                    '#upload-form > div:nth-child(5)');
            $I->see(sprintf('%s: %s から %s にはステータス変更できません', $Orders[2]->getShippings()[0]->getId(), '注文取消し', '発送済み'),
                    '#upload-form > div:nth-child(6)');
        } finally {
            if (file_exists($csvFileName)) {
                unlink($csvFileName);
            }
        }
    }

    public function shipping_出荷CSV雛形ファイルダウンロード(AcceptanceTester $I)
    {
        $I->wantTo('EA0903-UC04-T03 出荷CSV雛形ファイルのダウンロード');

        ShippingCsvUploadPage::go($I)->雛形ダウンロード();
        $csv = $I->getLastDownloadFile('/^shipping\.csv$/');
        $I->assertEquals(mb_convert_encoding(file_get_contents($csv), 'UTF-8', 'Shift_JIS'), '出荷ID,お問い合わせ番号,出荷日'.PHP_EOL);
    }
}

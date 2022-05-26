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
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Page\Admin\CsvSettingsPage;
use Page\Admin\OrderEditPage;
use Page\Admin\OrderManagePage;

/**
 * @group admin
 * @group admin01
 * @group order
 * @group ea4
 */
class EA04OrderCest
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

    public function order_受注検索(AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC01-T01(& UC01-T02, UC01-T03) 受注検索');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return !in_array($Order->getOrderStatus()->getId(), [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        });
        OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $TargetOrder = array_values($TargetOrders)[0];
        OrderManagePage::go($I)->検索($TargetOrder->getName01());
        $I->dontSee('検索結果：0件が該当しました', OrderManagePage::$検索結果_メッセージ);

        OrderManagePage::go($I)->検索('gege@gege.com');
        $I->see('検索結果：0件が該当しました', OrderManagePage::$検索結果_メッセージ);

        OrderManagePage::go($I)->詳細検索_電話番号('あああ');
        $I->see('検索条件に誤りがあります', OrderManagePage::$検索結果_エラーメッセージ);
    }

    /**
     * @group excludeCoverage
     * @group vaddy
     */
    public function order_受注CSVダウンロード(AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC02-T01 受注CSVダウンロード');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return !in_array($Order->getOrderStatus()->getId(), [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $OrderListPage->受注CSVダウンロード実行();
        // make sure wait to download file completely
        $I->wait(10);
        $OrderCSV = $I->getLastDownloadFile('/^order_\d{14}\.csv$/');
        $I->assertGreaterOrEquals(count($TargetOrders), count(file($OrderCSV)), '検索結果以上の行数があるはず');
    }

    public function order_受注情報のCSV出力項目変更設定(AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC02-T02 受注情報のCSV出力項目変更設定');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return !in_array($Order->getOrderStatus()->getId(), [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        /* 項目設定 */
        $OrderListPage->受注CSV出力項目設定();

        CsvSettingsPage::at($I);
        $value = $I->grabValueFrom(CsvSettingsPage::$CSVタイプ);
        $I->assertEquals(3, $value);
    }

    /**
     * @group excludeCoverage
     */
    public function order_配送CSVダウンロード(AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC03-T01 配送CSVダウンロード');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return !in_array($Order->getOrderStatus()->getId(), [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $OrderListPage->配送CSVダウンロード実行();
        // make sure wait to download file completely
        $I->wait(10);
        $OrderCSV = $I->getLastDownloadFile('/^shipping_\d{14}\.csv$/');
        $I->assertGreaterOrEquals(count($TargetOrders), count(file($OrderCSV)), '検索結果以上の行数があるはず');
    }

    public function order_配送情報のCSV出力項目変更設定(AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC03-T02 配送情報のCSV出力項目変更設定');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return !in_array($Order->getOrderStatus()->getId(), [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        /* 項目設定 */
        $OrderListPage->配送CSV出力項目設定();

        CsvSettingsPage::at($I);
        $value = $I->grabValueFrom(CsvSettingsPage::$CSVタイプ);
        $I->assertEquals(4, $value);
    }

    /**
     * @group vaddy
     */
    public function order_受注編集(AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC05-T01(& UC05-T02/UC05-T03/UC06-T01) 受注編集');

        // 新規受付ステータスの受注を作る
        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        /** @var Order[] $newOrders */
        $newOrders = $createOrders($createCustomer(), 1, [], OrderStatus::NEW);

        $OrderListPage = OrderManagePage::go($I)->検索($newOrders[0]->getOrderNo());

        $I->see('検索結果：1件が該当しました', OrderManagePage::$検索結果_メッセージ);

        /* 編集 */
        $OrderListPage->一覧_編集(1);

        $OrderRegisterPage = OrderEditPage::at($I)
            ->注文者パネルを開く()
            ->入力_姓('')
            ->受注情報登録();

        /* 異常系 */
        $I->see('入力されていません。', OrderEditPage::$姓_エラーメッセージ);

        /* 正常系 */
        $OrderRegisterPage
            ->入力_姓('aaa')
            ->入力_セイ('アアア')
            ->入力_メイ('アアア')
            ->入力_郵便番号('060-0000')
            ->入力_都道府県(['1' => '北海道'])
            ->入力_市区町村名('bbb')
            ->入力_番地_ビル名('bbb')
            ->入力_電話番号('111-111-111')
            ->入力_番地_ビル名('address 2')
            ->入力_支払方法(['4' => '郵便振替'])
            ->受注情報登録();

        $I->see('保存しました', OrderEditPage::$登録完了メッセージ);

        /* ステータス変更 */
        $OrderRegisterPage
            // 新規受付から遷移可能なステータスをセットする.
            ->入力_受注ステータス(['3' => '入金済み'])
            ->受注情報登録();

        $I->see('保存しました', OrderEditPage::$登録完了メッセージ);

        /* 明細の削除 */
        $itemName = $OrderRegisterPage->明細の項目名を取得(1);
        $OrderRegisterPage->明細を削除(1)
            ->acceptDeleteModal(1);
        $I->wait(2);

        // before submit
        $I->dontSee($itemName, '#table-form-field');

        // after submit
        $OrderRegisterPage->受注情報登録();
        $I->dontSee($itemName, '#table-form-field');

        $I->see('保存しました', OrderEditPage::$登録完了メッセージ);
    }

    /**
     * @group vaddy
     */
    public function order_受注削除(AcceptanceTester $I)
    {
        $I->getScenario()->incomplete('未実装：受注削除は未実装');
        $I->wantTo('EA0401-UC08-T01 受注削除');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return !in_array($Order->getOrderStatus()->getId(), [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        });

        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        // 削除
        $OrderNumForDel = $OrderListPage->一覧_注文番号(1);
        $OrderListPage
            ->一覧_選択(1)
            ->一覧_削除()
            ->Accept_削除();

        $I->see('削除しました', ['css' => '#page_admin_order > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span']);
        $I->assertNotEquals($OrderNumForDel, $OrderListPage->一覧_注文番号(1));

        // 削除キャンセル
        $OrderNumForDontDel = $OrderListPage->一覧_注文番号(1);
        $OrderListPage
            ->一覧_選択(1)
            ->一覧_削除()
            ->Cancel_削除();

        $I->assertEquals($OrderNumForDontDel, $OrderListPage->一覧_注文番号(1));
    }

    public function order_一覧でのソート(AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC09-T01 一覧でのソート');
        $page = OrderManagePage::go($I);

        // 対応状況横の上矢印をクリック
        $I->click('a[data-sortkey="order_status"]');
        $I->seeElement('.listSort-current[data-sortkey="order_status"] .fa-arrow-up');
        $page->assertSortedStatusList('asc');

        // 対応状況横の下矢印をクリック
        $I->click('a[data-sortkey="order_status"]');
        $I->seeElement('.listSort-current[data-sortkey="order_status"] .fa-arrow-down');
        $page->assertSortedStatusList('desc');

        // 購入金額横の上矢印をクリック
        $I->click('[data-sortkey="purchase_price"]');
        $I->seeElement('.listSort-current[data-sortkey="purchase_price"] .fa-arrow-up');
        $page->assertSortedPriceList('asc');

        // 購入金額横の下矢印をクリック
        $I->click('a[data-sortkey="purchase_price"]');
        $I->seeElement('.listSort-current[data-sortkey="purchase_price"] .fa-arrow-down');
        $page->assertSortedPriceList('desc');
    }

    /**
     * @group vaddy
     */
    public function order_受注メール通知(AcceptanceTester $I)
    {
        $I->wantTo('EA0402-UC01-T01 受注メール通知');

        $I->resetEmails();

        OrderManagePage::go($I)
            ->一覧_メール通知(1);

        $message = $I->lastMessage();
        $I->assertCount(2, $message->getRecipients(), 'Bcc で管理者にも送信するので宛先アドレスは2つ');
        $I->seeEmailCount(1);

        $I->seeInLastEmailSubjectTo('admin@example.com', '[EC-CUBE SHOP] 商品出荷のお知らせ');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function order_一括メール通知(AcceptanceTester $I)
    {
        $I->wantTo('EA0402-UC02-T01 一括メール通知');

        $I->resetEmails();

        OrderManagePage::go($I)
            ->件数変更(10)
            ->一覧_全選択()
            ->一括メール送信();

        $message = $I->lastMessage();
        $I->assertCount(2, $message->getRecipients(), 'Bcc で管理者にも送信するので宛先アドレスは2つ');
        $I->seeEmailCount(10);
    }

    public function order_一括メール通知_キャンセル(AcceptanceTester $I)
    {
        $I->wantTo('EA0402-UC02-T02 一括メール通知 (キャンセル)');

        $I->resetEmails();

        OrderManagePage::go($I)
            ->件数変更(10)
            ->一覧_全選択()
            ->一括メール送信_キャンセル();

        $I->seeEmailCount(0);
    }

    /**
     * @group vaddy
     */
    public function order_受注登録(AcceptanceTester $I)
    {
        $I->wantTo('EA0405-UC01-T01(& UC01-T02) 受注登録');

        $OrderRegisterPage = OrderEditPage::go($I)->受注情報登録();

        /* 異常系 */
        $I->dontSee('保存しました', OrderEditPage::$登録完了メッセージ);

        /* 正常系 */
        $OrderRegisterPage
            ->入力_支払方法(['4' => '郵便振替'])
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
            ->注文者情報をコピー()
            ->入力_配送業者([1 => 'サンプル業者'])
            ->商品検索('チェリーアイスサンド')
            ->商品検索結果_選択(1)
            ->受注情報登録();

        $I->see('保存しました', OrderEditPage::$登録完了メッセージ);
    }

    public function order_納品書の出力(AcceptanceTester $I)
    {
        $I->wantTo('EA0405-UC06-T02 納品書の出力');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return !in_array($Order->getOrderStatus()->getId(), [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $OrderListPage->すべてチェック();
        $OrderListPage->要素をクリック('#form_bulk #bulkExportPdf');

        // 別ウィンドウ
        $I->switchToWindow('newwin');

        // Check redirect to form pdf information
        $I->see('納品書出力受注管理', OrderManagePage::$タイトル要素);

        $I->click('.btn-ec-conversion');
        $I->wait(2);
        $filename = $I->getLastDownloadFile('/^nouhinsyo.pdf$/');
        $I->assertTrue(file_exists($filename));

        $I->closeTab();
    }

    /**
     * @group vaddy
     */
    public function order_納品書の一括出力(AcceptanceTester $I)
    {
        $I->wantTo('EA0405-UC06-T03 納品書の一括出力');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return !in_array($Order->getOrderStatus()->getId(), [OrderStatus::PROCESSING, OrderStatus::PENDING]);
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $OrderListPage->すべてチェック();
        $OrderListPage->要素をクリック('#form_bulk #bulkExportPdf');

        // 別ウィンドウ
        $I->switchToWindow('newwin');

        $I->see('納品書出力受注管理', OrderManagePage::$タイトル要素);

        $OrderListPage->PDFフォームを入力(['id' => 'order_pdf_note1'], 'Test note first');
        $OrderListPage->PDFフォームを入力(['id' => 'order_pdf_note2'], 'Test note second');
        $OrderListPage->PDFフォームを入力(['id' => 'order_pdf_note3'], 'Test note third');
        $OrderListPage->要素をクリック('#order_pdf_default');
        $OrderListPage->要素をクリック('#order_pdf_form .c-conversionArea .justify-content-end button.btn-ec-conversion');
        // make sure wait to download file completely
        $I->wait(5);
        $filename = $I->getLastDownloadFile('/^nouhinsyo\.pdf$/');
        $I->assertTrue(file_exists($filename));

        $I->closeTab();
    }

    public function order_ー括受注のステータス変更(AcceptanceTester $I)
    {
        $I->wantTo('EA0405-UC06-T01_ー括受注のステータス変更');

        // 新規受付ステータスをキャンセルに変更する
        $entityManager = Fixtures::get('entityManager');
        $findOrders = Fixtures::get('findOrders');
        $ExistsNewOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::NEW;
        });
        $CancelStatus = $entityManager->getRepository('Eccube\Entity\Master\OrderStatus')->find(OrderStatus::CANCEL);
        foreach ($ExistsNewOrders as $ExistsNewOrder) {
            $ExistsNewOrder->setOrderStatus($CancelStatus);
        }
        $entityManager->flush();

        // 新規受付ステータスの受注を作る
        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        $NewOrders = $createOrders($createCustomer(), 2, []);
        $Status = $entityManager->getRepository('Eccube\Entity\Master\OrderStatus')->find(OrderStatus::NEW);
        foreach ($NewOrders as $NewOrder) {
            $NewOrder->setOrderStatus($Status);
        }
        $entityManager->flush();

        // 検索し直す
        $NewOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::NEW;
        });
        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::NEW);
        $I->comment('新規受付の受注を検索します。想定値: '.count($NewOrders).'件');
        $I->see('検索結果：'.count($NewOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $DeliveredOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::DELIVERED;
        });
        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::DELIVERED);
        $I->comment('発送済みの受注を検索します。想定値: '.count($DeliveredOrders).'件');
        $I->see('検索結果：'.count($DeliveredOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $I->comment('受注ステータスを新規受付から発送済みに変更します');
        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::NEW)
            ->一覧_全選択()
            ->受注ステータス変更('発送済み');

        $I->waitForElementVisible(['id' => 'bulkChangeComplete']);

        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::DELIVERED);
        $I->comment('発送済みに変更した件数を確認します。新規受付: '.count($NewOrders).'件 +  発送済み: '.count($DeliveredOrders).'件');
        $I->see('検索結果：'.(count($DeliveredOrders) + count($NewOrders)).'件が該当しました', OrderManagePage::$検索結果_メッセージ);
    }

    /**
     * @group vaddy
     */
    public function order_個別出荷済みステータス変更(AcceptanceTester $I)
    {
        $I->wantTo('EA0405-UC07-T01_個別出荷済みステータス変更');

        $I->resetEmails();

        // 新規受付ステータスをキャンセルに変更する
        $entityManager = Fixtures::get('entityManager');
        $findOrders = Fixtures::get('findOrders');
        $ExistsNewOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::NEW;
        });
        $CancelStatus = $entityManager->getRepository('Eccube\Entity\Master\OrderStatus')->find(OrderStatus::CANCEL);
        foreach ($ExistsNewOrders as $ExistsNewOrder) {
            $ExistsNewOrder->setOrderStatus($CancelStatus);
        }
        $entityManager->flush();

        // 新規受付ステータスの受注を作る
        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        $NewOrders = $createOrders($createCustomer(), 2, []);
        $Status = $entityManager->getRepository('Eccube\Entity\Master\OrderStatus')->find(OrderStatus::NEW);
        foreach ($NewOrders as $NewOrder) {
            $NewOrder->setOrderStatus($Status);
        }
        $entityManager->flush();

        $DeliveredOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::DELIVERED;
        });
        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::DELIVERED);
        $I->comment('発送済みの受注を検索します。想定値: '.count($DeliveredOrders).'件');
        $I->see('検索結果：'.count($DeliveredOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $NewOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::NEW;
        });
        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::NEW);
        $I->comment('新規受付の受注を検索します。想定値: '.count($NewOrders).'件');
        $I->see('検索結果：'.count($NewOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $I->comment('出荷済みに変更します');
        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::NEW)
            ->出荷済にする(1);

        $I->wait(5);
        $message = $I->lastMessage();
        $I->assertCount(2, $message->getRecipients(), 'Bcc で管理者にも送信するので宛先アドレスは2つ');
        $I->seeEmailCount(1);
        $I->seeInLastEmailSubjectTo('admin@example.com', '[EC-CUBE SHOP] 商品出荷のお知らせ');

        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::NEW);
        $I->see('検索結果：1件が該当しました', OrderManagePage::$検索結果_メッセージ);
    }
}

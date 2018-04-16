<?php

use Codeception\Util\Fixtures;
use Page\Admin\CsvSettingsPage;
use Page\Admin\OrderManagePage;
use Page\Admin\OrderEditPage;
use Eccube\Entity\Master\OrderStatus;

/**
 * @group admin
 * @group admin01
 * @group order
 * @group ea4
 */
class EA04OrderCest
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

    public function order_受注検索(\AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC01-T01(& UC01-T02) 受注検索');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() != OrderStatus::PROCESSING;
        });
        OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        OrderManagePage::go($I)->検索('gege@gege.com');
        $I->see('検索結果：0件が該当しました', OrderManagePage::$検索結果_メッセージ);
    }

    /**
     * @env firefox
     * @env chrome
     */
    public function order_受注CSVダウンロード(\AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC02-T01 受注CSVダウンロード');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() != OrderStatus::PROCESSING;
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $OrderListPage->受注CSVダウンロード実行();
        // make sure wait to download file completely
        $I->wait(10);
        $OrderCSV = $I->getLastDownloadFile('/^order_\d{14}\.csv$/');
        $I->assertGreaterOrEquals(count($TargetOrders), count(file($OrderCSV)), '検索結果以上の行数があるはず');
    }

    public function order_受注情報のCSV出力項目変更設定(\AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC02-T02 受注情報のCSV出力項目変更設定');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() != OrderStatus::PROCESSING;
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
     * TODO: will fix when apply style guide for admin order edit
     *
     * @skip
     */
    public function order_受注編集(\AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC05-T01(& UC05-T02/UC06-T01) 受注編集');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() != OrderStatus::PROCESSING;
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        /* 編集 */
        $OrderListPage->一覧_編集(1);

        $OrderRegisterPage = OrderEditPage::at($I)
            ->入力_姓('')
            ->受注情報登録();

        /* 異常系 */
        $I->see('入力されていません。', OrderEditPage::$姓_エラーメッセージ);

        /* 正常系 */
        $OrderRegisterPage
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
            ->入力_支払方法(['4' => '郵便振替'])
            ->受注情報登録();

        $I->see('受注情報を保存しました。', OrderEditPage::$登録完了メッセージ);

        /* ステータス変更 */
        $OrderRegisterPage
            ->入力_受注ステータス(['2' => '入金待ち'])
            ->受注情報登録();

        $I->see('受注情報を保存しました。', OrderEditPage::$登録完了メッセージ);
    }

    public function order_受注削除(\AcceptanceTester $I)
    {
        $I->wantTo('EA0401-UC08-T01(& UC08-T02) 受注削除');

        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() != OrderStatus::PROCESSING;
        });

        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        // 削除
        $OrderNumForDel = $OrderListPage->一覧_注文番号(1);
        $OrderListPage
          ->一覧_選択(1)
          ->一覧_削除()
          ->Accept_削除();

        $I->see('受注情報を削除しました', ['css' => '#page_admin_order > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span']);
        $I->assertNotEquals($OrderNumForDel, $OrderListPage->一覧_注文番号(1));

        // 削除キャンセル
        $OrderNumForDontDel = $OrderListPage->一覧_注文番号(1);
        $OrderListPage
          ->一覧_選択(1)
          ->一覧_削除()
          ->Cancel_削除();

        $I->assertEquals($OrderNumForDontDel, $OrderListPage->一覧_注文番号(1));
    }

    public function order_受注メール通知(\AcceptanceTester $I)
    {
        $I->wantTo('EA0402-UC01-T01 受注メール通知');

        $I->resetEmails();
        $findOrders = Fixtures::get('findOrders');
        $NewOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::NEW;
        });
        $Order = array_pop($NewOrders);
        $OrderListPage = OrderManagePage::go($I)->検索($Order->getId());
        $I->see('検索結果：1件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $OrderListPage->一覧_メール通知(1);

        $I->selectOption(['id' => 'template-change'], ['1' => '注文受付メール']);
        $I->click(['id' => 'mailConfirm']);
        $I->scrollTo(['id' => 'sendMail'], 0, 100);
        $I->wait(1);
        $I->click(['id' => 'sendMail']);

        $I->wait(3);
        $I->seeEmailCount(2);

        $I->seeInLastEmailSubjectTo('admin@example.com', 'ご注文ありがとうございます');
    }

    public function order_一括メール通知(\AcceptanceTester $I)
    {
        $I->wantTo('EA0402-UC02-T01(& UC02-T02) 一括メール通知');

        $I->resetEmails();

        $config = Fixtures::get('config');
        $findOrders = Fixtures::get('findOrders'); // Closure
        $TargetOrders = array_filter($findOrders(), function ($Order) use ($config) {
            return $Order->getOrderStatus()->getId() != OrderStatus::PROCESSING;
        });
        $OrderListPage = OrderManagePage::go($I)->検索();
        $I->see('検索結果：'.count($TargetOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $OrderListPage
            ->一覧_全選択()
            ->メール一括通知();

        $I->selectOption(['id' => 'template-change'], ['1' => '注文受付メール']);
        $I->click(['id' => 'mailConfirm']);
        $I->scrollTo(['id' => 'sendMail'], 0, 100);
        $I->wait(1);
        $I->click(['id' => 'sendMail']);

        $I->wait(5);
        $I->seeEmailCount(20);
    }

    public function order_受注登録(\AcceptanceTester $I)
    {
        $I->wantTo('EA0405-UC01-T01(& UC01-T02) 受注登録');

        $OrderRegisterPage = OrderEditPage::go($I)->受注情報登録();

        /* 異常系 */
        $I->dontSee('受注情報を保存しました。', OrderEditPage::$登録完了メッセージ);

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

        $I->see('受注情報を保存しました。', OrderEditPage::$登録完了メッセージ);
    }

    public function order_ー括受注のステータス変更(\AcceptanceTester $I)
    {
        $I->wantTo('EA0405-UC06-T01_ー括受注のステータス変更');

        // 新規受付ステータスをキャンセルに変更する
        $entityManager = Fixtures::get('entityManager');
        $findOrders = Fixtures::get('findOrders');
        $NewOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::NEW;
        });
        $CancelStatus = $entityManager->getRepository('Eccube\Entity\Master\OrderStatus')->find(OrderStatus::CANCEL);
        foreach ($NewOrders as $newOrder) {
            $newOrder->setOrderStatus($CancelStatus);
        }
        $entityManager->flush();

        // 新規受付ステータスの受注を作る
        $createCustomer = Fixtures::get('createCustomer');
        $createOrders = Fixtures::get('createOrders');
        $newOrders = $createOrders($createCustomer(), 2, array());
        $Status = $entityManager->getRepository('Eccube\Entity\Master\OrderStatus')->find(OrderStatus::NEW);
        foreach ($newOrders as $newOrder) {
            $newOrder->setOrderStatus($Status);
        }
        $entityManager->flush();

        $NewOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::NEW;
        });
        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::NEW);
        $I->see('検索結果：'.count($NewOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        $DeliveredOrders = array_filter($findOrders(), function ($Order) {
            return $Order->getOrderStatus()->getId() == OrderStatus::DELIVERED;
        });
        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::DELIVERED);
        $I->see('検索結果：'.count($DeliveredOrders).'件が該当しました', OrderManagePage::$検索結果_メッセージ);

        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::NEW)
            ->一覧_全選択()
            ->受注ステータス変更('発送済み');

        OrderManagePage::go($I)->受注ステータス検索(OrderStatus::DELIVERED);
        $I->see('検索結果：'.(count($DeliveredOrders) + count($NewOrders)).'件が該当しました', OrderManagePage::$検索結果_メッセージ);
    }
}

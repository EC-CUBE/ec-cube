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
use Page\Admin\OrderManagePage;
use Page\Admin\TopPage;

/**
 * @group admin
 * @group admin01
 * @group toppage
 * @group ea1
 */
class EA01TopCest
{
    const ページタイトル = '.c-pageTitle h2.c-pageTitle__title';

    public function _before(\AcceptanceTester $I)
    {
        // すべてのテストケース実施前にログインしておく
        // ログイン後は管理アプリのトップページに遷移している
        $I->loginAsAdmin();
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function top_001(\AcceptanceTester $I)
    {
        $I->wantTo('EA0101-UC01-T01 TOPページ 初期表示');

        // TOP画面に現在の受注状況、お知らせ、売り上げ状況、ショップ状況が表示されている
        $I->see('新規受付', TopPage::$受付状況_新規受付);
        $I->see('お知らせ', TopPage::$お知らせ);
        $I->see('売上状況', TopPage::$売上状況);
        $I->see('ショップ状況', TopPage::$ショップ状況);

        // 新規受付をクリックすると受注管理画面に遷移することを確認
        $I->click(TopPage::$受付状況_新規受付);
        $I->see('受注一覧', self::ページタイトル);
        $I->goToAdminPage();

        // 購入された商品が受注管理画面のページにて反映されていることを確認
        $config = Fixtures::get('config');
        $findOrders = Fixtures::get('findOrders');
        $NewOrders = array_filter($findOrders(), function ($Order) use ($config) {
            return $Order->getOrderStatus()->getId() == \Eccube\Entity\Master\OrderStatus::NEW;
        });
        $I->see(count($NewOrders), TopPage::$受付状況_新規受付数);

        // FIXME [issue] ソート順が指定されていないのでテストが失敗する
        // https://github.com/EC-CUBE/ec-cube/issues/1908
        // // 入金待ちをクリックすると「受注管理＞入金待ち」のページに遷移することを確認
        // $I->click(TopPage::$受付状況_入金待ち);
        // $I->see('受注一覧', self::ページタイトル);
        // $I->seeInField(OrderManagePage::$検索条件_受注ステータス, '2'/*入金待ち*/);
        // $I->goToAdminPage();
        //
        // // 入金済みをクリックすると「受注管理＞入金済み」のページに遷移することを確認
        // $I->click(TopPage::$受付状況_入金済み);
        // $I->see('受注一覧', self::ページタイトル);
        // $I->seeInField(OrderManagePage::$検索条件_受注ステータス, '6'/*入金済み*/);
        // $I->goToAdminPage();
        //
        // // 取り寄せ中をクリックすると「受注管理＞取り寄せ」のページに遷移することを確認
        // $I->click(TopPage::$受付状況_取り寄せ中);
        // $I->see('受注一覧', self::ページタイトル);
        // $I->seeInField(OrderManagePage::$検索条件_受注ステータス, '4'/*取り寄せ中*/);
        // $I->goToAdminPage();

        // お知らせの記事をクリックすると設定されたURLに遷移することを確認
        $I->switchToIFrame('information');
        $selector = '.news_area .link_list .tableish a:nth-child(1)';
        $url = $I->executeJS('return location.href');
        $I->click(['css' => $selector]);
        $I->switchToNewWindow();
        $I->assertNotEquals($url, $I->executeJS('return location.href'), $url.' から遷移していません。');
        $I->switchToWindow();

        // ショップ情報の在庫切れ商品をクリックすると商品管理ページに遷移することを確認
        $I->click(TopPage::$ショップ状況_在庫切れ商品);
        $I->see('商品一覧', self::ページタイトル);
        $I->goToAdminPage();

        // ショップ情報の会員数をクリックすると会員管理に遷移することを確認
        $I->click(TopPage::$ショップ状況_会員数);
        $I->see('会員一覧', self::ページタイトル);
    }
}

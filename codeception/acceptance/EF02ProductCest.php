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
use Page\Front\ProductDetailPage;
use Page\Front\ProductListPage;
use Page\Front\TopPage;

/**
 * @group front
 * @group product
 * @group ef2
 */
class EF02ProductCest
{
    public function product_商品一覧初期表示(AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC01-T01 & UC01-T02 商品一覧ページ 初期表示');
        $topPage = TopPage::go($I);

        // TOPページ>商品一覧（ヘッダーのいずれかのカテゴリを選択）へ遷移
        $topPage->カテゴリ選択(['アイスサンド', 'フルーツ']);

        // 登録商品がカテゴリごとに一覧表示される
        $I->see('フルーツ', '.ec-topicpath');

        // 一覧ページで商品がサムネイル表示される
        $I->see('チェリーアイスサンド', '.ec-shelfGrid');
    }

    /**
     * @group vaddy
     */
    public function product_商品一覧ソート(AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC03-T01 商品一覧ページ ソート');
        $topPage = TopPage::go($I);

        // TOPページ>商品一覧（ヘッダーのいずれかのカテゴリを選択）へ遷移
        $topPage->カテゴリ選択(['新入荷']);

        // 各商品のサムネイルが表示される デフォルトは価格順
        $products = $I->grabMultiple(['xpath' => "//*[@class='ec-shelfGrid__item']/a/p[2]"]);
        $pPos = 0;
        $fPos = 0;
        foreach ($products as $key => $product) {
            if ($product == 'チェリーアイスサンド') {
                $pPos = $key;
            }
            if ($product == '彩のジェラートCUBE') {
                $fPos = $key;
            }
        }
        $I->assertTrue(($pPos < $fPos));

        // ソート条件の選択リストを変更する
        ProductListPage::at($I)
            ->表示件数設定(40)
            ->表示順設定('価格が高い順');

        // 変更されたソート条件に従い、商品がソートされる
        $products = $I->grabMultiple(['xpath' => "//*[@class='ec-shelfGrid__item']/a/p[2]"]);
        $pPos = 0;
        $fPos = 0;
        foreach ($products as $key => $product) {
            if ($product == 'チェリーアイスサンド') {
                $pPos = $key;
            }
            if ($product == '彩のジェラートCUBE') {
                $fPos = $key;
            }
        }
        $I->assertTrue(($pPos > $fPos));
    }

    public function product_商品一覧表示件数(AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC04-T01 商品一覧ページ 表示件数');
        $topPage = TopPage::go($I);

        // TOPページ>商品一覧（ヘッダーのいずれかのカテゴリを選択）へ遷移
        $topPage->カテゴリ選択(['新入荷']);
        $listPage = new ProductListPage($I);

        // 各商品のサムネイルが表示される
        $config = Fixtures::get('test_config');
        $productNum = $config['fixture_product_num'] + 2;
        $itemNum = ($productNum >= 20) ? 20 : $productNum;
        $I->assertEquals($itemNum, $listPage->一覧件数取得());

        // 表示件数の選択リストを変更する
        $listPage->表示件数設定(40);

        // 変更された表示件数分が1画面に表示される
        $expected = ($productNum >= 40) ? 40 : $productNum;
        $I->assertEquals($expected, $listPage->一覧件数取得());
    }

    public function product_商品一覧ページング(AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC04-T02 商品一覧ページ ページング');
        $topPage = TopPage::go($I);

        // TOPページ>商品一覧（ヘッダーのいずれかのカテゴリを選択）へ遷移
        $topPage->カテゴリ選択(['新入荷']);

        // 絞込検索条件では、検索数が多い場合、「次へ」「前へ」「ページ番号」が表示される
        $I->see('1', ['css' => 'li.ec-pager__item--active']);
        $I->see('2', ['xpath' => "//li[@class='ec-pager__item'][position()=1]"]);
        $I->see('次へ', ['xpath' => "//li[@class='ec-pager__item'][position()=2]"]);

        // 選択されたリンクに応じてページングされる

        // '2'をクリック
        $I->click(['xpath' => "//li[@class='ec-pager__item'][position()=1]/a"]);
        $I->see('2', ['css' => 'li.ec-pager__item--active']);

        // '前へ'をクリック
        $I->click(['xpath' => "//li[@class='ec-pager__item'][position()=1]/a"]);
        $I->see('1', ['css' => 'li.ec-pager__item--active']);

        // '次へ'をクリック
        $I->click(['xpath' => "//li[@class='ec-pager__item'][position()=2]/a"]);
        $I->see('2', ['css' => 'li.ec-pager__item--active']);
    }

    public function product_商品詳細初期表示(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC01-T01 商品詳細 初期表示');
        $I->setStock(2, 0);
        ProductDetailPage::go($I, 2);

        // 「カートに入れる」ボタンが、非活性となり「ただいま品切れ中です」と表示される。
        $I->see('ただいま品切れ中です', '#form1 button');
    }

    public function product_商品詳細カテゴリリンク(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC01-T02 商品詳細 カテゴリリンク');
        $productPage = ProductDetailPage::go($I, 2);

        // 商品詳細の関連カテゴリに表示されている、カテゴリリンクを押下する
        $productPage->カテゴリ選択(['アイスサンド', 'フルーツ']);

        // 登録商品がカテゴリごとに一覧表示される
        $I->see('フルーツ', '.ec-topicpath');

        // 一覧ページで商品がサムネイル表示される
        $I->see('チェリーアイスサンド', '.ec-shelfGrid');
    }

    public function product_商品詳細サムネイル(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC01-T03 商品詳細 サムネイル');
        $productPage = ProductDetailPage::go($I, 2);

        // デフォルトサムネイル表示確認
        $img = $productPage->サムネイル画像URL(1);
        $I->assertRegExp('/\/upload\/save_image\/sand-1\.png$/', $img, $img.' が見つかりません');

        // 2個目のサムネイルクリック
        $productPage->サムネイル切替(2);
        $img = $productPage->サムネイル画像URL(2);
        $I->assertRegExp('/\/upload\/save_image\/sand-2\.png$/', $img, $img.' が見つかりません');
    }

    /**
     * @group excludeCoverage
     */
    public function product_商品詳細カート1(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T01 商品詳細 カート 注文数＜販売制限数＜在庫数の注文');
        $I->setStock(2, 10);
        $productPage = ProductDetailPage::go($I, 2);

        // 「カートに入れる」ボタンを押下する
        $productPage->カートに入れる(4);
        $I->wait(5);

        $I->assertContains('カートに追加しました。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('チェリーアイスサンド', $cartPage->商品名(1));
        $I->assertContains('4', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    /**
     * @group excludeCoverage
     */
    public function product_商品詳細カート2(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T02 商品詳細 カート 販売制限数＜注文数＜在庫数の注文');
        $I->setStock(2, 10);

        $productPage = ProductDetailPage::go($I, 2);

        // 「カートに入れる」ボタンを押下する
        $productPage->カートに入れる(6);
        $I->wait(5);

        $I->assertContains('「チェリーアイスサンド」は販売制限しております。一度に販売制限数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('チェリーアイスサンド', $cartPage->商品名(1));
        $I->assertContains('5', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    /**
     * @group excludeCoverage
     */
    public function product_商品詳細カート3(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T03 商品詳細 カート 販売制限数＜在庫数＜注文数の注文');
        $I->setStock(2, 10);

        $productPage = ProductDetailPage::go($I, 2);

        // 「カートに入れる」ボタンを押下する
        $productPage->カートに入れる(12);
        $I->wait(5);

        $I->assertContains('「チェリーアイスサンド」は販売制限しております。一度に販売制限数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('チェリーアイスサンド', $cartPage->商品名(1));
        $I->assertContains('5', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    /**
     * @group excludeCoverage
     * @group vaddy
     */
    public function product_商品詳細カート4(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T04 商品詳細(規格あり) カート 注文数＜販売制限数＜在庫数の注文');
        $I->setStock(1, [10, 10, 10, 10, 10, 10, 10, 10, 10]);

        $productPage = ProductDetailPage::go($I, 1)
            ->規格選択(['チョコ', '16mm × 16mm'])
            ->カートに入れる(1);

        $I->wait(5);

        $I->assertContains('カートに追加しました。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('彩のジェラートCUBE', $cartPage->商品名(1));
        $I->assertContains('1', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    /**
     * @group excludeCoverage
     */
    public function product_商品詳細カート5(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T05 商品詳細(規格あり) カート 販売制限数＜注文数＜在庫数の注文');
        $I->setStock(1, [10, 10, 10, 10, 10, 10, 10, 10, 10]);

        $productPage = ProductDetailPage::go($I, 1)
            ->規格選択(['チョコ', '16mm × 16mm'])
            ->カートに入れる(3);

        $I->wait(5);

        $I->assertContains('「彩のジェラートCUBE - チョコ - 16mm × 16mm」は販売制限しております。一度に販売制限数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('彩のジェラートCUBE', $cartPage->商品名(1));
        $I->assertContains('2', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    /**
     * @group excludeCoverage
     */
    public function product_商品詳細カート6(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T06 商品詳細(規格あり) カート 販売制限数＜在庫数＜注文数の注文');
        $I->setStock(1, [10, 10, 10, 10, 10, 10, 10, 10, 10]);

        $productPage = ProductDetailPage::go($I, 1)
            ->規格選択(['チョコ', '16mm × 16mm'])
            ->カートに入れる(12);

        $I->wait(5);

        $I->assertContains('「彩のジェラートCUBE - チョコ - 16mm × 16mm」は販売制限しております。一度に販売制限数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('彩のジェラートCUBE', $cartPage->商品名(1));
        $I->assertContains('2', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    public function product_商品詳細カート7(AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC03-T01_商品詳細（カートに入れる・在庫数＜注文数 の注文）');
        $I->setStock(2, 3);

        $productPage = ProductDetailPage::go($I, 2);

        // 「カートに入れる」ボタンを押下する
        $productPage->カートに入れる(4);
        $I->wait(1);

        $I->assertContains('「チェリーアイスサンド」の在庫が不足しております。一度に在庫数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 在庫数分が、カート画面の対象商品に追加されている。
        $I->assertContains('チェリーアイスサンド', $cartPage->商品名(1));
        $I->assertContains('3', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }
}

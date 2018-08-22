<?php

use Codeception\Util\Fixtures;
use Page\Front\CartPage;
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
    public function product_商品一覧初期表示(\AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC01-T01 商品一覧ページ 初期表示');
        $topPage = TopPage::go($I);

        // TOPページ>商品一覧（ヘッダーのいずれかのカテゴリを選択）へ遷移
        $topPage->カテゴリ選択(['キッチンツール', '調理器具']);

        // 登録商品がカテゴリごとに一覧表示される
        $I->see('調理器具', '.ec-topicpath');

        // 一覧ページで商品がサムネイル表示される
        $I->see('パーコレーター', '.ec-shelfGrid');
    }

    public function product_商品一覧ヘッダ以外のカテゴリリンク(\AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC01-T02 商品一覧ページ ヘッダ以外のカテゴリリンク');
        $I->amOnPage('/');

        // MEMO: EF0201-UC01-T02... テスト項目の記述が意味不明なのでskip
    }

    public function product_商品一覧ソート(\AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC03-T01 商品一覧ページ ソート');
        $topPage = TopPage::go($I);

        // TOPページ>商品一覧（ヘッダーのいずれかのカテゴリを選択）へ遷移
        $topPage->カテゴリ選択(['キッチンツール']);

        // 各商品のサムネイルが表示される デフォルトは価格順
        $products = $I->grabMultiple(['xpath' => "//*[@class='ec-shelfGrid__item']/a/p[2]"]);
        $pPos = 0;
        $fPos = 0;
        foreach ($products as $key => $product) {
            if ($product == 'パーコレーター') {
                $pPos = $key;
            }
            if ($product == 'ディナーフォーク') {
                $fPos = $key;
            }
        }
        $I->assertTrue(($pPos < $fPos));

        $listPage = new ProductListPage($I);
        // ソート条件の選択リストを変更する 価格順->新着順
        $listPage
            ->表示件数設定(30)
            ->表示順設定('新着順');

        // 変更されたソート条件に従い、商品がソートされる
        $products = $I->grabMultiple(['xpath' => "//*[@class='ec-shelfGrid__item']/a/p[1]"]);
        $pPos = 0;
        $fPos = 0;
        foreach ($products as $key => $product) {
            if ($product == 'パーコレーター') {
                $pPos = $key;
            }
            if ($product == 'ディナーフォーク') {
                $fPos = $key;
            }
        }
        // ToDo [issue]
        // まだバグ修正前 https://github.com/EC-CAUBE/ec-cube/issues/1118
        // 修正されたら以下を追加
        //$I->assertTrue(($pPos > $fPos));
    }

    public function product_商品一覧表示件数(\AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC04-T01 商品一覧ページ 表示件数');
        $topPage = TopPage::go($I);

        // TOPページ>商品一覧（ヘッダーのいずれかのカテゴリを選択）へ遷移
        $topPage->カテゴリ選択(['キッチンツール']);
        $listPage = new ProductListPage($I);

        // 各商品のサムネイルが表示される
        $config = Fixtures::get('test_config');
        $productNum = $config['fixture_product_num'] + 2;
        $itemNum = ($productNum >= 15) ? 15 : $productNum;
        $I->assertEquals($itemNum, $listPage->一覧件数取得());

        // 表示件数の選択リストを変更する
        $listPage->表示件数設定(30);

        // 変更された表示件数分が1画面に表示される
        $expected = ($productNum >= 30) ? 30 : $productNum;
        $I->assertEquals($expected, $listPage->一覧件数取得());
    }

    public function product_商品一覧ページング(\AcceptanceTester $I)
    {
        $I->wantTo('EF0201-UC04-T02 商品一覧ページ ページング');
        $topPage = TopPage::go($I);

        // TOPページ>商品一覧（ヘッダーのいずれかのカテゴリを選択）へ遷移
        $topPage->カテゴリ選択(['キッチンツール']);

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

    public function product_商品詳細初期表示(\AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC01-T01 商品詳細 初期表示');
        $I->setStock(2, 0);
        ProductDetailPage::go($I, 2);

        // 「カートに入れる」ボタンが、非活性となり「ただいま品切れ中です」と表示される。
        $I->see('ただいま品切れ中です', '#form1 button');
    }

    public function product_商品詳細カテゴリリンク(\AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC01-T02 商品詳細 カテゴリリンク');
        $productPage = ProductDetailPage::go($I, 2);

        // 商品詳細の関連カテゴリに表示されている、カテゴリリンクを押下する
        $productPage->カテゴリ選択(['キッチンツール', '調理器具']);

        // 登録商品がカテゴリごとに一覧表示される
        $I->see('調理器具', '.ec-topicpath');

        // 一覧ページで商品がサムネイル表示される
        $I->see('パーコレーター', '.ec-shelfGrid');
    }

    public function product_商品詳細サムネイル(\AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC01-T03 商品詳細 サムネイル');
        $productPage = ProductDetailPage::go($I, 2);

        // デフォルトサムネイル表示確認
        $img = $productPage->サムネイル画像URL();
        $I->assertRegExp('/\/upload\/save_image\/cafe-1\.jpg$/', $img, $img.' が見つかりません');

        // 2個目のサムネイルクリック
        $productPage->サムネイル切替(2);
        $img = $productPage->サムネイル画像URL();
        $I->assertRegExp('/\/upload\/save_image\/cafe-2\.jpg$/', $img, $img.' が見つかりません');
    }

    public function product_商品詳細カート1(\AcceptanceTester $I)
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
        $I->assertContains('パーコレーター', $cartPage->商品名(1));
        $I->assertContains('4', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    public function product_商品詳細カート2(\AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T02 商品詳細 カート 販売制限数＜注文数＜在庫数の注文');
        $I->setStock(2, 10);

        $productPage = ProductDetailPage::go($I, 2);

        // 「カートに入れる」ボタンを押下する
        $productPage->カートに入れる(6);
        $I->wait(5);

        $I->assertContains('選択された商品(パーコレーター)は販売制限しております。 一度に販売制限数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('パーコレーター', $cartPage->商品名(1));
        $I->assertContains('5', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    public function product_商品詳細カート3(\AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T03 商品詳細 カート 販売制限数＜在庫数＜注文数の注文');
        $I->setStock(2, 10);

        $productPage = ProductDetailPage::go($I, 2);

        // 「カートに入れる」ボタンを押下する
        $productPage->カートに入れる(12);
        $I->wait(5);

        $I->assertContains('選択された商品(パーコレーター)は販売制限しております。 一度に販売制限数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('パーコレーター', $cartPage->商品名(1));
        $I->assertContains('5', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    public function product_商品詳細カート4(\AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T04 商品詳細(規格あり) カート 注文数＜販売制限数＜在庫数の注文');
        $I->setStock(1, array(10, 10, 10, 10, 10, 10, 10, 10, 10));

        $productPage = ProductDetailPage::go($I, 1)
            ->規格選択(['プラチナ', '150cm'])
            ->カートに入れる(1);

        $I->wait(5);

        $I->assertContains('カートに追加しました。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('ディナーフォーク', $cartPage->商品名(1));
        $I->assertContains('1', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    public function product_商品詳細カート5(\AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T05 商品詳細(規格あり) カート 販売制限数＜注文数＜在庫数の注文');
        $I->setStock(1, array(10, 10, 10, 10, 10, 10, 10, 10, 10));

        $productPage = ProductDetailPage::go($I, 1)
            ->規格選択(['プラチナ', '150cm'])
            ->カートに入れる(3);

        $I->wait(5);

        $I->assertContains('選択された商品(ディナーフォーク - プラチナ - 150cm)は販売制限しております。 一度に販売制限数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('ディナーフォーク', $cartPage->商品名(1));
        $I->assertContains('2', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }

    public function product_商品詳細カート6(\AcceptanceTester $I)
    {
        $I->wantTo('EF0202-UC02-T06 商品詳細(規格あり) カート 販売制限数＜在庫数＜注文数の注文');
        $I->setStock(1, array(10, 10, 10, 10, 10, 10, 10, 10, 10));

        $productPage = ProductDetailPage::go($I, 1)
            ->規格選択(['プラチナ', '150cm'])
            ->カートに入れる(12);

        $I->wait(5);

        $I->assertContains('選択された商品(ディナーフォーク - プラチナ - 150cm)は販売制限しております。 一度に販売制限数を超える購入はできません。', $productPage->カートに追加());

        $cartPage = $productPage->カートへ進む();

        // 入力された個数分が、カート画面の対象商品に追加されている。
        $I->assertContains('ディナーフォーク', $cartPage->商品名(1));
        $I->assertContains('2', $cartPage->商品数量(1));

        // カートを空に
        $cartPage->商品削除(1);
    }
}

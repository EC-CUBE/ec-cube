<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Codeception\Util\Fixtures;
use Page\Front\TopPage;

/**
 * @group front
 * @group toppage
 * @group ef1
 */
class EF01TopCest
{
    public function _before(\AcceptanceTester $I)
    {
    }

    public function _after(\AcceptanceTester $I)
    {
    }

    public function topページ_初期表示(\AcceptanceTester $I)
    {
        $I->wantTo('EF0101-UC01-T01 TOPページ 初期表示');
        TopPage::go($I);

        // カテゴリ選択ボックス（キーワード検索用）、キーワード検索入力欄、虫眼鏡ボタンが表示されている
        $I->see('全ての商品', TopPage::$検索_カテゴリ選択);
        $I->see('', TopPage::$検索_カテゴリ選択);

        // カテゴリ名（カテゴリ検索用）が表示されている
        $categories = Fixtures::get('categories');
        foreach ($categories as $category) {
            $I->see($category->getName(), '#searchform #category_id option');
        }

        //管理側のコンテンツ管理（新着情報管理）に設定されている情報が、順位順に表示されている
        $today = new DateTime();
        $minus1 = $today->sub(new DateInterval('P1D'));
        $minus2 = $today->sub(new DateInterval('P2D'));

        $createNews = Fixtures::get('createNews');
        $News1 = $createNews($minus1, 'タイトル1', 'コメント1');
        $News2 = $createNews($minus2, 'タイトル2', 'コメント2');

        $I->reloadPage();

        $findNews = Fixtures::get('findNews');
        $newsAll = $findNews();
        foreach ($newsAll as $index => $news) {
            $rowNum = $index + 1;
            $I->see($news['title'], 'div.ec-news .ec-news__item:nth-child('.$rowNum.') .ec-newsline__title');
            // 5件を超えるとread moreが表示される.
            if ($rowNum > 5) {
                break;
            }
        }

        $em = Fixtures::get('entityManager');
        $em->remove($News1);
        $em->remove($News2);
        $em->flush([$News1, $News2]);
    }

    public function topページ_新着情報(\AcceptanceTester $I)
    {
        $I->wantTo('EF0101-UC01-T02 TOPページ 新着情報');

        $createNews = Fixtures::get('createNews');
        $News = $createNews(new \DateTime(), 'タイトル1', 'コメント1', 'https://www.ec-cube.net');

        $topPage = TopPage::go($I);

        // 各新着情報の箇所を押下する
        // Knowhow: javascriptでclick eventハンドリングしている場合はclick('表示文字列')では探せない
        $topPage->新着情報選択(1);

        // 押下された新着情報のセクションが広がり、詳細情報、リンクが表示される
        $I->assertContains('コメント1', $topPage->新着情報詳細(1));

        // 「詳しくはこちら」リンクを押下する
        $I->assertContains('詳しくはこちら', $topPage->新着情報詳細(1));
        $topPage->新着情報リンククリック(1);
        $I->amOnUrl($News->getUrl());

        $em = Fixtures::get('entityManager');
        $em->remove($News);
        $em->flush($News);
    }

    public function topページ_カテゴリ検索(\AcceptanceTester $I)
    {
        $I->wantTo('EF0101-UC02-T01 TOPページ カテゴリ検索');
        $topPage = TopPage::go($I);

        // カテゴリを選択、そのまま続けて子カテゴリを選択する
        $topPage->カテゴリ選択(['キッチンツール', '調理器具']);

        // 商品一覧の上部に、選択されたカテゴリとその親カテゴリのリンクが表示される
        $I->see('調理器具', '.ec-topicpath');
        $I->see('パーコレーター', '.ec-shelfGrid');
    }

    public function topページ_全件検索(\AcceptanceTester $I)
    {
        $I->wantTo('EF0101-UC03-T01 TOPページ 全件検索');
        $topPage = TopPage::go($I);
        $topPage->検索();

        // 商品一覧の上部に、選択されたカテゴリとその親カテゴリのリンクが表示される
        $I->see('全て', '.ec-topicpath');

        // カテゴリに分類されている商品のみ表示される
        $products = $I->grabMultiple('ul.ec-shelfGrid li.ec-shelfGrid__item');
        $I->assertTrue((count($products) >= 2));
    }

    public function topページ_カテゴリ絞込検索(\AcceptanceTester $I)
    {
        $I->wantTo('EF0101-UC03-T02 TOPページ カテゴリ絞込検索');
        $topPage = TopPage::go($I);

        // カテゴリを選択する
        $I->selectOption(['id' => 'category_id'], '調理器具');

        // 虫眼鏡ボタンを押下する
        $topPage->検索();

        // 商品一覧の上部に、選択されたカテゴリとその親カテゴリのリンクが表示される
        $I->see('調理器具', '.ec-topicpath');

        // カテゴリに分類されている商品のみ表示される
        $I->see('パーコレーター', '.ec-shelfGrid');
        $I->dontSee('ディナーフォーク', '.ec-shelfGrid');
    }

    public function topページ_キーワード絞込検索(\AcceptanceTester $I)
    {
        $I->wantTo('EF0101-UC03-T02 TOPページ キーワード絞込検索');
        $topPage = TopPage::go($I);

        // キーワードを入力する
        $I->fillField(['id' => 'name'], 'フォーク');

        // 虫眼鏡ボタンを押下する
        $topPage->検索();

        // 商品一覧の上部に、選択されたカテゴリとその親カテゴリのリンクが表示される
        $I->see('フォーク', '.ec-topicpath');

        // カテゴリに分類されている商品のみ表示される
        $I->dontSee('パーコレーター', '.ec-topicpath');
        $I->see('ディナーフォーク', '.ec-shelfGrid');
    }
}

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

namespace Plugin;

use AcceptanceTester;
use Page\Admin\ProductReviewManagePage;
use Page\Front\ProductDetailPage;
use Page\Front\ProductReviewPage;

/**
 * @group plugin
 * @group vaddy
 */
class PL07ProductReviewCest
{
    public function レビュー投稿(AcceptanceTester $I)
    {
        ProductDetailPage::go($I, 1);
        $I->scrollTo(['css' => '#product_review_area > div > div:nth-child(3) > a'], 0, -100);
        $I->click(['css' => '#product_review_area > div > div:nth-child(3) > a']);

        ProductReviewPage::at($I)
            ->入力_投稿者名('test')
            ->入力_URL('http://example.com')
            ->入力_性別()
            ->入力_おすすめレベル()
            ->入力_タイトル('test')
            ->入力_コメント('test')
            ->確認ページへ()
            ->投稿する();
    }

    public function レビュー管理(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        ProductReviewManagePage::go($I)
            ->検索()
            ->CSVダウンロード()
            ->編集(1);
        $I->click(['css' => '#page_product_review_admin_product_review_edit > div.c-container > div.c-contentsArea > form > div.c-conversionArea > div > div > div:nth-child(2) > div > div:nth-child(2) > button']);

        ProductReviewManagePage::go($I)
            ->削除(1);
    }
}

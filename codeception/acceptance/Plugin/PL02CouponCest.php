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
use Page\Admin\CouponEditPage;
use Page\Admin\CouponManagePage;

/**
 * @group plugin
 * @group vaddy
 */
class PL02CouponCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function 新規登録(AcceptanceTester $I)
    {
        CouponManagePage::go($I)
            ->新規登録();

        CouponEditPage::at($I)
            ->入力_クーポン名('test')
            ->選択_対象_商品()
            ->入力_値引き額(100)
            ->入力_発行枚数(10)
            ->入力_有効期限開始('2021-01-01')
            ->入力_有効期限終了('2021-02-01')
            ->商品追加()
            ->商品削除()
            ->選択_対象_カテゴリ()
            ->カテゴリ追加()
            ->登録する();

        CouponManagePage::at($I);
    }

    public function 編集(AcceptanceTester $I)
    {
        CouponManagePage::go($I)
            ->編集(1);

        CouponEditPage::at($I)
            ->登録する();

        CouponManagePage::at($I);
    }

    public function 有効化(AcceptanceTester $I)
    {
        CouponManagePage::go($I)
            ->状態変更(1);

        CouponManagePage::at($I);
    }

    public function 削除(AcceptanceTester $I)
    {
        CouponManagePage::go($I)
            ->削除(1);

        CouponManagePage::at($I);
    }
}

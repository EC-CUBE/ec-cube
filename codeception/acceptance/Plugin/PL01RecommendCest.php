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
use Page\Admin\RecommendEditPage;
use Page\Admin\RecommendManagePage;

/**
 * @group plugin
 * @group vaddy
 */
class PL01RecommendCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function 新規作成(AcceptanceTester $I)
    {
        RecommendManagePage::go($I)
            ->新規登録();

        RecommendEditPage::at($I)
            ->商品追加()
            ->入力_説明文('test')
            ->登録();

        RecommendManagePage::at($I);
    }

    public function 編集(AcceptanceTester $I)
    {
        RecommendManagePage::go($I)
            ->編集(1);
        RecommendEditPage::at($I)
            ->登録();
        RecommendManagePage::at($I);
    }

    public function 削除(AcceptanceTester $I)
    {
        RecommendManagePage::go($I)
            ->削除(1);
        RecommendManagePage::at($I);
    }
}

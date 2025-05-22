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
use Page\Admin\RelatedProductEditPage;

/**
 * @group plugin
 * @group vaddy
 */
class PL05RelatedProductCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function 関連商品設定(AcceptanceTester $I)
    {
        RelatedProductEditPage::goEdit($I, 1)
            ->選択_関連商品1()
            ->入力_説明文1('test')
            ->登録();

        RelatedProductEditPage::goEdit($I, 1)
            ->削除_関連商品1()
            ->登録();
    }
}

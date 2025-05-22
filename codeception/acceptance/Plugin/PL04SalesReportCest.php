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
use Page\Admin\SalesReportPage;

/**
 * @group plugin
 * @group vaddy
 */
class PL04SalesReportCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function 集計(AcceptanceTester $I)
    {
        $yesterday = new DateTime('-1 day');
        $lastMonth = new DateTime('-1 month');
        SalesReportPage::goTerm($I)
            ->選択_日別()
            ->月度で集計($yesterday->format('Ym'))
            ->選択_月別()
            ->期間で集計($lastMonth->format('Y-m-d'), $yesterday->format('Y-m-d'))
            ->選択_曜日別()
            ->月度で集計($yesterday->format('Ym'))
            ->選択_時間別()
            ->期間で集計($lastMonth->format('Y-m-d'), $yesterday->format('Y-m-d'))
            ->CSVダウンロード();

        SalesReportPage::goProduct($I)
            ->月度で集計($yesterday->format('Ym'))
            ->期間で集計($lastMonth->format('Y-m-d'), $yesterday->format('Y-m-d'))
            ->CSVダウンロード();

        SalesReportPage::goAge($I)
            ->月度で集計($yesterday->format('Ym'))
            ->期間で集計($lastMonth->format('Y-m-d'), $yesterday->format('Y-m-d'))
            ->CSVダウンロード();
    }
}

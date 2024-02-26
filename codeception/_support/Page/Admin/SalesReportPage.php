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

namespace Page\Admin;

class SalesReportPage extends AbstractAdminPageStyleGuide
{
    public static function goTerm(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/sales_report/term', '売上管理期間別集計');
        return $page;
    }

    public static function goProduct(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/sales_report/product', '売上管理商品別集計');
        return $page;
    }

    public static function goAge(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/sales_report/age', '売上管理年代別集計');
        return $page;
    }

    public function 選択_日別()
    {
        $this->tester->click(['id' => 'sales_report_unit_0']);
        return $this;
    }

    public function 選択_月別()
    {
        $this->tester->click(['id' => 'sales_report_unit_0']);
        return $this;
    }

    public function 選択_曜日別()
    {
        $this->tester->click(['id' => 'sales_report_unit_0']);
        return $this;
    }

    public function 選択_時間別()
    {
        $this->tester->click(['id' => 'sales_report_unit_0']);
        return $this;
    }

    public function 月度で集計($yyyyMm)
    {
        $this->tester->selectOption(['id' => 'sales_report_monthly_year'], intval(substr($yyyyMm, 0, 4)));
        $this->tester->selectOption(['id' => 'sales_report_monthly_month'], intval(substr($yyyyMm, 4, 2)));
        $this->tester->click(['id' => 'btn-monthly']);
        return $this;
    }

    public function 期間で集計($start, $end)
    {
        $this->tester->executeJS("$('#sales_report_term_start').val('${start}')");
        $this->tester->wait(1);
        $this->tester->executeJS("$('#sales_report_term_end').val('${end}')");
        $this->tester->wait(1);
        $this->tester->click(['id' => 'btn-term']);
        return $this;
    }

    public function CSVダウンロード()
    {
        $this->tester->click(['id' => 'export-csv']);
        return $this;
    }
}

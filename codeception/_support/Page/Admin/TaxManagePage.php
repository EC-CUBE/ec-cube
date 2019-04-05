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

class TaxManagePage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '.c-container .c-contentsArea .alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/shop/tax', '税率設定店舗設定');
    }

    public function 入力_消費税率($row, $value)
    {
        $this->tester->fillField(['css' => 'table tbody tr:nth-child('.$row.') input[type=number]'], $value);

        return $this;
    }

    public function 入力_適用日($year, $month, $day)
    {
        $this->tester->executeJS("document.getElementById('tax_rule_apply_date_date_year').value = '{$year}'");
        $this->tester->executeJS("document.getElementById('tax_rule_apply_date_date_month').value = '{$month}'");
        $this->tester->executeJS("document.getElementById('tax_rule_apply_date_date_day').value = '{$day}'");

        return $this;
    }

    public function 入力_適用時($hour, $minute)
    {
        $this->tester->executeJS("document.getElementById('tax_rule_apply_date_time_hour').value = '{$hour}'");
        $this->tester->executeJS("document.getElementById('tax_rule_apply_date_time_minute').value = '{$minute}'");

        return $this;
    }

    public function 入力_個別税率設定($row, $value)
    {
        $this->tester->checkOption(['css' => 'table tbody tr:nth-child('.$row.') select:nth-child(1)'], $value);

        return $this;
    }

    public function 個別税率設定_登録()
    {
        $this->tester->click('#form1 div div div:nth-child(2) button');

        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click("table tbody tr:nth-child(${rowNum}) .edit-button");

        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->tester->click("table tbody tr:nth-child(${rowNum}) > td.align-middle.action > div > div > div:nth-child(2) > div.d-inline-block.mr-3 > a");

        // accept modal
        $this->tester->waitForElementVisible("table tbody tr:nth-child(${rowNum}) > td.align-middle.action > div > div > div:nth-child(2) > div.modal");
        $this->tester->click("table tbody tr:nth-child(${rowNum}) > td.align-middle.action > div > div > div:nth-child(2) > div.modal.fade.show > div > div > div.modal-footer > a");

        return $this;
    }

    public function 一覧_税率($rowNum)
    {
        return "table > tbody > tr:nth-child(${rowNum}) > td.align-middle.text-right .list";
    }

    public function 共通税率設定_登録()
    {
        $this->tester->click('table tbody tr:nth-child(1) button');

        return;
    }

    public function 決定($row)
    {
        $this->tester->click('table tbody tr:nth-child('.$row.') > td > div.edit > button.btn.btn-ec-conversion');

        return;
    }
}

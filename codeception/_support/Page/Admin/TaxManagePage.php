<?php


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
        return $page->goPage('/setting/shop/tax', '税率設定基本情報設定');
    }

    public function 入力_消費税率($row, $value) {
        $this->tester->fillField(['css' => 'table tbody tr:nth-child('.$row.') input[type=number]'], $value);
        return $this;
    }

    public function 入力_適用日時($row, $value) {
//        $this->tester->fillField(['css' => 'table tbody tr:nth-child('.$row.') input[type=date]'], $value);
        $this->tester->executeJS("document.getElementById('tax_rule_apply_date').value = '{$value}'");
        return $this;
    }

    public function 入力_個別税率設定($row, $value) {
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
        $this->tester->click("table tbody tr:nth-child(${rowNum}) > td.align-middle.action > div > div:nth-child(2) > a");
        $this->tester->acceptPopup();
        return $this;
    }

    public function 一覧_税率($rowNum)
    {
        return "table > tbody > tr:nth-child(${rowNum}) > td.align-middle.text-right";
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
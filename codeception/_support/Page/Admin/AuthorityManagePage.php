<?php


namespace Page\Admin;

class AuthorityManagePage extends AbstractAdminPageStyleGuide
{
    public static $完了メッセージ = '.c-contentsArea .alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        return $page->goPage('/setting/system/authority', '権限管理システム設定');
    }

    public function 行追加()
    {
        $this->tester->click('form .add');
        return $this;
    }

    public function 行削除($rowNum)
    {
        $this->tester->click(['css' => "form #table-authority tbody tr:nth-child($rowNum) td:nth-child(3) button"]);
        return $this;
    }

    public function 入力($rowNum, $role, $url) {
        $this->tester->selectOption(['css' => "form #table-authority tbody tr:nth-child(${rowNum}) td:nth-child(1) select"], $role);
        $this->tester->fillField(['css' => "form #table-authority tbody tr:nth-child(${rowNum}) td:nth-child(2) input"], $url);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click('form .c-conversionArea button');
        return $this;
    }
}
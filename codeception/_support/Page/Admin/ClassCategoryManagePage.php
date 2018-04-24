<?php

namespace Page\Admin;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;

class ClassCategoryManagePage extends AbstractAdminPageStyleGuide
{

    public static $登録完了メッセージ = ['css' => '#page_admin_product_class_category > div > div.c-contentsArea > div.alert'];

    public static $分類名 = ['id' => 'admin_class_category_name'];

    /**
     * ProductClassCategoryPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new ClassCategoryManagePage($I);
        return $page->atPage('規格管理商品管理');
    }

    public function 入力_分類名($value)
    {
        $this->tester->fillField(self::$分類名, $value);
        return $this;
    }

    public function 分類作成()
    {
        $this->tester->click('#form1 button');
        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $rowNum += 1;
        $this->tester->click("ul.list-group > li:nth-child(${rowNum}) a:nth-child(3)");
        return $this;
    }

    public function 一覧_入力_分類名($row, $value)
    {
        $row += 1;
        $this->tester->fillField(['css' => "ul.list-group > li:nth-child(${row}) form input[type=text]"], $value);
        return $this;
    }

    public function 一覧_分類作成($row)
    {
        $row += 1;
        $this->tester->click("ul.list-group > li:nth-child(${row}) form button[type=submit]");
        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $rowNum += 1;
        $this->tester->click("ul.list-group > li:nth-child(${rowNum}) a:nth-child(4)");
        return $this;
    }

    public function acceptModal($rowNum)
    {
        $rowNum += 1;
        $this->tester->waitForElementVisible("ul.list-group > li:nth-child(${rowNum}) div.modal");
        $this->tester->click("ul.list-group > li:nth-child(${rowNum}) div.modal a.btn-ec-delete");
        return $this;
    }

    public function 一覧_上に($rowNum)
    {

        $rowNum += 1;
        $this->tester->dragAndDropBy("ul.list-group > li:nth-child(${rowNum})", 0, -60);
        return $this;
    }

    public function 一覧_下に($rowNum)
    {
        $rowNum += 1;
        $this->tester->dragAndDropBy("ul.list-group > li:nth-child(${rowNum})", 0, 60);
        return $this;
    }

    public function 一覧_名称($rowNum)
    {
        $rowNum += 1;
        return "ul.list-group > li:nth-child(${rowNum}) > div > div.col.d-flex.align-items-center";
    }
}
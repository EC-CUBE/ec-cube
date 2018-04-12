<?php

namespace Page\Admin;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Interactions\DragAndDropBy;

/**
 * 商品管理規格編集
 * @package Page\Admin
 */
class ClassNameManagePage extends AbstractAdminPageStyleGuide
{

    public static $登録完了メッセージ = ['css' => '#page_admin_product_class_name > div > div.c-contentsArea > div.alert'];
    public static $管理名 = ['id' => 'admin_class_name_name'];

    /**
     * ProductClassPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new ClassNameManagePage($I);
        return $page->goPage('/product/class_name', '規格管理商品管理');
    }

    public function 入力_管理名($value)
    {
        $this->tester->fillField(self::$管理名, $value);
        return $this;
    }

    public function 規格作成()
    {
        $this->tester->click(['css' => '#form1 button']);
        return $this;
    }

    public function 一覧_名称($rowNum)
    {
        $rowNum += 1;
        return "ul.tableish > li:nth-child(${rowNum}) > div > div:nth-child(2) a:nth-child(1)";
    }

    public function 一覧_分類登録($rowNum)
    {
        $rowNum += 1;
        $this->tester->click("ul.tableish > li:nth-child(${rowNum}) > div > div:nth-child(2) a:nth-child(1)");
        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $this->一覧_オプション($rowNum);
        $this->tester->click("#main .container-fluid .box .box-body .item_box:nth-child(${rowNum}) .icon_edit .dropdown ul li:nth-child(2) a");
        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $rowNum += 1;
        $this->tester->click("ul.list-group > li:nth-child(${rowNum}) a:nth-child(4)");
        return $this;
    }

    private function 一覧_オプション($rowNum)
    {
        $this->tester->click("#main .container-fluid .box .box-body .item_box:nth-child(${rowNum}) .icon_edit .dropdown a");
    }

    public function 一覧_上に($rowNum)
    {
        $rowNum += 1;
        $this->tester->dragAndDropBy("ul.tableish > li:nth-child(${rowNum})", 0, -60);
        return $this;
    }

    public function 一覧_下に($rowNum)
    {
        $rowNum += 1;
        $this->tester->dragAndDropBy("ul.tableish > li:nth-child(${rowNum})", 0, 60);
        return $this;
    }
}
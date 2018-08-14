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
    public static $管理名 = ['id' => 'admin_class_name_backend_name'];
    public static $表示名 = ['id' => 'admin_class_name_name'];
    public static $管理名編集3 = ['id' => 'class_name_3_backend_name'];
    public static $表示名編集3 = ['id' => 'class_name_3_name'];

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

    public function 入力_表示名($value)
    {
        $this->tester->fillField(self::$表示名, $value);
        return $this;
    }

    public function 規格作成()
    {
        $this->tester->click(['css' => '#form1 button']);
        return $this;
    }

    public function 規格編集($rowNum)
    {
        $this->tester->click("#page_admin_product_class_name > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div > div > ul > li:nth-child(${rowNum}) > form > div:nth-child(6) > button");
        return $this;
    }

    public function 一覧_名称($rowNum)
    {
        $rowNum += 1;
        return "#page_admin_product_class_name > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col.d-flex.align-items-center > a";
    }

    public function 一覧_分類登録($rowNum)
    {
        $rowNum += 1;
        $this->tester->click("#page_admin_product_class_name > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col.d-flex.align-items-center > a");
        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click("#page_admin_product_class_name > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col-auto.text-right > a.btn.btn-ec-actionIcon.mr-3.action-edit");
        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $rowNum += 1;
        $this->tester->click("ul.list-group > li:nth-child(${rowNum}) > div > div.col-auto.text-right > div > a");
        return $this;
    }

    public function acceptModal()
    {
        $this->tester->waitForElementVisible("#delete_modal");
        $this->tester->click("#delete_modal > div > div > div.modal-footer > a");
        return $this;
    }

    public function 一覧_上に($rowNum)
    {
        $rowNum += 1;
        $this->tester->dragAndDropBy("#page_admin_product_class_name > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div > div > ul > li:nth-child(${rowNum})", 0, -60);
        return $this;
    }

    public function 一覧_下に($rowNum)
    {
        $rowNum += 1;
        $this->tester->dragAndDropBy("#page_admin_product_class_name > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div > div > ul > li:nth-child(${rowNum})", 0, 60);
        return $this;
    }
}

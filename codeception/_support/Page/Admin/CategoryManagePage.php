<?php

namespace Page\Admin;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;

class CategoryManagePage extends AbstractAdminPageStyleGuide
{

    public static $登録完了メッセージ = 'body > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3';
    public static $パンくず_1階層 = 'body > div > div.c-contentsArea > div.c-outsideBlock > div > div > div:nth-child(1) > nav > ol > li:nth-child(2) > a';
    public static $パンくず_2階層 = 'body > div > div.c-contentsArea > div.c-outsideBlock > div > div > div:nth-child(1) > nav > ol > li.nth-child(3) > a';
    public static $パンくず_3階層 = 'body > div > div.c-contentsArea > div.c-outsideBlock > div > div > div:nth-child(1) > nav > ol > li:nth-child(4) > a';
    public static $パンくず_4階層 = 'body > div > div.c-contentsArea > div.c-outsideBlock > div > div > div:nth-child(1) > nav > ol > li:nth-child(5) > a';

    /**
     * CategoryPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        return $page->goPage('/product/category', '商品管理カテゴリ編集');
    }

    public function 入力_カテゴリ名($value)
    {
        $this->tester->fillField(['id' => 'admin_category_name'], $value);
        return $this;
    }

    public function カテゴリ作成()
    {
        $this->tester->click('#form1 > div:nth-child(2) > div:nth-child(2) > button');
        return $this;
    }

    public function 一覧_選択($rowNum)
    {
        $this->tester->click("body > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div.c-contentsArea__primaryCol > div > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col.d-flex.align-items-center > a");
        return $this;
    }

    public function 一覧_編集($rowNum)
    {
        $this->tester->click("body > div > div.c-contentsArea > div.c-contentsArea__cols > div.c-contentsArea__primaryCol > div > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col-auto.text-right > a:nth-child(3)");
        return $this;
    }

    public function 一覧_インライン編集_カテゴリ名($rowNum, $value)
    {
        $this->tester->fillField('body > div > div.c-contentsArea > div.c-contentsArea__cols > div.c-contentsArea__primaryCol > div > div > div > div > ul > li:nth-child('.$rowNum.') > form.mode-edit input[type="text"][name*="category_"]', $value);
        return $this;
    }

    public function 一覧_インライン編集_決定($rowNum)
    {
        $this->tester->click('body > div > div.c-contentsArea > div.c-contentsArea__cols > div.c-contentsArea__primaryCol > div > div > div > div > ul > li:nth-child('.$rowNum.') > form.mode-edit button[type="submit"]');
        return $this;
    }

    public function 一覧_削除($rowNum)
    {
        $this->tester->click("body > div > div.c-contentsArea > div.c-contentsArea__cols > div.c-contentsArea__primaryCol > div > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col-auto.text-right > div > a");
        return $this;
    }

    public function acceptModal()
    {
        $this->tester->waitForElementVisible("#delete_modal");
        $this->tester->click("#delete_modal > div > div > div.modal-footer > a");
        return $this;
    }

    public function CSVダウンロード実行()
    {
        $this->tester->click('body > div > div.c-contentsArea > div.c-outsideBlock > div > div > div.col-6.text-right > div > button:nth-child(1)');
        return $this;
    }

    public function CSV出力項目設定()
    {
        $this->tester->click('body > div > div.c-contentsArea > div.c-outsideBlock > div > div > div.col-6.text-right > div > button:nth-child(2)');
    }

    public function 一覧_上に($rowNum)
    {
        $this->tester->dragAndDropBy("body > div > div.c-contentsArea > div.c-contentsArea__cols > div.c-contentsArea__primaryCol > div > div > div > div > ul > li:nth-child($rowNum)", 0, -75);
        return $this;
    }

    public function 一覧_下に($rowNum)
    {
        $this->tester->dragAndDropBy("body > div > div.c-contentsArea > div.c-contentsArea__cols > div.c-contentsArea__primaryCol > div > div > div > div > ul > li:nth-child($rowNum)", 0, 75);
        return $this;
    }

    public function 一覧_名称($rowNum)
    {
        return "body > div > div.c-contentsArea > div.c-contentsArea__cols > div.c-contentsArea__primaryCol > div > div > div > div > ul > li:nth-child($rowNum) > div > div.col.d-flex.align-items-center > a";
    }

    public static function XPathでタグを取得する($textEl) {
        return '//*[@id="page_admin_product_category"]/div[1]/div[3]/div[3]/div[1]/div/div/div/div/ul/li/div/div[2]/a[contains(text(), "'.$textEl.'")]';
    }
}
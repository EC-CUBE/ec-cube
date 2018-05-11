<?php

/**
 * 商品管理/商品登録
 */
namespace Page\Admin;

class ProductEditPage extends AbstractAdminPageStyleGuide
{
    public static $登録結果メッセージ = '#page_admin_product_product_edit > div.c-container > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3';
    public static $販売種別 = ['id' => 'admin_product_class_sale_type'];
    public static $通常価格 = ['id' => 'admin_product_class_price01'];
    public static $販売価格 = ['id' => 'admin_product_class_price02'];
    public static $在庫数 = ['id' => 'admin_product_class_stock'];
    public static $商品コード = ['id' => 'admin_product_class_code'];
    public static $販売制限数 = ['id' => 'admin_product_class_sale_limit'];
    public static $お届可能日 = ['id' => 'admin_product_class_delivery_duration'];

    /**
     * ProductRegisterPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new ProductEditPage($I);
        return $page->goPage('/product/product/new', '商品管理商品登録');
    }

    public static function at($I)
    {
        $page = new ProductEditPage($I);
        $page->tester->see('商品管理商品登録', '#page_admin_product_product_edit > div.c-container > div.c-contentsArea > div > div');
        return $page;
    }

    public function 入力_商品名($value)
    {
        $this->tester->fillField(['id' => 'admin_product_name'], $value);
        return $this;
    }

    public function 入力_販売価格($value)
    {
        $this->tester->fillField(self::$販売価格, $value);
        return $this;
    }

    public function 入力_公開()
    {
        $this->tester->selectOption('#admin_product_Status', '公開');
        return $this;
    }

    public function クリックして開くタグリスト()
    {
        $this->tester->click(['css' => 'div[href="#allTags"] > a']);
        return $this;
    }

    public function クリックして選択タグ($num)
    {
        $this->tester->click(['css' => "#allTags > div:nth-child(${num}) button"]);
        return $this;
    }

    public function 入力_カテゴリ($category_id)
    {
        $this->tester->checkOption(['id' => 'admin_product_category_'.$category_id]);
        return $this;
    }

    public function 規格管理()
    {
        $this->tester->click(['css' => '#standardConfig > div > div.d-block.text-center.text-center > a']);
        $this->tester->waitForElement(['css' => '#confirmFormChangeModal > div > div > div.modal-footer > a']);
        $this->tester->wait(1);
        $this->tester->click(['css' => '#confirmFormChangeModal > div > div > div.modal-footer > a']);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#form1 > div.c-conversionArea > div > div > div:nth-child(2) > div > div:nth-child(2) > button');
        return $this;
    }

}

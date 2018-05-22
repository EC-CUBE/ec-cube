<?php


namespace Page\Admin;


class ShopSettingPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '#page_admin_setting_shop > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        return $page->goPage('/setting/shop', 'ショップマスター基本情報設定');
    }

    public function 入力_会社名($value)
    {
        $this->tester->fillField(['id' => 'shop_master_company_name'], $value);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#point_form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');
        return $this;
    }
}
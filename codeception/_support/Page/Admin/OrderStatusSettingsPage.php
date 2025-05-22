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

class OrderStatusSettingsPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '#page_admin_setting_shop_order_status > div.c-container > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';
    public static $名称_マイページ = '#form_OrderStatuses_0_customer_order_status_name';
    public static $名称_管理 = '#form_OrderStatuses_0_name';

    /**
     * CsvSettingsPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/shop/order_status', '受注対応状況設定店舗設定');
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('受注対応状況設定店舗設定', '.c-pageTitle');

        return $page;
    }

    public function 入力_名称_管理($value)
    {
        $this->tester->fillField(['id' => 'form_OrderStatuses_0_name'], $value);

        return $this;
    }

    public function 入力_名称_マイページ($value)
    {
        $this->tester->fillField(['id' => 'form_OrderStatuses_0_customer_order_status_name'], $value);

        return $this;
    }

    public function 入力_色($value)
    {
        $this->tester->fillField(['id' => 'form_OrderStatuses_0_color'], $value);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#ex-conversion-action > div > button');

        return $this;
    }
}

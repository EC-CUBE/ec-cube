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

class CsvSettingsPage extends AbstractAdminPageStyleGuide
{
    public static $CSVタイプ = ['id' => 'csv-type'];

    public static $登録完了メッセージ = '#page_admin_setting_shop_csv > div.c-container > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';

    protected $tester;

    /**
     * CsvSettingsPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new CsvSettingsPage($I);

        return $page->goPage('/setting/shop/csv', 'CSV出力項目設定店舗設定');
    }

    public static function at($I)
    {
        $page = new CsvSettingsPage($I);
        $page->tester->see('CSV出力項目設定店舗設定', '.c-pageTitle');

        return $page;
    }

    public function 入力_CSVタイプ($value)
    {
        $this->tester->selectOption(['id' => 'csv-type'], $value);

        return $this;
    }

    public function 選択_出力項目($value)
    {
        $this->tester->selectOption(['id' => 'csv-output'], $value);

        return $this;
    }

    public function 削除()
    {
        $this->tester->click('#remove');

        return $this;
    }

    public function すべて出力()
    {
        $this->tester->click('#add-all');

        return $this;
    }

    public function 設定()
    {
        $this->tester->click('#csv-form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        return $this;
    }
}

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

class TradelawSettingPage extends AbstractAdminPage
{
    public static $登録完了メッセージ = '#page_admin_setting_shop_tradelaw > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';
    public static $販売業者 = 0;
    public static $代表責任者 = 1;
    public static $所在地 = 2;
    public static $電話番号 = 3;
    public static $メールアドレス = 4;
    public static $URL = 5;
    public static $商品代金以外の必要料金 = 6;
    public static $引き渡し時期 = 7;
    public static $返品交換について = 8;
    public static $その他01 = 9;
    public static $その他02 = 10;
    public static $その他03 = 11;
    public static $その他04 = 12;
    public static $その他05 = 13;
    public static $その他06 = 14;

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I): self
    {
        $page = new self($I);

        return $page->goPage('/setting/shop/tradelaw', '特定商取引法設定店舗設定');
    }

    public static function at($I): self
    {
        $page = new self($I);
        $page->tester->see('特定商取引法設定', '.c-pageTitle');

        return $page;
    }

    public function 入力(int $index, string $name, string$description): self
    {
        $this->tester->fillField(['id' => 'form_TradeLaws_'.$index.'_name'], $name);
        $this->tester->fillField(['id' => 'form_TradeLaws_'.$index.'_description'], $description);

        return $this;
    }

    public function 注文画面に表示(int $index): self
    {
        $this->tester->click('label[for=form_TradeLaws_'.$index.'_displayOrderScreen]');

        return $this;
    }

    public function 登録(): self
    {
        $this->tester->click('div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        return $this;
    }
}

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

class ShopSettingPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '#page_admin_setting_shop > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';
    public static $チェックボックス_仮会員機能 = 'shop_master_option_customer_activate';
    public static $チェックボックス_マイページに注文状況を表示 = 'shop_master_option_mypage_order_status_display';
    public static $チェックボックス_お気に入り商品機能 = 'shop_master_option_favorite_product';
    public static $チェックボックス_自動ログイン機能 = 'shop_master_option_remember_me';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/shop', '基本設定店舗設定');
    }

    public function 入力_会社名($value)
    {
        $this->tester->fillField(['id' => 'shop_master_company_name'], $value);

        return $this;
    }

    public function 入力_店名($value)
    {
        $this->tester->fillField(['id' => 'shop_master_shop_name'], $value);

        return $this;
    }

    public function 入力_郵便番号($value)
    {
        $this->tester->fillField(['id' => 'shop_master_postal_code'], $value);

        return $this;
    }

    public function 入力_電話番号($value)
    {
        $this->tester->fillField(['id' => 'shop_master_phone_number'], $value);

        return $this;
    }

    public function 入力_チェックボックス($id, $bool)
    {
        if ($this->tester->grabAttributeFrom(['id' => $id], 'checked') != $bool) {
            $this->tester->click("label[for='{$id}']");
            $this->tester->wait(1);
        }

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#point_form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        return $this;
    }
}

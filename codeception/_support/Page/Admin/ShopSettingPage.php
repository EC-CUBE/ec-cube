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

    public function 登録()
    {
        $this->tester->click('#point_form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        return $this;
    }

    public function 設定_在庫切れ商品の非表示($value)
    {
        $checked = $this->tester->grabAttributeFrom('#shop_master_option_nostock_hidden', 'checked');

        if (($value && ! $checked) || (!$value && $checked)) {
            $this->tester->click('label[for="shop_master_option_nostock_hidden"]');
            $this->tester->wait(1);
            $this->登録();
            $this->tester->see('保存しました', ShopSettingPage::$登録完了メッセージ);
        }
        $checked = $this->tester->grabAttributeFrom('#shop_master_option_nostock_hidden', 'checked');

        return $this;
    }
}

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

    public function 切替_カナ必須項目($value)
    {
        $cssClass = $this->tester->grabAttributeFrom(['css' => 'span.shop_master_option_require_kana-on'], 'class');
        $optionOn = strpos($cssClass, 'd-none') === false;
        $this->tester->scrollTo(['xpath' => '//label[@for="shop_master_option_require_kana"]'], 0, 100);
        if (($optionOn && !$value) || (!$optionOn && $value)) {
            $this->tester->click(['xpath' => '//label[@for="shop_master_option_require_kana"]']);
        }
        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#point_form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');

        return $this;
    }
}

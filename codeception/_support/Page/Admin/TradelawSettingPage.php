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
    public static $登録完了メッセージ = '#main .container-fluid div:nth-child(1) .alert-success';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/setting/shop/tradelaw', 'ショップ設定特定商取引法');
    }

    public function 入力_販売業者($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_company'], $value);

        return $this;
    }

    public function 入力_運営責任者($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_manager'], $value);

        return $this;
    }

    public function 入力_郵便番号($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_postal_code'], $value);

        return $this;
    }

    public function 入力_都道府県($value)
    {
        $this->tester->selectOption(['id' => 'tradelaw_address_pref'], $value);

        return $this;
    }

    public function 入力_市区町村名($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_address_addr01'], $value);

        return $this;
    }

    public function 入力_番地_ビル名($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_address_addr02'], $value);

        return $this;
    }

    public function 入力_電話番号($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_phone_number'], $value);

        return $this;
    }

    public function 入力_Eメール($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_email'], $value);

        return $this;
    }

    public function 入力_URL($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_url'], $value);

        return $this;
    }

    public function 入力_商品代金以外の必要料金($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_term01'], $value);

        return $this;
    }

    public function 入力_注文方法($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_term02'], $value);

        return $this;
    }

    public function 入力_支払方法($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_term03'], $value);

        return $this;
    }

    public function 入力_支払期限($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_term04'], $value);

        return $this;
    }

    public function 入力_引き渡し時期($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_term05'], $value);

        return $this;
    }

    public function 入力_返品交換について($value)
    {
        $this->tester->fillField(['id' => 'tradelaw_term06'], $value);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#tradelaw_form #aside_column button');
    }
}

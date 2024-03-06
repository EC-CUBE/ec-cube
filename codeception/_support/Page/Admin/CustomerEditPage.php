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

class CustomerEditPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = '#page_admin_customer_edit > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';

    /**
     * CustomerRegisterPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/customer/new', '会員登録会員管理');
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('会員登録会員管理');
    }

    public function 入力_姓($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_name_name01'], $value);

        return $this;
    }

    public function 入力_名($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_name_name02'], $value);

        return $this;
    }

    public function 入力_セイ($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_kana_kana01'], $value);

        return $this;
    }

    public function 入力_メイ($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_kana_kana02'], $value);

        return $this;
    }

    public function 入力_郵便番号($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_postal_code'], $value);

        return $this;
    }

    public function 入力_都道府県($value)
    {
        $this->tester->selectOption(['id' => 'admin_customer_address_pref'], $value);

        return $this;
    }

    public function 入力_市区町村名($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_address_addr01'], $value);

        return $this;
    }

    public function 入力_番地_ビル名($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_address_addr02'], $value);

        return $this;
    }

    public function 入力_Eメール($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_email'], $value);

        return $this;
    }

    public function 入力_電話番号($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_phone_number'], $value);

        return $this;
    }

    public function 入力_パスワード($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_plain_password_first'], $value);

        return $this;
    }

    public function 入力_パスワード確認($value)
    {
        $this->tester->fillField(['id' => 'admin_customer_plain_password_second'], $value);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#customer_form > div.c-conversionArea > div > div > div:nth-child(2) > div > div:nth-child(2) > button');

        return $this;
    }
}

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

namespace Page\Front;

class CustomerAddressEditPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('マイページ/お届け先一覧', 'div.ec-pageHeader h1');

        return $page;
    }

    public function 入力_姓($value)
    {
        $this->tester->fillField(['id' => 'customer_address_name_name01'], $value);

        return $this;
    }

    public function 入力_名($value)
    {
        $this->tester->fillField(['id' => 'customer_address_name_name02'], $value);

        return $this;
    }

    public function 入力_セイ($value)
    {
        $this->tester->fillField(['id' => 'customer_address_kana_kana01'], $value);

        return $this;
    }

    public function 入力_メイ($value)
    {
        $this->tester->fillField(['id' => 'customer_address_kana_kana02'], $value);

        return $this;
    }

    public function 入力_郵便番号($value)
    {
        $this->tester->fillField(['id' => 'customer_address_postal_code'], $value);

        return $this;
    }

    public function 入力_都道府県($value)
    {
        $this->tester->selectOption(['id' => 'customer_address_address_pref'], $value);

        return $this;
    }

    public function 入力_市区町村名($value)
    {
        $this->tester->fillField(['id' => 'customer_address_address_addr01'], $value);

        return $this;
    }

    public function 入力_番地_ビル名($value)
    {
        $this->tester->fillField(['id' => 'customer_address_address_addr02'], $value);

        return $this;
    }

    public function 入力_電話番号($value)
    {
        $this->tester->fillField(['id' => 'customer_address_phone_number'], $value);

        return $this;
    }

    public function 登録する()
    {
        $this->tester->click('div.ec-RegisterRole__actions button');
    }
}

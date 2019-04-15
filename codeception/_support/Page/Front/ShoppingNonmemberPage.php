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

class ShoppingNonmemberPage extends AbstractFrontPage
{
    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('お客様情報の入力', 'div.ec-pageHeader h1');

        return $page;
    }

    public function 入力_姓($value)
    {
        $this->tester->fillField(['id' => 'nonmember_name_name01'], $value);

        return $this;
    }

    public function 入力_名($value)
    {
        $this->tester->fillField(['id' => 'nonmember_name_name02'], $value);

        return $this;
    }

    public function 入力_セイ($value)
    {
        $this->tester->fillField(['id' => 'nonmember_kana_kana01'], $value);

        return $this;
    }

    public function 入力_メイ($value)
    {
        $this->tester->fillField(['id' => 'nonmember_kana_kana02'], $value);

        return $this;
    }

    public function 入力_郵便番号($value)
    {
        $this->tester->fillField(['id' => 'nonmember_postal_code'], $value);

        return $this;
    }

    public function 入力_都道府県($value)
    {
        $this->tester->selectOption(['id' => 'nonmember_address_pref'], $value);

        return $this;
    }

    public function 入力_市区町村名($value)
    {
        $this->tester->fillField(['id' => 'nonmember_address_addr01'], $value);

        return $this;
    }

    public function 入力_番地_ビル名($value)
    {
        $this->tester->fillField(['id' => 'nonmember_address_addr02'], $value);

        return $this;
    }

    public function 入力_Eメール($value)
    {
        $this->tester->fillField(['id' => 'nonmember_email_first'], $value);

        return $this;
    }

    public function 入力_Eメール確認($value)
    {
        $this->tester->fillField(['id' => 'nonmember_email_second'], $value);

        return $this;
    }

    public function 入力_電話番号($value)
    {
        $this->tester->fillField(['id' => 'nonmember_phone_number'], $value);

        return $this;
    }

    public function 次へ()
    {
        $this->tester->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');

        return $this;
    }
}

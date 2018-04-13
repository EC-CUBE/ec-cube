<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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

    public function 入力_郵便番号1($value)
    {
        $this->tester->fillField(['id' => 'nonmember_zip_zip01'], $value);
        return $this;
    }

    public function 入力_郵便番号2($value)
    {
        $this->tester->fillField(['id' => 'nonmember_zip_zip02'], $value);
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

    public function 入力_電話番号1($value)
    {
        $this->tester->fillField(['id' => 'nonmember_tel_tel01'], $value);
        return $this;
    }

    public function 入力_電話番号2($value)
    {
        $this->tester->fillField(['id' => 'nonmember_tel_tel02'], $value);
        return $this;
    }

    public function 入力_電話番号3($value)
    {
        $this->tester->fillField(['id' => 'nonmember_tel_tel03'], $value);
        return $this;
    }

    public function 次へ()
    {
        $this->tester->click('div.ec-RegisterRole__actions button.ec-blockBtn--action');
        return $this;
    }
}
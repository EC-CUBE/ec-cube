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


class EntryPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        $page->goPage('/entry');

        return $page;
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->tester->see('新規会員登録', ['css' => 'div.ec-pageHeader > h1']);

        return $page;
    }

    public function 入力_姓($value)
    {
        $this->tester->fillField(['id' => 'entry_name_name01'], $value);
        return $this;
    }

    public function 入力_名($value)
    {
        $this->tester->fillField(['id' => 'entry_name_name02'], $value);
        return $this;
    }

    public function 入力_姓カナ($value)
    {
        $this->tester->fillField(['id' => 'entry_kana_kana01'], $value);
        return $this;
    }

    public function 入力_名カナ($value)
    {
        $this->tester->fillField(['id' => 'entry_kana_kana02'], $value);
        return $this;
    }

    public function 入力_郵便番号($value)
    {
        $this->tester->fillField(['id' => 'entry_postal_code'], $value);
        return $this;
    }

    public function 入力_都道府県($value)
    {
        $this->tester->selectOption(['id' => 'entry_address_pref'], $value);
        return $this;
    }

    public function 入力_市区町村($value)
    {
        $this->tester->fillField(['id' => 'entry_address_addr01'], $value);
        return $this;
    }

    public function 入力_住所($value)
    {
        $this->tester->fillField(['id' => 'entry_address_addr02'], $value);
        return $this;
    }

    public function 入力_電話番号($value) {
        $this->tester->fillField(['id' => 'entry_phone_number'], $value);
        return $this;
    }

    public function 入力_メールアドレス($value)
    {
        $this->tester->fillField(['id' => 'entry_email_first'], $value);
        return $this;
    }

    public function 入力_メールアドレス確認($value)
    {
        $this->tester->fillField(['id' => 'entry_email_second'], $value);
        return $this;
    }

    public function 入力_パスワード($value)
    {
        $this->tester->fillField(['id' => 'entry_password_first'], $value);
        return $this;
    }

    public function 入力_パスワード確認($value)
    {
        $this->tester->fillField(['id' => 'entry_password_second'], $value);
        return $this;
    }

    public function 入力_職業($value)
    {
        $this->tester->selectOption(['id' => 'entry_job'], $value);
        return $this;
    }

    public function 入力_利用規約同意()
    {
        $this->tester->checkOption(['id' => 'entry_user_policy_check']);
        return $this;
    }

    public function 同意して登録()
    {
        $this->tester->click(['css' => 'form > div.ec-registerRole__actions button']);
    }
}

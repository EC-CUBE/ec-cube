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


class EntryConfirmPage extends AbstractFrontPage
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
        $page->tester->see('新規会員登録(確認)', ['css' => 'div.ec-pageHeader > h1']);

        return $page;
    }

    public function お名前()
    {
        return $this->tester->grabTextFrom(['css' => '.ec-registerRole form .ec-borderedDefs dl:nth-child(1) dd']);
    }

    public function お名前カナ()
    {
        return $this->tester->grabTextFrom(['css' => '.ec-registerRole form .ec-borderedDefs dl:nth-child(2) dd']);
    }

    public function 住所()
    {
        return $this->tester->grabTextFrom(['css' => '.ec-registerRole form .ec-borderedDefs dl:nth-child(4) dd']);
    }

    public function 電話番号()
    {
        return $this->tester->grabTextFrom(['css' => '.ec-registerRole form .ec-borderedDefs dl:nth-child(5) dd']);
    }

    public function メールアドレス()
    {
        return $this->tester->grabTextFrom(['css' => '.ec-registerRole form .ec-borderedDefs dl:nth-child(6) dd']);
    }

    public function 職業()
    {
        return $this->tester->grabTextFrom(['css' => '.ec-registerRole form .ec-borderedDefs dl:nth-child(10) dd']);
    }

    public function 会員登録をする()
    {
        $this->tester->click(['css' => 'form > div.ec-registerRole__actions button.ec-blockBtn--action']);
    }

    public function 戻る()
    {
        $this->tester->click('.ec-registerRole form button.ec-blockBtn--cancel');
    }
}

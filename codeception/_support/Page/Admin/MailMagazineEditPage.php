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

class MailMagazineEditPage extends AbstractAdminPageStyleGuide
{
    public static function at(\AcceptanceTester $I)
    {
        $page = new MailMagazineEditPage($I);
        $page->atPage('配信メルマガ管理');
        return $page;
    }

    public function 入力_件名($value)
    {
        $this->tester->fillField(['id' => 'mail_magazine_subject'], $value);
        return $this;
    }

    public function 入力_本文テキスト($value)
    {
        $this->tester->fillField(['id' => 'mail_magazine_body'], $value);
        return $this;
    }

    public function 入力_本文HTML($value)
    {
        $this->tester->fillField(['id' => 'mail_magazine_htmlBody'], $value);
        return $this;
    }

    public function 確認画面へ()
    {
        $this->tester->click(['css' => '#form1 > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button']);
        return $this;
    }

    public function テスト配信する()
    {
        $this->tester->click(['css' => '#form1 > div.c-conversionArea > div > div > div:nth-child(2) > div > div:nth-child(1) > a']);
        $this->tester->waitForElementVisible(['id' => 'sendTestMail']);
        $this->tester->click(['id' => 'sendTestMail']);
        $this->tester->wait(3);
        $this->tester->acceptPopup();
        $this->tester->wait(3);
        $this->tester->acceptPopup();
        return $this;
    }

    public function 配信()
    {
        $this->tester->click(['id' => 'sendMailMagazine']);
        $this->tester->wait(3);
        $this->tester->acceptPopup();
        $this->tester->waitForElementVisible(['css' => '#sendModal > div > div > div.modal-footer > button']);
        $this->tester->click(['css' => '#sendModal > div > div > div.modal-footer > button']);
        return $this;
    }

    public function 配信履歴()
    {

    }
}

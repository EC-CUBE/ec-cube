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

class MailMagazineTemplateEditPage extends AbstractAdminPageStyleGuide
{
    public static function at(\AcceptanceTester $I)
    {
        $page = new MailMagazineTemplateEditPage($I);
        $page->tester->see('テンプレート設定', 'body > div > div.c-contentsArea > div.c-pageTitle > div > h2');
        return $page;
    }

    public function 入力_件名($value)
    {
        $this->tester->fillField(['id' => 'mail_magazine_template_edit_subject'], $value);
        return $this;
    }

    public function 入力_本文テキスト($value)
    {
        $this->tester->fillField(['id' => 'mail_magazine_template_edit_body'], $value);
        return $this;
    }

    public function 入力_本文HTML($value)
    {
        $this->tester->fillField(['id' => 'mail_magazine_template_edit_htmlBody'], $value);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click(['css' => '#content_page_form > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button']);
    }
}

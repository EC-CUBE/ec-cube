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

class MailMagazineTemplatePage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/mail_magazine/template', 'テンプレート設定');
        return $page;
    }

    public static function at(\AcceptanceTester $I)
    {
        $page = new MailMagazineTemplatePage($I);
        $page->tester->see('テンプレート設定', '#page_plugin_mail_magazine_template > div > div.c-contentsArea > div.c-pageTitle > div > h2');
        return $page;
    }

    public function 新規作成()
    {
        $this->tester->click(['css' => '#page_plugin_mail_magazine_template > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > div.row.justify-content-md-center.mb-4 > div > a']);
        return $this;
    }

    public function 編集($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_plugin_mail_magazine_template\"]/div//table/tbody/tr[${i}]/td[3]/a"]);
        return $this;
    }

    public function 削除($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_plugin_mail_magazine_template\"]/div//table/tbody/tr[${i}]/td[4]/a"]);
        $this->tester->waitForElementVisible(['xpath' => "//*[@id=\"page_plugin_mail_magazine_template\"]/div//table/tbody/tr[${i}]/td[4]//button[contains(@class, 'btn-ec-delete')]"]);
        $this->tester->click(['xpath' => "//*[@id=\"page_plugin_mail_magazine_template\"]/div//table/tbody/tr[${i}]/td[4]//button[contains(@class, 'btn-ec-delete')]"]);
        return $this;
    }

    public function プレビュー($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_plugin_mail_magazine_template\"]/div//table/tbody/tr[${i}]/td[5]/a"]);
        return $this;
    }
}

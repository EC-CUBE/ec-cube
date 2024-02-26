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

class MailMagazineHistoryPage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/mail_magazine/history', '配信履歴メルマガ管理');
        return $page;
    }

    public function プレビュー($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"form1\"]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[8]/div/div[1]/a"]);
        return $this;
    }

    public function 配信条件($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"form1\"]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[8]/div/div[2]/a"]);
        return $this;
    }

    public function 配信結果($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"form1\"]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[8]/div/div[3]/a"]);
    }

    public function 削除($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"form1\"]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[8]/div/div[4]/a"]);
        $this->tester->waitForElementVisible(['xpath' => "//*[@id=\"form1\"]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[8]/div/div[4]//button[contains(@class, 'btn-ec-delete')]"]);
        $this->tester->click(['xpath' => "//*[@id=\"form1\"]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[8]/div/div[4]//button[contains(@class, 'btn-ec-delete')]"]);
        return $this;
    }
}

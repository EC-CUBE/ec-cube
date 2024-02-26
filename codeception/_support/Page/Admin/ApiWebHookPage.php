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

class ApiWebHookPage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/api/webhook', 'WebHook管理API管理');
        return $page;
    }

    public function 新規登録()
    {
        $this->tester->click(['css' => '#page_admin_api_webhook > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div > div > div > div > div:nth-child(2) > a']);
        return $this;
    }

    public function 編集($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_admin_api_webhook\"]/div[1]/div[3]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[3]//a[1]"]);
        return $this;
    }

    public function 削除($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_admin_api_webhook\"]/div[1]/div[3]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[3]/div/div[2]//a[contains(@class, 'action-delete')]"]);
        $this->tester->waitForElementVisible(['xpath' => "//*[@id=\"page_admin_api_webhook\"]/div[1]/div[3]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[3]/div/div[2]//a[contains(@class, 'btn-ec-delete')]"]);
        $this->tester->click(['xpath' => "//*[@id=\"page_admin_api_webhook\"]/div[1]/div[3]/div[2]/div/div/div[2]/div/table/tbody/tr[${i}]/td[3]/div/div[2]//a[contains(@class, 'btn-ec-delete')]"]);
        return $this;
    }
}

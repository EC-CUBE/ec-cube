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

class ApiOauthPage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/api/oauth', 'OAuth管理API管理');
        return $page;
    }

    public function 新規登録()
    {
        $this->tester->click(['css' => '#create-client > a']);
        return $this;
    }

    public function 削除($i)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_admin_api_oauth\"]/div[1]//table/tbody/tr[${i}]/td[6]/div/div/div[1]/a"]);
        $this->tester->waitForElementVisible(['xpath' => "//*[@id=\"page_admin_api_oauth\"]/div[1]//table/tbody/tr[${i}]/td[6]//a[contains(@class, 'btn-ec-delete')]"]);
        $this->tester->click(['xpath' => "//*[@id=\"page_admin_api_oauth\"]/div[1]//table/tbody/tr[${i}]/td[6]//a[contains(@class, 'btn-ec-delete')]"]);
        return $this;
    }

    public function 期限切れトークン削除()
    {
        $this->tester->click(['css' => '#page_admin_api_oauth > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > div:nth-child(4) > a']);
        return $this;
    }
}

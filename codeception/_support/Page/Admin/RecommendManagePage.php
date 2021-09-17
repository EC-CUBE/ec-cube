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

class RecommendManagePage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/recommend', 'おすすめ商品管理');
        return $page;
    }

    public static function at(\AcceptanceTester $I)
    {
        $page = new RecommendManagePage($I);
        $page->tester->see('おすすめ商品内容設定', '#page_plugin_recommend_list > div > div.c-contentsArea > div.c-pageTitle > div > span');
        return $page;
    }

    public function 新規登録()
    {
        $this->tester->click(['css' => '#page_plugin_recommend_list > div > div.c-contentsArea > div.c-contentsArea__cols a.btn-ec-regular']);
        return $this;
    }

    public function 編集($i)
    {
        $this->tester->click(['xpath' => "//*[@id='page_plugin_recommend_list']//li[contains(@class, 'sortable-item')][${i}]//a[1]"]);
        return $this;
    }

    public function 削除($i)
    {
        $this->tester->click(['xpath' => "//*[@id='page_plugin_recommend_list']//li[contains(@class, 'sortable-item')][${i}]//a[2]"]);
        $this->tester->waitForElementVisible(['xpath' => "//*[@id='page_plugin_recommend_list']//li[contains(@class, 'sortable-item')][${i}]//div[contains(@class, 'modal')]//a"]);
        $this->tester->click(['xpath' => "//*[@id='page_plugin_recommend_list']//li[contains(@class, 'sortable-item')][${i}]//div[contains(@class, 'modal')]//a"]);
        return $this;
    }
}

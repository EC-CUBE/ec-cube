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

class LayoutManagePage extends AbstractAdminPageStyleGuide
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/content/layout', 'レイアウト管理コンテンツ管理');
    }

    public function レイアウト編集($layoutName)
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_admin_content_layout\"]/div[1]/div[3]/div[2]/div/div//div/a[translate(text(), ' \r\n', '')='${layoutName}']"]);
    }

    public function 削除($layoutName)
    {
        $this->tester->getScenario()->incomplete('未実装：レイアウトの削除は未実装');

        $this->tester->click(['xpath' => "//div[@id='sortable_list_box__list']//div[@class='item_pattern td'][translate(text(), ' \r\n', '')='${layoutName}']/parent::node()/div[@class='icon_edit td']/div/span"]);
        $this->tester->click(['xpath' => "//div[@id='sortable_list_box__list']//div[@class='item_pattern td'][translate(text(), ' \r\n', '')='${layoutName}']/parent::node()/div[@class='icon_edit td']/div/ul/li[2]/span"]);
    }

    public function 新規登録()
    {
        $this->tester->click(['xpath' => "//*[@id=\"page_admin_content_layout\"]/div[1]/div[3]/div[2]/div/div/div[1]/a"]);
    }
}

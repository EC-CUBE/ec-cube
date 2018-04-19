<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
        $this->tester->click(['xpath' => "//*[@id='pills-pc']//div[a]/a[translate(text(), ' \r\n', '')='${layoutName}']"]);
    }

    public function 削除($layoutName)
    {
        $this->tester->getScenario()->incomplete('未実装：レイアウトの削除は未実装');

        $this->tester->click(['xpath' => "//div[@id='sortable_list_box__list']//div[@class='item_pattern td'][translate(text(), ' \r\n', '')='${layoutName}']/parent::node()/div[@class='icon_edit td']/div/a"]);
        $this->tester->click(['xpath' => "//div[@id='sortable_list_box__list']//div[@class='item_pattern td'][translate(text(), ' \r\n', '')='${layoutName}']/parent::node()/div[@class='icon_edit td']/div/ul/li[2]/a"]);
    }
}
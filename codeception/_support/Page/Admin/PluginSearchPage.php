<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
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


class PluginSearchPage extends AbstractAdminPageStyleGuide
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/store/plugin/api/search', 'プラグインを探すオーナーズストア');
    }

    /**
     * @param $pluginCode
     * @return PluginInstallPage
     */
    public function 入手する($pluginCode)
    {
        $this->tester->click(['xpath' => '//*[@id="plugin-list"]//a[@data-code="'.$pluginCode.'"]/parent::node()/parent::node()/div[3]/form/a[contains(text(), "入手する")]']);
        return PluginInstallPage::at($this->tester);
    }
}
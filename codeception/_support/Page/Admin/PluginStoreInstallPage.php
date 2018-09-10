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


class PluginStoreInstallPage extends AbstractAdminPageStyleGuide
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('インストール確認 オーナーズストア');
    }

    /**
     * @return PluginManagePage
     * @throws \Exception
     */
    public function インストール()
    {
        $this->tester->click(['css' => '#plugin-list > div.card-body > div:nth-child(2) > div > button.btn.btn-primary']);
        $this->tester->waitForElementVisible(['id' => 'installBtn']);
        $this->tester->click(['id' => 'installBtn']);
        $this->tester->waitForElementVisible(['css' => '#installModal > div > div > div.modal-footer > a'], 30);
        $this->tester->click(['css' => '#installModal > div > div > div.modal-footer > a']);
        return PluginManagePage::at($this->tester);
    }
}
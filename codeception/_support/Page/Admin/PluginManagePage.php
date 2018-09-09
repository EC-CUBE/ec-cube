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


class PluginManagePage extends AbstractAdminPageStyleGuide
{
    const 完了メーッセージ = '#page_admin_store_plugin > div.c-container > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('インストールプラグイン一覧オーナーズストア');
    }

    /**
     * @param $pluginCode
     * @return PluginManagePage
     */
    public function ストアプラグイン_有効化($pluginCode) {
        return $this->ストアプラグイン_ボタンクリック($pluginCode, '有効化');
    }

    /**
     * @param $pluginCode
     * @return PluginManagePage
     */
    public function ストアプラグイン_無効化($pluginCode) {
        return $this->ストアプラグイン_ボタンクリック($pluginCode, '無効化');
    }

    /**
     * @param $pluginCode
     * @return PluginManagePage
     * @throws \Exception
     */
    public function ストアプラグイン_削除($pluginCode) {
        $this->ストアプラグイン_ボタンクリック($pluginCode, '削除');
        $this->tester->waitForElementVisible(['id' => 'officialPluginDeleteButton']);
        $this->tester->click(['id' => 'officialPluginDeleteButton']);
        $this->tester->waitForElementVisible(['css' => '#officialPluginDeleteModal > div > div > div.modal-footer > button:nth-child(3)'], 30);
        $this->tester->click(['css' => '#officialPluginDeleteModal > div > div > div.modal-footer > button:nth-child(3)']);
        return $this;
    }

    private function ストアプラグイン_ボタンクリック($pluginCode, $label)
    {
        $xpath = ['xpath' => '//*[@id="page_admin_store_plugin"]//table[1]/tbody//td[3]/p[contains(text(), "'.$pluginCode.'")]/../../td[6]//i[@data-original-title="'.$label.'"]/parent::node()'];
        $this->tester->click($xpath);
        return $this;
    }
}
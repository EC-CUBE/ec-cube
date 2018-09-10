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


class PluginLocalInstallPage extends AbstractAdminPageStyleGuide
{
    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/store/plugin/install', '独自プラグインのアップロードオーナーズストア');
    }

    /**
     * @param $fileName
     * @return PluginManagePage
     */
    public function アップロード($fileName)
    {
        $this->tester->attachFile(['id' => 'plugin_local_install_plugin_archive'], $fileName);
        $this->tester->click(['css' => '#upload-form > div > div > div > div > div.card-body > div > div > button']);
        return PluginManagePage::at($this->tester);
    }
}
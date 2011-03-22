<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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

// load plugins
/* -----------------------------------------------------------------------------
 * TODO PHP4 でも使えるように, XML パーサーをファクトリークラスで実装する
 * ----------------------------------------------------------------------------*/
define('DEBUG_LOAD_PLUGIN', true);

if (version_compare("5", PHP_VERSION, "<")) {
    $pluginsXml = SC_Utils_Ex::sfGetPluginsXml();
    foreach ($pluginsXml->plugin as $plugin) {
        $requireFile = PLUGIN_REALDIR . "{$plugin->path}/require.php";
        if (file_exists($requireFile)) {
            include_once $requireFile;
        }
    }

    // Smarty に引き渡す目的
    // FIXME スーパーグローバルを書き換える以外の方法に改める。(グローバル変数にセットして、Smrty 関数で読み出すなど)
    $_ENV['pluginsXml'] = $pluginsXml;

    // グローバル変数を掃除
    unset($plugin);
    unset($pluginsXml);
}
?>

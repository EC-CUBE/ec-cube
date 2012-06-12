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

require DATA_REALDIR . 'module/Locale/streams.php';
require DATA_REALDIR . 'module/Locale/gettext.php';

/**
 * ローカリゼーション関係のヘルパークラス.
 * 主に static 参照する関数群.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_Locale {

    // }}}
    // {{{ functions

    /**
     * メッセージの一覧から、エイリアスに対応する文字列を返す.
     *
     * @param string $string メッセージエイリアス
     * @param string $lang_code 言語コード
     * @param integer $device_type_id 端末種別ID
     * @return string エイリアスに対応する文字列
     */
    function get_locale($string, $lang_code = LANG_CODE, $device_type_id = DEVICE_TYPE_PC) {
        // 指定言語の文字列一覧を取得する
        $translations = SC_Helper_Locale_Ex::get_translations($lang_code, $device_type_id);
        // エイリアスに対応する文字列が存在するかどうか
        if (isset($translations[$string])) {
            return $translations[$string];
        }
        else {
            return $string;
        }
    }

    /**
     * 指定言語の文字列一覧を取得する
     *
     * @param string $lang_code 言語コード
     * @param integer $device_type_id 端末種別ID
     * @return array 文字列一覧
     */
    function get_translations($lang_code = LANG_CODE, $device_type_id = DEVICE_TYPE_PC) {
        static $translations;

        // 指定言語の文字列がまだ読み込まれていない場合
        if (empty($translations[$lang_code][$device_type_id])) {
            $translations[$lang_code][$device_type_id] = array();

            // 読み込むロケールファイルの一覧を取得
            $file_list = SC_Helper_Locale_Ex::get_locale_file_list($lang_code, $device_type_id);

            // ロケールファイル毎に、php_gettextを利用して文字列を取得する
            foreach ($file_list as $locale_file) {
                $stream = new FileReader($locale_file);
                $gettext = new gettext_reader($stream);

                $gettext->load_tables();
                $translations[$lang_code][$device_type_id] = array_merge($translations[$lang_code][$device_type_id], $gettext->cache_translations);
            }
        }

        return $translations[$lang_code][$device_type_id];
    }

    /**
     * ロケールファイルの一覧を取得する
     *
     * @param string $lang_code 言語コード
     * @param integer $device_type_id 端末種別ID
     * @return array ファイル一覧
     */
    function get_locale_file_list($lang_code = LANG_CODE, $device_type_id = DEVICE_TYPE_PC) {
        $file_list = array();

        // コアのロケールファイルのパスを作成
        $core_locale_path = DATA_REALDIR . "locales/$lang_code.mo";
        // 指定言語のロケールファイルが存在すればファイル一覧に追加
        if (file_exists($core_locale_path)) {
            $file_list[] = $core_locale_path;
        }

        // 有効なプラグインを取得
        $arrPluginDataList = SC_Plugin_Util_Ex::getEnablePlugin();
        // pluginディレクトリを取得
        $arrPluginDirectory = SC_Plugin_Util_Ex::getPluginDirectory();
        foreach ($arrPluginDataList as $arrPluginData) {
            // プラグイン本体ファイル名が取得したプラグインディレクトリ一覧にある事を確認
            if (array_search($arrPluginData['plugin_code'], $arrPluginDirectory) !== false) {
                // プラグインのロケールファイルのパスを作成
                $plugin_locale_path = PLUGIN_UPLOAD_REALDIR . $arrPluginData['plugin_code'] . "/locales/$lang_code.mo";
                // 指定言語のロケールファイルが存在すればファイル一覧に追加
                if (file_exists($plugin_locale_path)) {
                    $file_list[] = $plugin_locale_path;
                }
            }
        }

        // テンプレートのロケールファイルのパスを作成
        $template_locale_path = HTML_REALDIR . SC_Helper_PageLayout_Ex::getUserDir($device_type_id, true) . "locales/$lang_code.mo";
        // 指定言語のロケールファイルが存在すればファイル一覧に追加
        if (file_exists($template_locale_path)) {
            $file_list[] = $template_locale_path;
        }

        return $file_list;
    }

    /**
     * 適切な複数形の書式を判定する
     *
     * @param integer $count 数値
     * @param string $lang_code 言語コード
     * @return integer インデックス
     */
    function get_plural_index($count, $lang_code = LANG_CODE) {
        // 計算式を取得
        $string = SC_Helper_Locale_Ex::get_plural_forms($lang_code);
        $string = str_replace('nplurals', "\$total", $string);
        $string = str_replace("n", $count, $string);
        $string = str_replace('plural', "\$plural", $string);

        $total = 0;
        $plural = 0;

        eval("$string");
        if ($plural >= $total) $plural = $total - 1;

        return $plural;
    }

    /**
     * 適切な複数形の書式判定に使用する計算式を取得する
     *
     * @param string $lang_code 言語コード
     * @return string 計算式
     */
    function get_plural_forms($lang_code = LANG_CODE) {
        static $plural_forms;

        // 書式が読みこまれていない場合はファイルから一覧を読み込む
        if(empty($plural_forms)){
            $plural_forms = @include_once DATA_REALDIR . "include/plural_forms.inc";
        }

        // 書式が無い場合は、日本語用のものをデフォルトで返す
        return (isset($plural_forms[$lang_code])) ? $plural_forms[$lang_code] : "nplurals=2; plural=(n!=1);";
    }
}

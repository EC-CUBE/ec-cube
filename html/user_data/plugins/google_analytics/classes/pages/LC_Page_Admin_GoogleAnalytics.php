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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * Google Analytics プラグインの管理画面を制御するクラス.
 *
 * @package Page
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class LC_Page_Admin_GoogleAnalytics extends LC_Page_Ex {

    /** プラグイン情報配列 (呼び出し元でセットする) */
    var $arrPluginInfo;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = $this->arrPluginInfo['fullpath'] . 'tpl/admin/index.tpl';
        $this->tpl_mainno   = 'plugin';
        $this->tpl_subno    = $this->arrPluginInfo['path'];
        $this->tpl_subtitle = "プラグイン「{$this->arrPluginInfo['name']}」の設定";

        if (empty($_POST['mode'])) {
            $_POST['mode'] = '';
        }

        if (empty($_GET['mode'])) {
            $_GET['mode'] = '';
        }
    }

    /**
     * Page のプロセス.
     *
     * POST パラメーター 'mode' が register の場合は登録処理を行う.
     * 登録処理の後, 自ページをリロードし, GET パラメーター 'mode' を付与する.
     * 登録に成功し, GET パラメーター 'mode' の値が success の場合は
     * 「登録に成功しました」というメッセージをポップアップで表示する.
     * 登録に失敗し, GET パラメーター 'mode' の値が failure の場合は
     * 「登録に失敗しました」というメッセージをポップアップで表示する.
     *
     * TODO Transaction Token を使用する
     *
     * @return void
     */
    function process() {
        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess(new SC_Session());

        switch ($_POST['mode']) {
            case 'register':
                if ($this->register($_POST['ga_ua'])) {
                    SC_Response_Ex::reload(array('mode' => 'success'), true);
                    exit;
                } else {
                    SC_Response_Ex::reload(array('mode' => 'failure'), true);
                    exit;
                }
                break;

            default:
                break;
        }

        switch ($_GET['mode']) {
            case 'success':
                $this->tpl_onload .= "window.alert('登録に成功しました。');";
                break;

            case 'failure':
                $this->tpl_onload .= "window.alert('登録に失敗しました。');";
                break;

            default:
                break;
        }

        $objView = new SC_AdminView_Ex();
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

    /**
     * UA の登録を行う.
     *
     * classes/pages/ga_config.php を読み込み, ウェブプロパティID の文字列
     * を定数として書き出す.
     *
     * @param string ウェブプロパティID の文字列
     * @return boolean 登録に成功した場合 true; 失敗した場合 false;
     */
    function register($ua) {
        $data = "<?php\ndefine('GA_UA', '" 
            . htmlspecialchars($ua, ENT_QUOTES) . "');\n?>\n";

        $configFile = $this->arrPluginInfo['fullpath'] . 'classes/pages/ga_config.php';
        $handle = fopen($configFile, 'w');
        if (!$handle) {
            return false;
        }
        // ファイルの内容を書き出す.
        if (fwrite($handle, $data) === false) {
            return false;
        }
        return true;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}

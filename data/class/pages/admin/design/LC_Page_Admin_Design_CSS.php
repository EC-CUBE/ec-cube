<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * CSS設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_CSS extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/css.tpl';
        $this->tpl_subnavi  = 'design/subnavi.tpl';
        $this->area_row = 30;
        $this->tpl_subno = "css";
        $this->tpl_mainno = "design";
        $this->tpl_subtitle = 'CSS編集';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        $css_path = USER_PATH . "css/common.css";

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // データ更新処理
        if ($_POST['mode'] == 'confirm'){
            // プレビュー用テンプレートに書き込み
            $fp = fopen($css_path,"w"); // TODO
            fwrite($fp, $_POST['css']);
            fclose($fp);

            $this->tpl_onload="alert('登録が完了しました。');";
        }

        // CSSファイルの読み込み
        if(file_exists($css_path)){
            $css_data = file_get_contents($css_path);
        }

        // テキストエリアに表示
        $this->css_data = $css_data;

        // 画面の表示
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
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
?>

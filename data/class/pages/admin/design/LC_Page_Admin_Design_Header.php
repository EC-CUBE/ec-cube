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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * ヘッダ, フッタ編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Header extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/header.tpl';
        $this->tpl_subnavi  = 'design/subnavi.tpl';
        $this->header_row = 13;
        $this->footer_row = 13;
        $this->tpl_subno = "header";
        $this->tpl_mainno = "design";
        $this->tpl_subtitle = 'ヘッダー/フッター設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * FIXME テンプレートの取得方法を要修正
     *
     * @return void
     */
    function action() {
        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        $this->objLayout = new SC_Helper_PageLayout_Ex();

        // 端末種別IDを取得
        if (isset($_REQUEST['device_type_id'])
            && is_numeric($_REQUEST['device_type_id'])) {
            $device_type_id = $_REQUEST['device_type_id'];
        } else {
            $device_type_id = DEVICE_TYPE_PC;
        }

        $division = isset($_POST['division']) ? $_POST['division'] : "";
        $pre_DIR = USER_INC_REALDIR . 'preview/';

        // データ更新処理
        if ($division != ''){
            // プレビュー用テンプレートに書き込み
            $fp = fopen($pre_DIR.$division.'.tpl',"w"); // TODO
            fwrite($fp, $_POST[$division]);
            fclose($fp);

            // 登録時はプレビュー用テンプレートをコピーする
            if ($this->getMode() == 'confirm'){
                copy($pre_DIR.$division.".tpl", $this->objLayout->getTemplatePath($device_type_id) . $division . ".tpl");
                // 完了メッセージ（プレビュー時は表示しない）
                $this->tpl_onload="alert('登録が完了しました。');";

                // テキストエリアの幅を元に戻す(処理の統一のため)
                $_POST['header_row'] = "";
                $_POST['footer_row'] = "";
            }else if ($this->getMode() == 'preview'){
                if ($division == "header") $this->header_prev = "on";
                if ($division == "footer") $this->footer_prev = "on";
            }

            // ヘッダーファイルの読み込み(プレビューデータ)
            $header_data = file_get_contents($pre_DIR . "header.tpl");

            // フッターファイルの読み込み(プレビューデータ)
            $footer_data = file_get_contents($pre_DIR . "footer.tpl");
        }else{
            // postでデータが渡されなければ新規読み込みと判断をし、プレビュー用データを正規のデータで上書きする
            if (!is_dir($pre_DIR)) {
                mkdir($pre_DIR);
            }

            // ユーザーパスにテンプレートが存在しなければ,
            // 指定テンプレートから読み込む
            $header_tpl = $this->objLayout->getTemplatePath($device_type_id) . "header.tpl";
            $footer_tpl = $this->objLayout->getTemplatePath($device_type_id) . "footer.tpl";

            copy($header_tpl, $pre_DIR . "header.tpl");
            copy($footer_tpl, $pre_DIR . "footer.tpl");

            // ヘッダーファイルの読み込み
            $header_data = file_get_contents($header_tpl);
            // フッターファイルの読み込み
            $footer_data = file_get_contents($footer_tpl);
        }

        // テキストエリアに表示
        $this->header_data = $header_data;
        $this->footer_data = $footer_data;
        $this->device_type_id = $device_type_id;

        if (isset($_POST['header_row']) && $_POST['header_row'] != ''){
            $this->header_row = $_POST['header_row'];
        }

        if (isset($_POST['footer_row']) && $_POST['footer_row'] != ''){
            $this->footer_row = $_POST['footer_row'];
        }

        // ブラウザタイプ
        $this->browser_type =
            isset($_POST['browser_type']) ? $_POST['browser_type'] : "";
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

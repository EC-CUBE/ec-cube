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
require_once(CLASS_EX_REALDIR . "helper_extends/SC_Helper_FileManager_Ex.php");

/**
 * CSS設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_CSS extends LC_Page_Admin {

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
        $this->tpl_subtitle = 'CSS設定';
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
     * FIXME css ファイルの取得方法を要修正
     *
     * @return void
     */
    function action() {
        $objFileManager = new SC_Helper_FileManager_Ex();
        $this->objLayout = new SC_Helper_PageLayout_Ex();

        // CSSファイル名を取得
        if (isset($_POST['css_name'])) {
            $css_name = $_POST['css_name'];
        }else if (isset($_GET['css_name'])){
            $css_name = $_GET['css_name'];
        }else{
            $css_name = '';
        }
        $this->css_name = $css_name;

        if (isset($_POST['old_css_name'])) {
            $old_css_name = $_POST['old_css_name'];
        }else if (isset($_GET['css_name'])) {
            $old_css_name = $_GET['css_name'];
        }else{
            $old_css_name = '';
        }
        $this->old_css_name = $old_css_name;

        // 端末種別IDを取得
        if (isset($_REQUEST['device_type_id'])
            && is_numeric($_REQUEST['device_type_id'])) {
            $device_type_id = $_REQUEST['device_type_id'];
        } else {
            $device_type_id = DEVICE_TYPE_PC;
        }

        $css_dir = $this->objLayout->getTemplatePath($device_type_id, true) . "css/";
        $css_path = $css_dir . $css_name . '.css';

        // CSSファイルの読み込み
        if($css_name != ''){
            $css_data = $objFileManager->sfReadFile($css_path);
        }
        // テキストエリアに表示
        $this->css_data = $css_data;

        switch($this->getMode()) {
            // データ更新処理
            case 'confirm':
                $this->lfExecuteConfirm($css_dir, $css_name, $old_css_name, $css_path);
                break;
            case 'delete':
                $this->lfExecuteDelete($css_path);
                break;
            default:
                GC_Utils::gfPrintLog("MODEエラー：".$this->getMode());
                break;
        }

        // ファイルリストを取得
        $this->arrCSSList = $this->lfGetCSSList($css_dir);
        $this->device_type_id = $device_type_id;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfExecuteConfirm($css_dir, $css_name, $old_css_name, $css_path) {
        $objFileManager = new SC_Helper_FileManager_Ex();

        // エラーチェック
        $this->arrErr = $this->lfErrorCheck($_POST, $css_dir);

        // エラーがなければ更新処理を行う
        if (count($this->arrErr) == 0) {
            // 旧ファイルの削除
            if ($old_css_name != '' && $old_css_name != $css_name) {
                $objFileManager->sfDeleteDir($css_dir . $old_css_name . '.css');
            }
            // プレビュー用テンプレートに書き込み
            $objFileManager->sfWriteFile($css_path, $_POST['css']);

            $this->tpl_onload="alert('登録が完了しました。');";
            $this->old_css_name = $css_name;
        }
        $this->css_data = $_POST['css'];
    }

    function lfExecuteDelete($css_path) {
        $objFileManager = new SC_Helper_FileManager_Ex();

        // css_name が空でない場合にはdeleteを実行
        if ($_POST['css_name'] !== '') {
            $objFileManager->sfDeleteDir($css_path);
        }
        $this->objDisplay->reload(array(), true);
    }

    /**
     * CSSファイルのリストを取得.
     *
     * @param array $css_dir CSSディレクトリ
     * @return array ファイルリスト
     */
    function lfGetCSSList($css_dir) {
        $objFileManager = new SC_Helper_FileManager_Ex();

        $arrFileList = $objFileManager->sfGetFileList($css_dir);
        foreach ($arrFileList as $key => $val) {
            if (!$val['is_dir']) {
                $arrCSSList[] = array(
                    'file_name' => $val['file_name'],
                    'css_name'  => preg_replace('/(.+)\.(.+?)$/','$1',$val['file_name']),
                );
            }
        }
        return $arrCSSList;
    }

    /**
     * 入力項目のエラーチェックを行う.
     *
     * @param array $arrData 入力データ
     * @param array $css_dir CSSディレクトリ
     * @return array エラー情報
     */
    function lfErrorCheck($array, $css_dir) {
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("CSSファイル名", "css_name", STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK"));

        // 入力文字が英数字,"_","-"以外ならエラーを返す
        if(!isset($objErr->arrErr['css_name'])){
            if(!ereg("^[a-zA-Z0-9_\.-]+$", $array['css_name'])) {
                $objErr->arrErr['css_name'] = '※ CSSファイル名は英数字と"_"および"-"だけを入力してください。<br />';
            }
        }

        // 同一のファイル名が存在している場合にはエラー
        if(!isset($objErr->arrErr['css_name'])){
            $arrCSSList = $this->lfGetCSSList($css_dir);
            foreach ($arrCSSList as $key => $val) {
                if ($val['css_name'] == $array['css_name']) {
                    if ($array['old_css_name'] == '' || $array['old_css_name'] != $array['css_name']) {
                        $errFlg = TRUE;
                    }
                }
            }
            if ($errFlg) $objErr->arrErr['css_name'] = '※ 同じファイル名のデータが存在しています。別の名称を付けてください。<br />';
        }

        return $objErr->arrErr;
    }
}
?>

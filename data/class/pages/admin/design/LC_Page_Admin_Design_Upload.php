<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(DATA_PATH. "module/Tar.php");

/**
 * テンプレートアップロード のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Upload extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/upload.tpl';
        $this->tpl_subnavi = 'design/subnavi.tpl';
        $this->tpl_subno = 'template';
        $this->tpl_subno_template = 'upload';
        $this->tpl_mainno = "design";
        $this->tpl_subtitle = 'アップロード';
        $this->template_name = 'アップロード';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objQuery = new SC_Query();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['template_code'])) $_POST['template_code'] = "";
        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // アップロードしたファイルを格納するディレクトリ
        $new_file_dir = USER_TEMPLATE_PATH . $_POST['template_code'];
        // ファイル管理クラス
        $objUpFile = new SC_UploadFile(TEMPLATE_TEMP_DIR, $new_file_dir);
        // ファイル情報の初期化
        $this->lfInitFile($objUpFile);
        // パラメータ管理クラス
        $objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);

        switch($_POST['mode']) {
        case 'upload':
            $objFormParam->setParam($_POST);
            $arrRet = $objFormParam->getHashArray();

            $this->arrErr = $this->lfErrorCheck($arrRet, $objQuery, $objFormParam);

            // ファイルを一時フォルダへ保存
            $ret = $objUpFile->makeTempFile('template_file', false);
            if($ret != "") {
                $this->arrErr['template_file'] = $ret;
            } else if(count($this->arrErr) <= 0) {
                // フォルダ作成
                $ret = @mkdir($new_file_dir);
                // 一時フォルダから保存ディレクトリへ移動
                $objUpFile->moveTempFile();
                // 解凍
                $this->lfUnpacking($new_file_dir, $_FILES['template_file']['name'], $new_file_dir."/");

                $mess = "";
                // Smarty テンプレートをコピー
                $target_smarty = $new_file_dir . "/Smarty/";
                $mess .= SC_Utils_Ex::sfCopyDir($target_smarty, SMARTY_TEMPLATES_DIR . $_POST['template_code'] . "/", $mess);
                // コピー済みファイルを削除
                SC_Utils_Ex::sfDelFile($target_smarty);
                // DBにテンプレート情報を保存
                $this->lfRegistTemplate($arrRet, $objQuery);
                // 完了表示javascript
                $this->tpl_onload = "alert('テンプレートファイルをアップロードしました。');";
                // フォーム値をクリア
                $objFormParam->setParam(array('template_code' => "", 'template_name' => ""));
            }
            break;
        default:
            break;
        }
        // 画面の表示
        $this->arrForm = $objFormParam->getFormParamList();
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

    /**
     * ファイル情報の初期化を行う.
     *
     * @param SC_UploadFile $objUpFile SC_UploadFile インスタンス
     * @return void
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile("テンプレートファイル", 'template_file', array(), TEMPLATE_SIZE, true, 0, 0, false);
    }

    /**
     * パラメータ情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("テンプレートコード", "template_code", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK"));
        $objFormParam->addParam("テンプレート名", "template_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
    }

    /**
     * エラーチェックを行う.
     *
     * @param array $arrList フォームの値の配列
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラー情報の配列
     */
    function lfErrorCheck($arrList, &$objQuery, &$objFormParam) {

        $objErr = new SC_CheckError($arrList);
        $objErr->arrErr = $objFormParam->checkError();

        if(count($objErr->arrErr) <= 0) {
            // 同名のフォルダが存在する場合はエラー
            if(file_exists(USER_TEMPLATE_PATH.$arrList['template_code'])) {
                $objErr->arrErr['template_code'] = "※ 同名のファイルがすでに存在します。<br/>";
            }
            // DBにすでに登録されていないかチェック
            $ret = $objQuery->get("dtb_templates", "template_code", "template_code = ?", array($arrList['template_code']));
            if($ret != "") {
                $objErr->arrErr['template_code'] = "※ すでに登録されているテンプレートコードです。<br/>";
            }
            // ファイルの拡張子チェック(.tar/tar.gzのみ許可)
            $errFlag = true;
            $array_ext = explode(".", $_FILES['template_file']['name']);
            $ext = $array_ext[ count ( $array_ext ) - 1 ];
            $ext = strtolower($ext);
            // .tarチェック
            if ($ext == 'tar') {
                $errFlag = false;
            }

            $ext = $array_ext[ count ( $array_ext ) - 2 ].".".$ext;
            $ext = strtolower($ext);
            // .tar.gzチェック
            if ($ext== 'tar.gz') {
                $errFlag = false;
            }

            if($errFlag) {
                $objErr->arrErr['template_file'] = "※ アップロードするテンプレートファイルで許可されている形式は、tar/tar.gzです。<br />";
            }
        }

        return $objErr->arrErr;
    }

    /**
     * テンプレートデータを登録する.
     *
     * @param array $arrList 登録するデータの配列
     * @param SC_Query $objQuery SC_Query インスタンス
     * @return void
     */
    function lfRegistTemplate($arrList, &$objQuery) {

        // INSERTする値を作成する。
        $sqlval['template_code'] = $arrList['template_code'];
        $sqlval['template_name'] = $arrList['template_name'];
        $sqlval['create_date'] = "now()";
        $sqlval['update_date'] = "now()";

        $objQuery->insert("dtb_templates", $sqlval);
    }

    /**
     * テンプレートのアーカイブを展開する.
     *
     * @param string ディレクトリ名
     * @param string ファイル名
     * @param string 展開先ディレクトリ
     * @return void
     */
    function lfUnpacking($dir, $file_name, $unpacking_dir) {

        // 圧縮フラグTRUEはgzip解凍をおこなう
        $tar = new Archive_Tar("$dir/$file_name", TRUE);

        // 拡張子を切り取る
        $unpacking_name = ereg_replace("\.tar$", "", $file_name);
        $unpacking_name = ereg_replace("\.tar\.gz$", "", $file_name);

        // 指定されたフォルダ内に解凍する
        $err = $tar->extractModify($unpacking_dir, $unpacking_name);

        // フォルダ削除
        @SC_Utils_Ex::sfDelFile("$dir/$unpacking_name");
        // 圧縮ファイル削除
        @unlink("$dir/$file_name");

        return $err;
    }
}
?>

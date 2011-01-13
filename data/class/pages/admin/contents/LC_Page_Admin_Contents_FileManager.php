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
 * ファイル管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_FileManager extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/file_manager.tpl';
        $this->tpl_mainno = 'contents';
        $this->tpl_subnavi = 'contents/subnavi.tpl';
        $this->tpl_subno = "file";
        $this->tpl_subtitle = 'ファイル管理';

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
     * @return void
     */
    function action() {
        //---- 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ルートディレクトリ
        $top_dir = USER_REALDIR;

        $objView = new SC_AdminView();
        $objQuery = new SC_Query();
        $objFileManager = new SC_Helper_FileManager_Ex();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
		
        // 現在の階層を取得
        if($_POST['mode'] != "") {
            $now_dir = $_POST['now_file'];
        } else {
            // 初期表示はルートディレクトリ(user_data/)を表示
            $now_dir = $top_dir;
        }

        // ファイル管理クラス
        $objUpFile = new SC_UploadFile($now_dir, $now_dir);
        // ファイル情報の初期化
        $this->lfInitFile($objUpFile);

        switch($_POST['mode']) {

            // ファイル表示

        case 'view':
            // エラーチェック

            $arrErr = $this->lfErrorCheck();

            if (empty($arrErr)) {
                // 選択されたファイルがディレクトリなら移動
                if(is_dir($_POST['select_file'])) {
                    $now_dir = $this->lfCheckSelectDir($_POST['select_file']);
                } else {
                    // javascriptで別窓表示(テンプレート側に渡す)
                    // FIXME XSS対策すること
                    $file_url = ereg_replace(USER_REALDIR, "", $_POST['select_file']);
                    $this->tpl_onload = "win02('./file_view.php?file=". $file_url ."', 'user_data', '600', '400');";
                    $now_dir = $this->lfCheckSelectDir(dirname($_POST['select_file']));
                }
            }

            break;
            
            // ファイルダウンロード
        case 'download':

            // エラーチェック
            $arrErr = $this->lfErrorCheck();
            if (empty($arrErr)) {
                if(is_dir($_POST['select_file'])) {
                    // ディレクトリの場合はjavascriptエラー
                    $arrErr['select_file'] = "※ ディレクトリをダウンロードすることは出来ません。<br/>";
                } else {
                    // ファイルダウンロード
                    $objFileManager->sfDownloadFile($_POST['select_file']);
                    exit;
                }
            }
            break;
            // ファイル削除
        case 'delete':
            // エラーチェック
            $arrErr = $this->lfErrorCheck();
            if (empty($arrErr)) {
                $objFileManager->sfDeleteDir($_POST['select_file']);
            }
            break;
            // ファイル作成
        case 'create':
            // エラーチェック
            $arrErr = $this->lfCreateErrorCheck();
            if (empty($arrErr)) {
                $create_dir = ereg_replace("/$", "", $now_dir);
                // ファイル作成
                if(!$objFileManager->sfCreateFile($create_dir."/".$_POST['create_file'], 0755)) {
                    // 作成エラー
                    $arrErr['create_file'] = "※ ".$_POST['create_file']."の作成に失敗しました。<br/>";
                } else {
                    $this->tpl_onload .= "alert('フォルダを作成しました。');";
                }
            }
            break;
            // ファイルアップロード
        case 'upload':
            // 画像保存処理
            $ret = $objUpFile->makeTempFile('upload_file', false);
            if($ret != "") {
                $arrErr['upload_file'] = $ret;
            } else {
                $this->tpl_onload .= "alert('ファイルをアップロードしました。');";
            }
            break;
            // フォルダ移動
        case 'move':
            $now_dir = $this->lfCheckSelectDir($_POST['tree_select_file']);
            break;
            // 初期表示
        default :
            break;
        }
        // トップディレクトリか調査
        $is_top_dir = false;
        // 末尾の/をとる
        $top_dir_check = ereg_replace("/$", "", $top_dir);
        $now_dir_check = ereg_replace("/$", "", $now_dir);
        if($top_dir_check == $now_dir_check) $is_top_dir = true;

        // 現在の階層より一つ上の階層を取得
        $parent_dir = $this->lfGetParentDir($now_dir);

        // 現在のディレクトリ配下のファイル一覧を取得
        $this->arrFileList = $objFileManager->sfGetFileList($now_dir);
        $this->tpl_is_top_dir = $is_top_dir;
        $this->tpl_parent_dir = $parent_dir;
        // TODO JSON で投げて, フロント側で処理した方が良い？
        $this->tpl_now_dir = "";
        $arrNowDir = preg_split('/\//', str_replace(HTML_REALDIR, '', $now_dir));
        for ($i = 0; $i < count($arrNowDir); $i++) {
            if (!empty($arrNowDir)) {
                $this->tpl_now_dir .= $arrNowDir[$i];
                if ($i < count($arrNowDir) - 1) {
                     // フロント側で &gt; へエスケープするため, ここでは > を使用
                    $this->tpl_now_dir .= ' > ';
                }
            }
        }
        $this->tpl_now_file = basename($now_dir);
        $this->arrErr = isset($arrErr) ? $arrErr : "";
        $this->arrParam = $_POST;

        // ツリーを表示する divタグid, ツリー配列変数名, 現在ディレクトリ, 選択ツリーhidden名, ツリー状態hidden名, mode hidden名
        $treeView = "fnTreeView('tree', arrTree, '$now_dir', 'tree_select_file', 'tree_status', 'move');";
        if (!empty($this->tpl_onload)) {
            $this->tpl_onload .= $treeView;
        } else {
            $this->tpl_onload = $treeView;
        }

        // ツリー配列作成用 javascript
        if (!isset($_POST['tree_status'])) $_POST['tree_status'] = "";
        $arrTree = $objFileManager->sfGetFileTree($top_dir, $_POST['tree_status']);
        $this->tpl_javascript .= "arrTree = new Array();\n";
        foreach($arrTree as $arrVal) {
            $this->tpl_javascript .= "arrTree[".$arrVal['count']."] = new Array(".$arrVal['count'].", '".$arrVal['type']."', '".$arrVal['path']."', ".$arrVal['rank'].",";
            if ($arrVal['open']) {
                $this->tpl_javascript .= "true);\n";
            } else {
                $this->tpl_javascript .= "false);\n";
            }
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /*
     * 関数名：lfErrorCheck()
     * 説明　：エラーチェック
     */
    function lfErrorCheck() {
        $objErr = new SC_CheckError($_POST);
        $objErr->doFunc(array("ファイル", "select_file"), array("SELECT_CHECK"));

        return $objErr->arrErr;
    }

    /*
     * 関数名：lfCreateErrorCheck()
     * 説明　：ファイル作成処理エラーチェック
     */
    function lfCreateErrorCheck() {
        $objErr = new SC_CheckError($_POST);
        $objErr->doFunc(array("作成ファイル名", "create_file"), array("EXIST_CHECK", "FILE_NAME_CHECK_BY_NOUPLOAD"));

        return $objErr->arrErr;
    }

    /*
     * 関数名：lfInitFile()
     * 説明　：ファイル情報の初期化
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile("ファイル", 'upload_file', array(), FILE_SIZE, true, 0, 0, false);
    }

    /*
     * 関数名：lfCheckSelectDir()
     * 引数１：ディレクトリ
     * 説明：選択ディレクトリがUSER_REALDIR以下かチェック
     */
    function lfCheckSelectDir($dir) {
        $top_dir = USER_REALDIR;
        // USER_REALDIR以下の場合
            if (preg_match("@^\Q". $top_dir. "\E@", $dir) > 0) {
            // 相対パスがある場合、USER_REALDIRを返す.
            if (preg_match("@\Q..\E@", $dir) > 0) {
                return $top_dir;
            // 相対パスがない場合、そのままディレクトリパスを返す.
            } else {
                return $dir;
            }
        // USER_REALDIR以下でない場合、USER_REALDIRを返す.
        } else {
            return $top_dir;
        }
    }

    /*
     * 関数名：lfGetParentDir()
     * 引数1 ：ディレクトリ
     * 説明　：親ディレクトリ取得
     */
    function lfGetParentDir($dir) {
        $dir = ereg_replace("/$", "", $dir);
        $arrDir = split('/', $dir);
        array_pop($arrDir);
        $parent_dir = "";
        foreach($arrDir as $val) {
            $parent_dir .= "$val/";
        }
        $parent_dir = ereg_replace("/$", "", $parent_dir);

        return $parent_dir;
    }
}
?>

<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ファイル管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_FileManager extends LC_Page {

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
        //---- 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ルートディレクトリ
        $top_dir = USER_PATH;

        $objView = new SC_AdminView();
        $objQuery = new SC_Query();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        $tpl_onload = "";

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
            if(!is_array($arrErr)) {

                // 選択されたファイルがディレクトリなら移動
                if(is_dir($_POST['select_file'])) {
                    ///$now_dir = $_POST['select_file'];
                    // ツリー遷移用のjavascriptを埋め込む
                    $arrErr['select_file'] = "※ ディレクトリを表示することは出来ません。<br/>";

                } else {
                    // javascriptで別窓表示(テンプレート側に渡す)
                    // FIXME
                    $file_url = ereg_replace(USER_PATH, "", $_POST['select_file']);
                    $tpl_onload = "win02('./file_view.php?file=". $file_url ."', 'user_data', '600', '400');";
                }
            }
            break;
            // ファイルダウンロード
        case 'download':

            // エラーチェック
            $arrErr = $this->lfErrorCheck();
            if(!is_array($arrErr)) {
                if(is_dir($_POST['select_file'])) {
                    // ディレクトリの場合はjavascriptエラー
                    $arrErr['select_file'] = "※ ディレクトリをダウンロードすることは出来ません。<br/>";
                } else {
                    // ファイルダウンロード
                    $this->sfDownloadFile($_POST['select_file']);
                    exit;
                }
            }
            break;
            // ファイル削除
        case 'delete':
            // エラーチェック
            $arrErr = $this->lfErrorCheck();
            if(!is_array($arrErr)) {
                $this->sfDeleteDir($_POST['select_file']);
            }
            break;
            // ファイル作成
        case 'create':
            // エラーチェック
            $arrErr = $this->lfCreateErrorCheck();
            if(!is_array($arrErr)) {
                $create_dir = ereg_replace("/$", "", $now_dir);
                // ファイル作成
                if(!$this->sfCreateFile($create_dir."/".$_POST['create_file'], 0755)) {
                    // 作成エラー
                    $arrErr['create_file'] = "※ ".$_POST['create_file']."の作成に失敗しました。<br/>";
                } else {
                    $tpl_onload .= "alert('フォルダを作成しました。');";
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
                $tpl_onload .= "alert('ファイルをアップロードしました。');";
            }
            break;
            // フォルダ移動
        case 'move':
            $now_dir = $_POST['tree_select_file'];
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
        $this->arrFileList = $this->sfGetFileList($now_dir);
        $this->tpl_is_top_dir = $is_top_dir;
        $this->tpl_parent_dir = $parent_dir;
        $this->tpl_now_dir = $now_dir;
        $this->tpl_now_file = basename($now_dir);
        $this->arrErr = isset($arrErr) ? $arrErr : "";
        $this->arrParam = $_POST;

        // ツリーを表示する divタグid, ツリー配列変数名, 現在ディレクトリ, 選択ツリーhidden名, ツリー状態hidden名, mode hidden名
        $treeView = "fnTreeView('tree', arrTree, '$now_dir', 'tree_select_file', 'tree_status', 'move');";
        if (!empty($this->tpl_onload)) {
            $this->tpl_onload .= $treeView . $tpl_onload;
        } else {
            $this->tpl_onload = $treeView;
        }

        // ツリー配列作成用 javascript
        if (!isset($_POST['tree_status'])) $_POST['tree_status'] = "";
        $arrTree = $this->sfGetFileTree($top_dir, $_POST['tree_status']);
        $this->tpl_javascript .= "arrTree = new Array();\n";
        foreach($arrTree as $arrVal) {
            $this->tpl_javascript .= "arrTree[".$arrVal['count']."] = new Array(".$arrVal['count'].", '".$arrVal['type']."', '".$arrVal['path']."', ".$arrVal['rank'].",";
            if ($arrVal['open']) {
                $this->tpl_javascript .= "true);\n";
            } else {
                $this->tpl_javascript .= "false);\n";
            }
        }

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

    /*
     * 関数名：sfGetFileList()
     * 説明　：指定パス配下のディレクトリ取得
     * 引数1 ：取得するディレクトリパス
     */
    function sfGetFileList($dir) {
        $arrFileList = array();
        $arrDirList = array();

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $cnt = 0;
                // 行末の/を取り除く
                while (($file = readdir($dh)) !== false) $arrDir[] = $file;
                $dir = ereg_replace("/$", "", $dir);
                // アルファベットと数字でソート
                natcasesort($arrDir);
                foreach($arrDir as $file) {
                    // ./ と ../を除くファイルのみを取得
                    if($file != "." && $file != "..") {

                        $path = $dir."/".$file;
                        // SELECT内の見た目を整えるため指定文字数で切る
                        $file_name = SC_Utils_Ex::sfCutString($file, FILE_NAME_LEN);
                        $file_size = SC_Utils_Ex::sfCutString($this->sfGetDirSize($path), FILE_NAME_LEN);
                        $file_time = date("Y/m/d", filemtime($path));

                        // ディレクトリとファイルで格納配列を変える
                        if(is_dir($path)) {
                            $arrDirList[$cnt]['file_name'] = $file;
                            $arrDirList[$cnt]['file_path'] = $path;
                            $arrDirList[$cnt]['file_size'] = $file_size;
                            $arrDirList[$cnt]['file_time'] = $file_time;
                            $arrDirList[$cnt]['is_dir'] = true;
                        } else {
                            $arrFileList[$cnt]['file_name'] = $file;
                            $arrFileList[$cnt]['file_path'] = $path;
                            $arrFileList[$cnt]['file_size'] = $file_size;
                            $arrFileList[$cnt]['file_time'] = $file_time;
                            $arrFileList[$cnt]['is_dir'] = false;
                        }
                        $cnt++;
                    }
                }
                closedir($dh);
            }
        }

        // フォルダを先頭にしてマージ
        return array_merge($arrDirList, $arrFileList);
    }

    /*
     * 関数名：sfGetDirSize()
     * 説明　：指定したディレクトリのバイト数を取得
     * 引数1 ：ディレクトリ
     */
    function sfGetDirSize($dir) {
        $bytes = 0;
        if(file_exists($dir)) {
            // ディレクトリの場合下層ファイルの総量を取得
            if (is_dir($dir)) {
                $handle = opendir($dir);
                while ($file = readdir($handle)) {
                    // 行末の/を取り除く
                    $dir = ereg_replace("/$", "", $dir);
                    $path = $dir."/".$file;
                    if ($file != '..' && $file != '.' && !is_dir($path)) {
                        $bytes += filesize($path);
                    } else if (is_dir($path) && $file != '..' && $file != '.') {
                        // 下層ファイルのバイト数を取得する為、再帰的に呼び出す。
                        $bytes += $this->sfGetDirSize($path);
                    }
                }
            } else {
                // ファイルの場合
                $bytes = filesize($dir);
            }
        }
        // ディレクトリ(ファイル)が存在しない場合は0byteを返す
        if($bytes == "") $bytes = 0;

        return $bytes;
    }

    /*
     * 関数名：sfDeleteDir()
     * 説明　：指定したディレクトリを削除
     * 引数1 ：削除ファイル
     */
    function sfDeleteDir($dir) {
        $arrResult = array();
        if(file_exists($dir)) {
            // ディレクトリかチェック
            if (is_dir($dir)) {
                if ($handle = opendir("$dir")) {
                    $cnt = 0;
                    while (false !== ($item = readdir($handle))) {
                        if ($item != "." && $item != "..") {
                            if (is_dir("$dir/$item")) {
                                sfDeleteDir("$dir/$item");
                            } else {
                                $arrResult[$cnt]['result'] = @unlink("$dir/$item");
                                $arrResult[$cnt]['file_name'] = "$dir/$item";
                            }
                        }
                        $cnt++;
                    }
                }
                closedir($handle);
                $arrResult[$cnt]['result'] = @rmdir($dir);
                $arrResult[$cnt]['file_name'] = "$dir/$item";
            } else {
                // ファイル削除
                $arrResult[0]['result'] = @unlink("$dir");
                $arrResult[0]['file_name'] = "$dir";
            }
        }

        return $arrResult;
    }

    /*
     * 関数名：sfGetFileTree()
     * 説明　：ツリー生成用配列取得(javascriptに渡す用)
     * 引数1 ：ディレクトリ
     * 引数2 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
     */
    function sfGetFileTree($dir, $tree_status) {

        $cnt = 0;
        $arrTree = array();
        $default_rank = count(split('/', $dir));

        // 文末の/を取り除く
        $dir = ereg_replace("/$", "", $dir);
        // 最上位層を格納(user_data/)
        if($this->sfDirChildExists($dir)) {
            $arrTree[$cnt]['type'] = "_parent";
        } else {
            $arrTree[$cnt]['type'] = "_child";
        }
        $arrTree[$cnt]['path'] = $dir;
        $arrTree[$cnt]['rank'] = 0;
        $arrTree[$cnt]['count'] = $cnt;
        // 初期表示はオープン
        if($_POST['mode'] != '') {
            $arrTree[$cnt]['open'] = $this->lfIsFileOpen($dir, $tree_status);
        } else {
            $arrTree[$cnt]['open'] = true;
        }
        $cnt++;

        $this->sfGetFileTreeSub($dir, $default_rank, $cnt, $arrTree, $tree_status);

        return $arrTree;
    }

    /*
     * 関数名：sfGetFileTree()
     * 説明　：ツリー生成用配列取得(javascriptに渡す用)
     * 引数1 ：ディレクトリ
     * 引数2 ：デフォルトの階層(/区切りで　0,1,2・・・とカウント)
     * 引数3 ：連番
     * 引数4 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
     */
    function sfGetFileTreeSub($dir, $default_rank, &$cnt, &$arrTree, $tree_status) {

        if(file_exists($dir)) {
            if ($handle = opendir("$dir")) {
                while (false !== ($item = readdir($handle))) $arrDir[] = $item;
                // アルファベットと数字でソート
                natcasesort($arrDir);
                foreach($arrDir as $item) {
                    if ($item != "." && $item != "..") {
                        // 文末の/を取り除く
                        $dir = ereg_replace("/$", "", $dir);
                        $path = $dir."/".$item;
                        // ディレクトリのみ取得
                        if (is_dir($path)) {
                            $arrTree[$cnt]['path'] = $path;
                            if($this->sfDirChildExists($path)) {
                                $arrTree[$cnt]['type'] = "_parent";
                            } else {
                                $arrTree[$cnt]['type'] = "_child";
                            }

                            // 階層を割り出す
                            $arrCnt = split('/', $path);
                            $rank = count($arrCnt);
                            $arrTree[$cnt]['rank'] = $rank - $default_rank + 1;
                            $arrTree[$cnt]['count'] = $cnt;
                            // フォルダが開いているか
                            $arrTree[$cnt]['open'] = $this->lfIsFileOpen($path, $tree_status);
                            $cnt++;
                            // 下層ディレクトリ取得の為、再帰的に呼び出す
                            $this->sfGetFileTreeSub($path, $default_rank, $cnt, $arrTree, $tree_status);
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

    /*
     * 関数名：sfDirChildExists()
     * 説明　：指定したディレクトリ配下にファイルがあるか
     * 引数1 ：ディレクトリ
     */
    function sfDirChildExists($dir) {
        if(file_exists($dir)) {
            if (is_dir($dir)) {
                $handle = opendir($dir);
                while ($file = readdir($handle)) {
                    // 行末の/を取り除く
                    $dir = ereg_replace("/$", "", $dir);
                    $path = $dir."/".$file;
                    if ($file != '..' && $file != '.' && is_dir($path)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /*
     * 関数名：lfIsFileOpen()
     * 説明　：指定したファイルが前回開かれた状態にあったかチェック
     * 引数1 ：ディレクトリ
     * 引数2 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
     */
    function lfIsFileOpen($dir, $tree_status) {
        $arrTreeStatus = split('\|', $tree_status);
        if(in_array($dir, $arrTreeStatus)) {
            return true;
        }

        return false;
    }

    /*
     * 関数名：sfDownloadFile()
     * 引数1 ：ファイルパス
     * 説明　：ファイルのダウンロード
     */
    function sfDownloadFile($file) {
        // ファイルの場合はダウンロードさせる
        Header("Content-disposition: attachment; filename=".basename($file));
        Header("Content-type: application/octet-stream; name=".basename($file));
        Header("Cache-Control: ");
        Header("Pragma: ");
        echo ($this->sfReadFile($file));
    }

    /*
     * 関数名：sfCreateFile()
     * 引数1 ：ファイルパス
     * 引数2 ：パーミッション
     * 説明　：ファイル作成
     */
    function sfCreateFile($file, $mode = "") {
        // 行末の/を取り除く
        if($mode != "") {
            $ret = @mkdir($file, $mode);
        } else {
            $ret = @mkdir($file);
        }

        return $ret;
    }

    /*
     * 関数名：sfReadFile()
     * 引数1 ：ファイルパス
     * 説明　：ファイル読込
     */
    function sfReadFile($filename) {
        $str = "";
        // バイナリモードでオープン
        $fp = @fopen($filename, "rb" );
        //ファイル内容を全て変数に読み込む
        if($fp) {
            $str = @fread($fp, filesize($filename)+1);
        }
        @fclose($fp);

        return $str;
    }
}
?>

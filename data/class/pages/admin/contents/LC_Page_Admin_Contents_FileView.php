<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");
require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_FileManager_Ex.php");

/**
 * ファイル表示 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_FileView extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        // ユーザー認証
        SC_Utils_Ex::sfIsSuccess(new SC_Session());

        // ソースとして表示するファイルを定義(直接実行しないファイル)
        $arrViewFile = array(
                             'html',
                             'htm',
                             'tpl',
                             'php',
                             'css',
                             'js',
                             );

        // 拡張子取得
        $arrResult = split('\.', $_GET['file']);
        $ext = $arrResult[count($arrResult)-1];

        // ファイル内容表示
        if(in_array($ext, $arrViewFile)) {
            $objFileManager = new SC_Helper_FileManager_Ex();
            // ファイルを読み込んで表示
            header("Content-type: text/plain\n\n");
            print($objFileManager->sfReadFile(USER_PATH . $_GET['file']));
        } else {
            $this->sendRedirect(USER_URL . $_GET['file']);
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
}
?>

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
        // FIXME パスのチェック関数が必要
        if (preg_match('|\./|', $_GET['file'])) {
            SC_Utils_Ex::sfDispError('');
        }
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
            exit;
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

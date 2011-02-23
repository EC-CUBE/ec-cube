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
require_once(CLASS_EX_REALDIR . "page_extends/LC_Page_Ex.php");

/**
 * 管理者ログイン のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->template = MAIN_FRAME;
        
        //IP制限チェック
        $allow_hosts = unserialize(ADMIN_ALLOW_HOSTS);
        if(count($allow_hosts) > 0){
            if(array_search($_SERVER["REMOTE_ADDR"],$allow_hosts) === FALSE){
                SC_Response::sendHttpStatus(403);
                exit;
            }
        }
        
        //SSL制限チェック
        if(ADMIN_FORCE_SSL == TRUE){
            if(empty($_SERVER['HTTPS']) AND $_SERVER['SERVER_PORT'] != 443){
                SC_Response::sendRedirect($SERVER["REQUEST_URI"], $_GET,FALSE, TRUE);
            }
        }

        // ディスプレイクラス生成
        $this->objDisplay = new SC_Display();

        // プラグインクラス生成
        // $this->objPlagin = new SC_Helper_Plugin_Ex();
        // $this->objPlagin->preProcess($this);

        // トランザクショントークンの検証と生成
        $this->doValidToken(true);
        $this->setTokenTo();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
    }

    /**
     * Page のレスポンス送信.
     *
     * @return void
     */
    function sendResponse() {
        $this->objDisplay->prepare($this, true);
        $this->objDisplay->response->write();
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

<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
        if (is_array($allow_hosts) && count($allow_hosts) > 0) {
            if (array_search($_SERVER['REMOTE_ADDR'],$allow_hosts) === FALSE) {
                SC_Utils_Ex::sfDispError(AUTH_ERROR);
            }
        }

        //SSL制限チェック
        if (ADMIN_FORCE_SSL == TRUE) {
            if (SC_Utils_Ex::sfIsHTTPS() === false) {
                SC_Response_Ex::sendRedirect($_SERVER['REQUEST_URI'], $_GET, FALSE, TRUE);
            }
        }

        $this->tpl_authority = $_SESSION['authority'];

        // ディスプレイクラス生成
        $this->objDisplay = new SC_Display_Ex();

        // スーパーフックポイントを実行.
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        $objPlugin->doAction('LC_Page_preProcess', array($this));

        // トランザクショントークンの検証と生成
        $this->doValidToken(true);
        $this->setTokenTo();

        // ローカルフックポイントを実行
        $parent_class_name = get_parent_class($this);
        $objPlugin->doAction($parent_class_name . '_action_before', array($this));
        $class_name = get_class($this);
        if ($class_name != $parent_class_name) {
            $objPlugin->doAction($class_name . '_action_before', array($this));
        }
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
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        // ローカルフックポイントを実行
        $parent_class_name = get_parent_class($this);
        $objPlugin->doAction($parent_class_name . '_action_after', array($this));
        $class_name = get_class($this);
        if ($class_name != $parent_class_name) {
            $objPlugin->doAction($class_name . '_action_after', array($this));
        }

        // HeadNaviにpluginテンプレートを追加する.
        $objPlugin->setHeadNaviBlocs($this->arrPageLayout['HeadNavi']);

        // スーパーフックポイントを実行.
        $objPlugin->doAction('LC_Page_process', array($this));

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

    /**
     * 前方互換用
     *
     * @deprecated 2.12.0 GC_Utils_Ex::gfPrintLog を使用すること
     */
    function log($mess, $log_level='Info') {
        trigger_error('前方互換用メソッドが使用されました。', E_USER_WARNING);
        // ログレベル=Debugの場合は、DEBUG_MODEがtrueの場合のみログ出力する
        if ($log_level === 'Debug' && DEBUG_MODE === false) {
            return;
        }

        // ログ出力
        GC_Utils_Ex::gfPrintLog($mess, '');
    }

}

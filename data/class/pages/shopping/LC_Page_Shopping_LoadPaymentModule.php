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
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 決済モジュールの呼び出しを行うクラス.
 *
 * 決済フローの妥当性検証は, トランザクションID等を使用して, 決済モジュール側で
 * 行う必要がある.
 *
 * @package Page
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class LC_Page_Shopping_LoadPaymentModule extends LC_Page_Ex {

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

        $order_id = $this->getOrderId();
        if ($order_id === false) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            return;
        }

        $module_path = $this->getModulePath($order_id);
        if ($module_path === false) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", true,
                                      "モジュールファイルの取得に失敗しました。<br />この手続きは無効となりました。");
            return;
        }
        require_once $module_path;
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
     * 受注IDをキーにして, 決済モジュールのパスを取得する.
     *
     * 決済モジュールが取得できた場合は, require 可能な決済モジュールのパスを返す.
     * 受注IDが無効な場合, 取得したパスにファイルが存在しない場合は false
     *
     * @param integer $order_id 受注ID
     * @return string|boolean 成功した場合は決済モジュールのパス;
     *                        失敗した場合 false
     */
    function getModulePath($order_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql = <<< __EOS__
            SELECT module_path
              FROM dtb_payment T1
              JOIN dtb_order T2
                ON T1.payment_id = T2.payment_id
             WHERE order_id = ?
__EOS__;
        $module_path = $objQuery->getOne($sql, array($order_id));
        if (file_exists($module_path)) {
            return $module_path;
        }
        return false;
    }

    /**
     * 受注ID を取得する.
     *
     * 以下の順序で受注IDを取得する.
     *
     * 1. $_SESSION['order_id']
     * 2. $_POST['order_id']
     * 3. $_GET['order_id']
     *
     * 受注IDが取得できない場合は false を返す.
     *
     * @access private
     * @return integer|boolean 受注IDの取得に成功した場合は受注IDを返す;
     *                         失敗した場合は, false を返す.
     */
    function getOrderId() {
        if (isset($_SESSION['order_id'])
            && !SC_Utils_Ex::isBlank($_SESSION['order_id'])
            && SC_Utils_Ex::sfIsInt($_SESSION['order_id'])) {
            return $_SESSION['order_id'];
        }

        if (isset($_POST['order_id'])
            && !SC_Utils_Ex::isBlank($_POST['order_id'])
            && SC_Utils_Ex::sfIsInt($_POST['order_id'])) {
            return $_POST['order_id'];
        }

        if (isset($_GET['order_id'])
            && !SC_Utils_Ex::isBlank($_GET['order_id'])
            && SC_Utils_Ex::sfIsInt($_GET['order_id'])) {
            return $_GET['order_id'];
        }
        return false;
    }
}
?>

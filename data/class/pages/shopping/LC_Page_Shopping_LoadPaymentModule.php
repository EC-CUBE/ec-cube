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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * 決済モジュールの呼び出しを行うクラス.
 *
 * @package Page
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class LC_Page_Shopping_LoadPaymentModule extends LC_Page {

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
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objPurchase = new SC_Helper_Purchase_Ex();

        if (!$objSiteSess->isPrePage()) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }

        $uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($uniqid, $objCartSess);

        $payment_id = $this->getPaymentId();
        if ($payment_id === false) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            return;
        }

        $module_path = $this->getModulePath($payment_id);
        if ($module_path === false) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", true,
                                      "モジュールファイルの取得に失敗しました。<br />この手続きは無効となりました。");
            return;
        }
        require_once($module_path);
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $this->process();
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
     * 支払い方法IDをキーにして, 決済モジュールのパスを取得する.
     *
     * 決済モジュールが取得できた場合は, require 可能な決済モジュールのパスを返す.
     * 支払い方法IDが無効な場合, 取得したパスにファイルが存在しない場合は false
     *
     * @param integer $payment_id 支払い方法ID
     * @return string|boolean 成功した場合は決済モジュールのパス;
     *                        失敗した場合 false
     */
    function getModulePath($payment_id) {
        $objQuery =& SC_Query::getSingletonInstance();
        $sql = <<< __EOS__
            SELECT module_path
              FROM dtb_payment
             WHERE payment_id = ?
__EOS__;
        $module_path = $objQuery->getOne($sql, array($payment_id));
        if (file_exists($module_path)) {
            return $module_path;
        }
        return false;
    }

    /**
     * 支払い方法ID を取得する.
     *
     * 以下の順序で支払い方法IDを取得する.
     *
     * 1. $_SESSION['payment_id']
     * 2. $_POST['payment_id']
     * 3. $_GET['payment_id']
     *
     * 支払い方法IDが取得できない場合は false を返す.
     *
     * @access private
     * @return integer|boolean 支払い方法の取得に成功した場合は支払い方法IDを返す;
     *                         失敗した場合は, false を返す.
     */
    function getPaymentId() {
        if (isset($_SESSION['payment_id'])
            && !SC_Utils_Ex::isBlank($_SESSION['payment_id'])) {
            return $_SESSION['payment_id'];
        }

        if (isset($_POST['payment_id'])
            && !SC_Utils_Ex::isBlank($_POST['payment_id'])) {
            return $_POST['payment_id'];
        }

        if (isset($_GET['payment_id'])
            && !SC_Utils_Ex::isBlank($_GET['payment_id'])) {
            return $_GET['payment_id'];
        }

        return false;
    }
}
?>

<?php //-*- coding: utf-8 -*-
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
 * エラー表示のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Error.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Error extends LC_Page_Ex {

    // {{{ properties

    /** エラー種別 */
    var $type;

    /** SC_SiteSession インスタンス */
    var $objSiteSess;

    /** TOPへ戻るフラグ */
    var $return_top = false;

    /** エラーメッセージ */
    var $err_msg = '';

    /** モバイルサイトの場合 true */
    var $is_mobile = false;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * DBエラー発生時, エラーページを表示しようした際の DB 接続を防ぐため,
     * ここでは, parent::init() を行わない.
     * @return void
     */
    function init() {
        $this->tpl_mainpage = 'error.tpl';
        $this->tpl_title = t('c_Error_01');
        // ディスプレイクラス生成
        $this->objDisplay = new SC_Display_Ex();

        // transformでフックしているばあいに, 再度エラーが発生するため, コールバックを無効化.
        $objHelperPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
        $objHelperPlugin->arrRegistedPluginActions = array();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のプロセス。
     *
     * @return void
     */
    function action() {

        switch ($this->type) {
            case PRODUCT_NOT_FOUND:
                $this->tpl_error=t('c_The page you specified does not exist._01');
                SC_Response_Ex::sendHttpStatus(404);
                break;
            case PAGE_ERROR:
                $this->tpl_error=t('LC_Page_Error_003');
                break;
            case CART_EMPTY:
                $this->tpl_error=t('c_There are no products in your cart._01');
                break;
            case CART_ADD_ERROR:
                $this->tpl_error=t('c_It is not possible to add products to your cart during purchase processing._01');
                break;
            case CANCEL_PURCHASE:
                $this->tpl_error=t('LC_Page_Error_006');
                break;
            case CATEGORY_NOT_FOUND:
                $this->tpl_error=t('c_The category you specified does not exist._01');
                SC_Response_Ex::sendHttpStatus(404);
                break;
            case SITE_LOGIN_ERROR:
                $this->tpl_error=t('LC_Page_Error_008');
                break;
            case TEMP_LOGIN_ERROR:
                $this->tpl_error=t('LC_Page_Error_009');
                break;
            case CUSTOMER_ERROR:
                $this->tpl_error=t('c_Unauthorized access._01');
                break;
            case SOLD_OUT:
                $this->tpl_error=t('LC_Page_Error_011');
                break;
            case CART_NOT_FOUND:
                $this->tpl_error=t('LC_Page_Error_012');
                break;
            case LACK_POINT:
                $this->tpl_error=t('LC_Page_Error_013');
                break;
            case FAVORITE_ERROR:
                $this->tpl_error=t('c_This product is already added to your favorites_01');
                break;
            case EXTRACT_ERROR:
                $this->tpl_error=t('LC_Page_Error_015');
                break;
            case FTP_DOWNLOAD_ERROR:
                $this->tpl_error=t('c_FTP download of file failed._01');
                break;
            case FTP_LOGIN_ERROR:
                $this->tpl_error=t('LC_Page_Error_017');
                break;
            case FTP_CONNECT_ERROR:
                $this->tpl_error=t('LC_Page_Error_018');
                break;
            case CREATE_DB_ERROR:
                $this->tpl_error=t('LC_Page_Error_019');
                break;
            case DB_IMPORT_ERROR:
                $this->tpl_error=t('LC_Page_Error_020');
                break;
            case FILE_NOT_FOUND:
                $this->tpl_error=t('LC_Page_Error_021');
                break;
            case WRITE_FILE_ERROR:
                $this->tpl_error=t('LC_Page_Error_022');
                break;
            case DOWNFILE_NOT_FOUND:
                $this->tpl_error=t('LC_Page_Error_023');
                break;
            case FREE_ERROR_MSG:
                $this->tpl_error=$this->err_msg;
                break;
            default:
                $this->tpl_error=t('c_An error has occurred._01');
                break;
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

    /**
     * エラーページではトランザクショントークンの自動検証は行わない
     */
    function doValidToken() {
        // queit.
    }
}

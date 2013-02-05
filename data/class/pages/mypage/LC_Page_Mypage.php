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
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * MyPage のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_MyPage extends LC_Page_AbstractMypage_Ex {

    // {{{ properties

    /** ページナンバー */
    var $tpl_pageno;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno = 'index';
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_subtitle = t('LC_Page_MyPage_001');


        } else {
            $this->tpl_subtitle = t('c_Purchase history list_01');
        }
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrCustomerOrderStatus = $masterData->getMasterData('mtb_customer_order_status');

        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {

        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getvalue('customer_id');

        //ページ送り用
        $this->objNavi = new SC_PageNavi_Ex($_REQUEST['pageno'],
                                            $this->lfGetOrderHistory($customer_id),
                                            SEARCH_PMAX,
                                            'fnNaviPage',
                                            NAVI_PMAX,
                                            'pageno=#page#',
                                            SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);

        $this->arrOrder = $this->lfGetOrderHistory($customer_id, $this->objNavi->start_row);

        switch ($this->getMode()) {
            case 'getList':
                echo SC_Utils_Ex::jsonEncode($this->arrOrder);
                SC_Response_Ex::actionExit();
                break;
            default:
                break;
        }
        // 支払い方法の取得
        $this->arrPayment = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;
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
     * 受注履歴を返す
     *
     * @param mixed $customer_id
     * @param mixed $startno 0以上の場合は受注履歴を返却する -1の場合は件数を返す
     * @access private
     * @return void
     */
    function lfGetOrderHistory($customer_id, $startno = -1) {
        $objQuery   = SC_Query_Ex::getSingletonInstance();

        $col        = 'order_id, create_date, payment_id, payment_total, status';
        $from       = 'dtb_order';
        $where      = 'del_flg = 0 AND customer_id = ?';
        $arrWhereVal = array($customer_id);
        $order      = 'order_id DESC';

        if ($startno == -1) {
            return $objQuery->count($from, $where, $arrWhereVal);
        }

        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        // 表示順序
        $objQuery->setOrder($order);

        //購入履歴の取得
        return $objQuery->select($col, $from, $where, $arrWhereVal);
    }
}

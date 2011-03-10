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
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * 購入履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_History extends LC_Page_AbstractMypage_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno     = 'index';
        $this->tpl_subtitle     = '購入履歴詳細';
        $this->httpCacheControl('nocache');

        $masterData             = new SC_DB_MasterData_Ex();
        $this->arrMAILTEMPLATE  = $masterData->getMasterData("mtb_mail_template");
        $this->arrPref          = $masterData->getMasterData('mtb_pref');
        $this->arrWDAY          = $masterData->getMasterData("mtb_wday");
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
        $objCustomer    = new SC_Customer_Ex();
        $objDb          = new SC_Helper_DB_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();

        if (!SC_Utils_Ex::sfIsInt($_GET['order_id'])) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $order_id        = $_GET['order_id'];

        //受注データの取得
        $this->tpl_arrOrderData = $objPurchase->getOrder($order_id, $objCustomer->getValue('customer_id'));

        if (empty($this->tpl_arrOrderData)){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->arrShipping      = $this->IfGetShippingDate(&$objPurchase, $order_id, $this->arrWDAY);

        $this->isMultiple       = count($this->arrShipping) > 1;
        // 支払い方法の取得
        $this->arrPayment       = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
        // 受注商品明細の取得
        $this->tpl_arrOrderDetail = $objPurchase->getOrderDetail($order_id);
        // 受注メール送信履歴の取得
        $this->tpl_arrMailHistory = $this->lfGetMailHistory($order_id);

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
     * 受注メール送信履歴の取得
     *
     * @param integer $order_id 注文番号
     * @return array 受注メール送信履歴の内容
     */
    function lfGetMailHistory($order_id) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $col        = 'send_date, subject, template_id, send_id';
        $where      = 'order_id = ?';
        $objQuery->setOrder('send_date DESC');
        return $objQuery->select($col, 'dtb_mail_history', $where, array($order_id));
    }

    /**
     * 受注お届け先情報の取得
     *
     * @param $objPurchase object SC_Helper_Purchaseクラス
     * @param $order_id integer 注文番号
     * @param $arrWDAY array 曜日データの配列
     * @return array お届け先情報
     */
    function lfGetShippingDate(&$objPurchase, $order_id, $arrWDAY) {
        $arrShipping = $objPurchase->getShippings($order_id);

        foreach($arrShipping as $shipping_index => $shippingData) {
            foreach($shippingData as $key => $val) {
                if($key == 'shipping_date' && SC_Utils_Ex::isBlank($val) == false) {
                    // お届け日を整形
                    list($y, $m, $d, $w) = explode(" ", date("Y m d w" , strtotime($val)));
                    $arrShipping[$shipping_index]['shipping_date'] = sprintf("%04d/%02d/%02d(%s)", $y, $m, $d, $arrWDAY[$w]);
                }
            }
        }

        return $arrShipping;
    }
}

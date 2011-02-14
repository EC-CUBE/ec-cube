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
require_once(CLASS_EX_REALDIR . "page_extends/mypage/LC_Page_AbstractMypage_Ex.php");

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
        $objCustomer    = new SC_Customer();
        $objDb          = new SC_Helper_DB_Ex();

        if (!SC_Utils_Ex::sfIsInt($_GET['order_id'])) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $order_id        = $_GET['order_id'];
        $arrOrderData   = $this->lfGetOrderData($objCustomer->getValue('customer_id'), $order_id);

        if (empty($arrOrderData)){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        //受注詳細データの取得
        $this->tpl_arrOrderData = $arrOrderData[0];

        $this->arrShipping      = $objDb->sfGetShippingData($order_id);
        $this->isMultiple       = count($this->arrShipping) > 1;
        // 支払い方法の取得
        $this->arrPayment       = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
        // お届け時間の取得
        $this->arrDelivTime     = SC_Utils_Ex::sfArrKeyValue($objDb->sfGetDelivTime($this->tpl_arrOrderData['payment_id']),
                                                            'time_id',
                                                            'deliv_time');
        // 受注商品明細の取得
        $this->tpl_arrOrderDetail = $this->lfGetOrderDetail($order_id);
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
     * 受注の取得
     *
     * @param integer $orderId 注文番号
     * @return array 受注の内容
     */
    function lfGetOrderData($customer_id, $order_id) {
        // DBから受注情報を読み込む
        $objQuery   =& SC_Query::getSingletonInstance();
        $from       = "dtb_order";
        $where      = "del_flg = 0 AND customer_id = ? AND order_id = ?";
        return $objQuery->select("*", $from, $where, array($customer_id, $order_id));
    }

    /**
     * 受注商品明細の取得
     *
     * @param integer $orderId 注文番号
     * @return array 受注商品明細の内容
     */
    function lfGetOrderDetail($order_id) {
        $objQuery   =& SC_Query::getSingletonInstance();
        $dbFactory  = SC_DB_DBFactory_Ex::getInstance();

        $col    = "
            od.product_id AS product_id,
            od.product_code AS product_code,
            od.product_name AS product_name,
            od.classcategory_name1 AS classcategory_name1,
            od.classcategory_name2 AS classcategory_name2,
            od.price AS price,
            od.quantity AS quantity,
            od.point_rate AS point_rate
            ,CASE WHEN EXISTS(SELECT * FROM dtb_products WHERE product_id = od.product_id AND del_flg = 0 AND status = 1) THEN '1' ELSE '0' END AS enable
            ,o.status AS status,
            pc.product_type_id AS product_type_id,
            o.payment_date AS payment_date,
            od.product_class_id as product_class_id,
            ".$dbFactory->getDownloadableDaysWhereSql()."
            AS effective";

        $from   = "dtb_products p, dtb_products_class pc, dtb_order_detail od, dtb_order o";
        $where  = "p.product_id = od.product_id AND pc.product_id = od.product_id AND pc.product_class_id = od.product_class_id AND od.order_id = o.order_id AND od.order_id = ?";

        return $objQuery->select($col, $from, $where, array($order_id));
    }

    /**
     * 受注メール送信履歴の取得
     *
     * @param integer $order_id 注文番号
     * @return array 受注メール送信履歴の内容
     */
    function lfGetMailHistory($order_id) {
        $objQuery   =& SC_Query::getSingletonInstance();
        $col        = 'send_date, subject, template_id, send_id';
        $where      = 'order_id = ?';
        $objQuery->setOrder('send_date DESC');
        return $objQuery->select($col, 'dtb_mail_history', $where, array($order_id));
    }
}

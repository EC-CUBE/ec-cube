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
 * 購入履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_History extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_navi = TEMPLATE_REALDIR . 'mypage/navi.tpl';
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'index';
        $this->tpl_subtitle = '購入履歴詳細';
        $this->httpCacheControl('nocache');
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMAILTEMPLATE = $masterData->getMasterData("mtb_mail_template");
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        
        $this->isMobile = Net_UserAgent_Mobile::isMobile();
   }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        if ( $this->isMobile === false ){
            $this->action();
        } else {
            $this->mobileAction();
        }
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        $objDb = new SC_Helper_DB_Ex();

        // FIXME 他の画面と同様のバリデーションを行なう
        if (!SC_Utils_Ex::sfIsInt($_GET['order_id'])) {
            SC_Utils_Ex::sfDispException();
        }

        $orderId = $_GET['order_id'];

        //不正アクセス判定
        $from = "dtb_order";
        $where = "del_flg = 0 AND customer_id = ? AND order_id = ? ";
        $arrval = array($objCustomer->getValue('customer_id'), $orderId);
        //DBに情報があるか判定
        $cnt = $objQuery->count($from, $where, $arrval);
        //ログインしていない、またはDBに情報が無い場合
        if (!$objCustomer->isLoginSuccess() || $cnt == 0){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        //受注詳細データの取得
        $this->arrDisp = $this->lfGetOrderData($orderId);
        $this->arrShipping = $this->lfGetShippingData($orderId);
        $this->isMultiple = count($this->arrShipping) > 1;
        // 支払い方法の取得
        $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
        // お届け時間の取得
        $arrRet = $objDb->sfGetDelivTime($this->arrDisp['payment_id']);
        $this->arrDelivTime = SC_Utils_Ex::sfArrKeyValue($arrRet, 'time_id', 'deliv_time');

        //マイページトップ顧客情報表示用
        $this->tpl_login = true;
        $this->CustomerName1 = $objCustomer->getvalue('name01');
        $this->CustomerName2 = $objCustomer->getvalue('name02');
        $this->CustomerPoint = $objCustomer->getvalue('point');

        // 受注商品明細の取得
        $this->tpl_arrOrderDetail = $this->lfGetOrderDetail($orderId);

        // 受注メール送信履歴の取得
        $this->tpl_arrMailHistory = $this->lfGetMailHistory($orderId);

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
     * Page のAction(モバイル).
     *
     * @return void
     */
    function mobileAction() {
        define ("HISTORY_NUM", 5);

        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        $pageNo = isset($_GET['pageno']) ? (int) $_GET['pageno'] : 0; // TODO

        // ログインチェック
        if(!isset($_SESSION['customer'])) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $col = "order_id, create_date, payment_id, payment_total";
        $from = "dtb_order";
        $where = "del_flg = 0 AND customer_id=?";
        $arrval = array($objCustomer->getvalue('customer_id'));
        $order = "order_id DESC";

        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset(HISTORY_NUM, $pageNo);
        // 表示順序
        $objQuery->setOrder($order);

        //購入履歴の取得
        $this->arrOrder = $objQuery->select($col, $from, $where, $arrval);

        // next
        if ($pageNo + HISTORY_NUM < $linemax) {
            $next = "<a href='history.php?pageno=" . ($pageNo + HISTORY_NUM) . "'>次へ→</a>";
        } else {
            $next = "";
        }

        // previous
        if ($pageNo - HISTORY_NUM > 0) {
            $previous = "<a href='history.php?pageno=" . ($pageNo - HISTORY_NUM) . "'>←前へ</a>";
        } elseif ($pageNo == 0) {
            $previous = "";
        } else {
            $previous = "<a href='history.php?pageno=0'>←前へ</a>";
        }

        // bar
        if ($next != '' && $previous != '') {
            $bar = " | ";
        } else {
            $bar = "";
        }

        $this->tpl_strnavi = $previous . $bar . $next;

    }

    /**
     * 受注の取得
     *
     * @param integer $orderId 注文番号
     * @return array 受注の内容
     */
    function lfGetOrderData($orderId) {
        // DBから受注情報を読み込む
        $objQuery = new SC_Query();
        /*
        $col = "order_id, create_date, payment_id, subtotal, tax, use_point, add_point, discount, ";
        $col .= "deliv_fee, charge, payment_total, deliv_name01, deliv_name02, deliv_kana01, deliv_kana02, ";
        $col .= "deliv_zip01, deliv_zip02, deliv_pref, deliv_addr01, deliv_addr02, deliv_tel01, deliv_tel02, deliv_tel03, deliv_time_id, deliv_date ";
        */
        $from = "dtb_order";
        $where = "order_id = ?";
        $arrRet = $objQuery->select("*", $from, $where, array($orderId));
        return $arrRet[0];
    }

    /**
     * 配送情報の取得.
     * TODO リファクタリング
     */
    function lfGetShippingData($orderId) {
        $objQuery =& SC_Query::getSingletonInstance();
        $objProduct = new SC_Product();
        $objQuery->setOrder('shipping_id');
        $arrRet = $objQuery->select("*", "dtb_shipping", "order_id = ?", array($orderId));
        foreach (array_keys($arrRet) as $key) {
            $objQuery->setOrder('shipping_id');
            $arrItems = $objQuery->select("*", "dtb_shipment_item", "order_id = ? AND shipping_id = ?",
                                       array($orderId, $arrRet[$key]['shipping_id']));
            foreach ($arrItems as $itemKey => $arrDetail) {
                foreach ($arrDetail as $detailKey => $detailVal) {
                    $arrRet[$key]['shipment_item'][$arrDetail['product_class_id']][$detailKey] = $detailVal;
                }

                $arrRet[$key]['shipment_item'][$arrDetail['product_class_id']]['productsClass'] =& $objProduct->getDetailAndProductsClass($arrDetail['product_class_id']);
            }
        }
        return $arrRet;
    }

    /**
     * 受注商品明細の取得
     *
     * @param integer $orderId 注文番号
     * @return array 受注商品明細の内容
     */
    function lfGetOrderDetail($orderId) {
        $objQuery = new SC_Query();
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();

        $col = "od.product_id AS product_id, od.product_code AS product_code, od.product_name AS product_name, od.classcategory_name1 AS classcategory_name1,";
        $col .= "od.classcategory_name2 AS classcategory_name2, od.price AS price, od.quantity AS quantity, od.point_rate AS point_rate";
        $col .= ",CASE WHEN EXISTS(SELECT * FROM dtb_products WHERE product_id = od.product_id AND del_flg = 0 AND status = 1) THEN '1' ELSE '0' END AS enable";
        $col .= ",o.status AS status, pc.product_type_id AS product_type_id, o.payment_date AS payment_date, od.product_class_id as product_class_id, ";
        $col .= $dbFactory->getDownloadableDaysWhereSql();
        $col .= " AS effective";
        $where = "p.product_id = od.product_id AND pc.product_id = od.product_id AND pc.product_class_id = od.product_class_id AND od.order_id = o.order_id AND od.order_id = ?";
        $arrRet = $objQuery->select($col, "dtb_products p, dtb_products_class pc, dtb_order_detail od, dtb_order o", $where,array($orderId));
        return $arrRet;
    }

    /**
     * 受注メール送信履歴の取得
     *
     * @param integer $orderId 注文番号
     * @return array 受注メール送信履歴の内容
     */
    function lfGetMailHistory($orderId) {
        $objQuery = new SC_Query();
        $col = 'send_date, subject, template_id, send_id';
        $where = 'order_id = ?';
        $objQuery->setOrder('send_date DESC');
        $this->arrMailHistory = $objQuery->select($col, 'dtb_mail_history', $where, array($orderId));
    }
}
?>

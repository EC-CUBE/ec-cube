<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * 受注履歴からカート遷移 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Order extends LC_Page_AbstractMypage_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action()
    {
        //決済処理中ステータスのロールバック
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objPurchase->checkSessionPendingOrder();
        $objPurchase->checkDbMyPendignOrder();
        $objPurchase->checkDbAllPendingOrder();
		
        //受注詳細データの取得
        $arrOrderDetail = $this->lfGetOrderDetail($_POST['order_id']);

        //ログインしていない、またはDBに情報が無い場合
        if (empty($arrOrderDetail)) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->lfAddCartProducts($arrOrderDetail);
        SC_Response_Ex::sendRedirect(CART_URLPATH);
    }

    // 受注詳細データの取得
    function lfGetOrderDetail($order_id)
    {
        $objQuery       = SC_Query_Ex::getSingletonInstance();

        $objCustomer    = new SC_Customer_Ex();
        //customer_idを検証
        $customer_id    = $objCustomer->getValue('customer_id');
        $order_count    = $objQuery->count('dtb_order', 'order_id = ? and customer_id = ?', array($order_id, $customer_id));
        if ($order_count != 1) return array();

        $col    = 'dtb_order_detail.product_class_id, quantity';
        $table  = 'dtb_order_detail LEFT JOIN dtb_products_class ON dtb_order_detail.product_class_id = dtb_products_class.product_class_id';
        $where  = 'order_id = ?';
        $objQuery->setOrder('order_detail_id');
        $arrOrderDetail = $objQuery->select($col, $table, $where, array($order_id));

        return $arrOrderDetail;
    }

    // 商品をカートに追加
    function lfAddCartProducts($arrOrderDetail)
    {
        $objCartSess = new SC_CartSession_Ex();
        foreach ($arrOrderDetail as $order_row) {
            $objCartSess->addProduct($order_row['product_class_id'], $order_row['quantity']);
        }
    }
}

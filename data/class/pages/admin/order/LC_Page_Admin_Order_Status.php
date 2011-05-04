<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * ステータス管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_Status extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/status.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'status';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = 'ステータス管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");
        $this->arrORDERSTATUS_COLOR = $masterData->getMasterData("mtb_order_status_color");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objDb = new SC_Helper_DB_Ex();

        // パラメータ管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        // 入力値の変換
        $objFormParam->convParam();

        $this->arrForm = $objFormParam->getHashArray();
        //        $this->arrForm = $_POST;

        //支払方法の取得
        $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

        switch ($this->getMode()){
            case 'update':
                switch ($objFormParam->getValue('change_status')) {
                    case '':
                        break;
                        // 削除
                    case 'delete':
                        $this->lfDelete($objFormParam->getValue('move'));
                        break;
                        // 更新
                    default:
                        $this->lfStatusMove($objFormParam->getValue('change_status'), $objFormParam->getValue('move'));
                        break;
                }

                //ステータス情報
                $status = !is_null($objFormParam->getValue('status')) ? $objFormParam->getValue('status') : "";
                break;

            case 'search':
                //ステータス情報
                $status = !is_null($_POST['status']) ? $objFormParam->getValue('status') : "";
                break;

            default:
                //ステータス情報
                //デフォルトで新規受付一覧表示
                $status = ORDER_NEW;
                break;
        }

        //ステータス情報
        $this->SelectedStatus = $status;
        //検索結果の表示
        $this->lfStatusDisp($status, $objFormParam->getValue('search_pageno'));
    }

    /**
     *  パラメータ情報の初期化
     *  @param SC_FormParam
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("注文番号", "order_id", INT_LEN, 'n', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("変更前ステータス", 'status', INT_LEN, 'n', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("変更後ステータス", "change_status", STEXT_LEN, 'KVa', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("ページ番号", "search_pageno", INT_LEN, 'n', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("移動注文番号", 'move', INT_LEN, 'n', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /**
     *  入力内容のチェック
     *  @param SC_FormParam
     */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet = $objFormParam->getHashArray();
        $arrErr = $objFormParam->checkError();
        if(is_null($objFormParam->getValue('search_pageno'))){
            $objFormParam->setValue('search_pageno', 1);
        }

        if($this->getMode() == 'change'){
            if(is_null($objFormParam->getValue('change_status'))){
                $objFormParam->setValue('change_status',"");
            }
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

    //ステータス一覧の表示
    function lfStatusDisp($status,$pageno){
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $select ="*";
        $from = "dtb_order";
        $where = "del_flg = 0 AND status = ?";
        $arrval[] = $status;
        $order = "order_id DESC";

        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;

        // ページ送りの処理
        $page_max = ORDER_STATUS_MAX;

        // ページ送りの取得
        $objNavi = new SC_PageNavi_Ex($pageno, $linemax, $page_max, 'fnNaviSearchOnlyPage', NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;      // 表示文字列
        $startno = $objNavi->start_row;

        $this->tpl_pageno = $pageno;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($page_max, $startno);

        //表示順序
        $objQuery->setOrder($order);

        //検索結果の取得
        $this->arrStatus = $objQuery->select($select, $from, $where, $arrval);
    }

    /**
     * ステータス情報の更新
     */
    function lfStatusMove($statusId, $arrOrderId) {
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objQuery = new SC_Query_Ex();

        if (!isset($arrOrderId) || !is_array($arrOrderId)) {
            return false;
        }
        $masterData = new SC_DB_MasterData_Ex();
        $arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");

        $objQuery->begin();

        foreach ($arrOrderId as $orderId) {
            $objPurchase->sfUpdateOrderStatus($orderId, $statusId);
        }

        $objQuery->commit();

        $this->tpl_onload = "window.alert('選択項目を" . $arrORDERSTATUS[$statusId] . "へ移動しました。');";
        return true;
    }

    /**
     * 受注テーブルの論理削除
     */
    function lfDelete($arrOrderId) {
        $objQuery = new SC_Query_Ex();

        if (!isset($arrOrderId) || !is_array($arrOrderId)) {
            return false;
        }

        $arrUpdate = array(
             'del_flg'      => 1,
             'update_date'  => 'Now()'
        );

        $objQuery->begin();

        foreach ($arrOrderId as $orderId) {
            $objQuery->update('dtb_order', $arrUpdate, 'order_id = ?', array($orderId));
        }

        $objQuery->commit();

        $this->tpl_onload = "window.alert('選択項目を削除しました。');";
        return true;
    }
}
?>

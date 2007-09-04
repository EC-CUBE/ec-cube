<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * ステータス管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_Status extends LC_Page {

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
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'status';

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
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDb = new SC_Helper_DB_Ex();
        $objQuery = new SC_Query();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        //ステータス情報（仮定）
        $this->SelectedStatus = isset($_POST['status']) ? $_POST['status'] : "";
        $this->arrForm = $_POST;

        //支払方法の取得
        $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        if (!isset($_POST['search_pageno'])) $_POST['search_pageno'] = 1;

        switch ($_POST['mode']){

        case 'search':
            if (!isset($_POST['change_status'])) $_POST['change_status'] = "";
            switch($_POST['change_status']){

            default:
                break;

                //新規受付
            case ORDER_NEW:
                $this->lfStatusMove(ORDER_NEW, $_POST['move']);
                break;

                //入金待ち
            case ORDER_PAY_WAIT:
                $this->lfStatusMove(ORDER_PAY_WAIT, $_POST['move']);
                break;

                //キャンセル
            case ORDER_CANCEL:
                $this->lfStatusMove(ORDER_CANCEL, $_POST['move']);
                break;

                //取り寄せ中
            case ORDER_BACK_ORDER:
                $this->lfStatusMove(ORDER_BACK_ORDER, $_POST['move']);
                break;

                //発送済み
            case ORDER_DELIV:
                $this->lfStatusMove(ORDER_DELIV, $_POST['move']);
                break;

                //入金済み
            case ORDER_PRE_END:
                $this->lfStatusMove(ORDER_PRE_END, $_POST['move']);
                break;

                //削除
            case 'delete':
                $this->lfStatusMove("delete",$_POST['move']);
                break;
            }

            //検索結果の表示
            $this->lfStatusDisp($_POST['status'],$_POST['search_pageno']);
            break;

        default:
            //デフォルトで新規受付一覧表示
            $this->lfStatusDisp(ORDER_NEW, $_POST['search_pageno']);
            $this->defaultstatus = ORDER_NEW;
            break;
        }

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
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
        $objQuery = new SC_Query();

        $select ="*";
        $from = "dtb_order";
        $where="del_flg=0 AND status=?";
        $order = "order_id DESC";

        $linemax = $objQuery->count("dtb_order", "del_flg = 0 AND status=?", array($status));
        $this->tpl_linemax = $linemax;

        // ページ送りの処理
        $page_max = ORDER_STATUS_MAX;

        // ページ送りの取得
        $objNavi = new SC_PageNavi($pageno, $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;      // 表示文字列
        $startno = $objNavi->start_row;

        $this->tpl_pageno = $pageno;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setlimitoffset($page_max, $startno);

        //表示順序
        $objQuery->setorder($order);

        //検索結果の取得
        $this->arrStatus = $objQuery->select($select, $from, $where, array($status));
    }

    //ステータス情報の更新（削除）
    function lfStatusMove($status_id, $arrMove){
        $objQuery = new SC_Query();
        $masterData = new SC_DB_MasterData_Ex();
        $arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");

        $table = 'dtb_order';
        $where = 'order_id = ?';
        $arrUpdate = array('update_date' => 'NOW()');

        $delflg  = '1'; // 削除フラグ
        $message = '';  // ステータス変更後にポップアップするメッセージの内容

        if ( $status_id == 'delete' ) {
            $arrUpdate['del_flg'] = $delflg;
            $message = '削除';
        }
        // ステータスが発送済みの時は発送日を更新
        elseif ( $status_id == ORDER_DELIV ) {
            $arrUpdate['status'] = $status_id;
            $arrUpdate['commit_date'] = 'NOW()';
            $message = $arrORDERSTATUS[$status_id] . 'へ移動';
        }
        else {
            $arrUpdate['status'] = $status_id;
            $message = $arrORDERSTATUS[$status_id] . 'へ移動';
        }

        if ( isset($arrMove) ){
            foreach ( $arrMove as $val ){
                if ( $val != "" ) {
                    $objQuery->update($table, $arrUpdate, $where, array($val));
                }

            }
        }

        $this->tpl_onload = "window.alert('選択項目を" . $message . "しました。');";
    }
}
?>

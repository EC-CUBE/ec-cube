<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./index_csv.php");

//ステータス管理-一括移動機能

class LC_Page {
    function LC_Page() {
        $this->tpl_mainpage = 'order/status.tpl';
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'status';
        global $arrORDERSTATUS;
        global $arrORDERSTATUS_COLOR;
        $this->arrORDERSTATUS = $arrORDERSTATUS;
        $this->arrORDERSTATUS_COLOR = $arrORDERSTATUS_COLOR;
        
    }
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

//ステータス情報（仮定）
$objPage->SelectedStatus = $_POST['status'];
$objPage->arrForm = $_POST;

//支払方法の取得
$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

switch ($_POST['mode']){
    
    case 'search':
        switch($_POST['change_status']){
                
            default:
            break;
            
            //新規受付
            case ORDER_NEW:
                lfStatusMove(ORDER_NEW, $_POST['move']);
            break;
            
            //入金待ち
            case ORDER_PAY_WAIT:
                lfStatusMove(ORDER_PAY_WAIT, $_POST['move']);
            break;
            
            //キャンセル
            case ORDER_CANCEL:
                lfStatusMove(ORDER_CANCEL, $_POST['move']);
            break;
            
            //取り寄せ中
            case ORDER_BACK_ORDER:
                lfStatusMove(ORDER_BACK_ORDER, $_POST['move']);
            break;
            
            //発送済み
            case ORDER_DELIV:
                lfStatusMove(ORDER_DELIV, $_POST['move']);
            break;
            
            //入金済み
            case ORDER_PRE_END:
                lfStatusMove(ORDER_PRE_END, $_POST['move']);
            break;
            
            //削除
            case 'delete':
                lfStatusMove("delete",$_POST['move']);
            break;
        }
        
        //検索結果の表示
        lfStatusDisp($_POST['status'],$_POST['search_pageno']);
        break;
        
    default:
        //デフォルトで新規受付一覧表示
        lfStatusDisp(ORDER_NEW, $_POST['search_pageno']);
        $objPage->defaultstatus = ORDER_NEW;
        break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

//ステータス一覧の表示
function lfStatusDisp($status,$pageno){
    global $objPage;
    global $objQuery;
    
    $select ="*";
    $from = "dtb_order";
    $where="del_flg=0 AND status=?";
    $order = "order_id DESC";
    
    $linemax = $objQuery->count("dtb_order", "del_flg = 0 AND status=?", array($status));
    $objPage->tpl_linemax = $linemax;
    
    // ページ送りの処理
    $page_max = ORDER_STATUS_MAX;
    
    // ページ送りの取得
    $objNavi = new SC_PageNavi($pageno, $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
    $objPage->tpl_strnavi = $objNavi->strnavi;      // 表示文字列
    $startno = $objNavi->start_row;
    
    $objPage->tpl_pageno = $pageno;
    
    // 取得範囲の指定(開始行番号、行数のセット)
    $objQuery->setlimitoffset($page_max, $startno);
    
    //表示順序
    $objQuery->setorder($order);
    
    //検索結果の取得
    $objPage->arrStatus = $objQuery->select($select, $from, $where, array($status));
    
    return $objPage;
}

//ステータス情報の更新（削除）
function lfStatusMove($status_id, $arrMove){
    global $objQuery;
    global $objPage;
    global $arrORDERSTATUS;
    
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
    
    $objPage->tpl_onload = "window.alert('選択項目を" . $message . "しました。');";
}

?>
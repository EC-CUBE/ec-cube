<?php
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
				case '1':
					lfStatusMove(1,$_POST['move']);
				break;
				
				//入金待ち
				case '2':
					lfStatusMove(2,$_POST['move']);
				break;
				
				//キャンセル
				case '3':
					lfStatusMove(3,$_POST['move']);
				break;
				
				//取り寄せ中
				case '4':
					lfStatusMove(4,$_POST['move']);
				break;
				
				//発送済み
				case '5':
					lfStatusMove(5,$_POST['move']);
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
	lfStatusDisp(1,$_POST['search_pageno']);
	$objPage->defaultstatus = 1;
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
	$where="delete=0 AND status=?";
	$order = "order_id DESC";
	
	$linemax = $objQuery->count("dtb_order", "delete = 0 AND status=?", array($status));
	$objPage->tpl_linemax = $linemax;
	
	// ページ送りの処理
	$page_max = ORDER_STATUS_MAX;
	
	// ページ送りの取得
	$objNavi = new SC_PageNavi($pageno, $linemax, $page_max, "fnNaviSearchPage", NAVI_PMAX);
	$objPage->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
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

//ステータス情報の更新（移動）
function lfStatusMove($status_id,$move){
	global $objQuery;
	global $objPage;
	
	if ($status_id == 'delete'){
		$sql="UPDATE dtb_order SET delete=1";
	}elseif ($status_id == 5){
		$sql="UPDATE dtb_order SET status=".$status_id.",commit_date=now() ";
	}else{
		$sql="UPDATE dtb_order SET status=".$status_id." ";
	}
		$sql.="WHERE order_id=?";
		if (isset($move)){
			foreach ($move as $val){
			if ($val != "") {
				$objQuery->exec($sql, array($val));
			}
			$objPage->tpl_onload = "window.alert('選択項目を移動しました。');";
			}
		}
}

?>
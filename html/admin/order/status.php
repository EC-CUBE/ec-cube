<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./index_csv.php");

//���ơ���������-����ư��ǽ

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

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

//���ơ���������ʲ����
$objPage->SelectedStatus = $_POST['status'];
$objPage->arrForm = $_POST;
					
//��ʧ��ˡ�μ���
$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

switch ($_POST['mode']){
	
	case 'search':
		switch($_POST['change_status']){
				
				default:
				break;
			
				//��������
				case '1':
					lfStatusMove(1,$_POST['move']);
				break;
				
				//�����Ԥ�
				case '2':
					lfStatusMove(2,$_POST['move']);
				break;
				
				//����󥻥�
				case '3':
					lfStatusMove(3,$_POST['move']);
				break;
				
				//������
				case '4':
					lfStatusMove(4,$_POST['move']);
				break;
				
				//ȯ���Ѥ�
				case '5':
					lfStatusMove(5,$_POST['move']);
				break;
				
				//����Ѥ�
				case '6':
					lfStatusMove(6,$_POST['move']);
				break;
				
				//���
				case 'delete':
					lfStatusMove("delete",$_POST['move']);
				break;
			}
	
	//������̤�ɽ��
	lfStatusDisp($_POST['status'],$_POST['search_pageno']);
	break;
	
	default:
	//�ǥե���Ȥǿ������հ���ɽ��
	lfStatusDisp(1,$_POST['search_pageno']);
	$objPage->defaultstatus = 1;
	break;
	}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

//���ơ�����������ɽ��
function lfStatusDisp($status,$pageno){
	global $objPage;
	global $objQuery;
	
	$select ="*";
	$from = "dtb_order";
	$where="del_flg=0 AND status=?";
	$order = "order_id DESC";
	
	$linemax = $objQuery->count("dtb_order", "del_flg = 0 AND status=?", array($status));
	$objPage->tpl_linemax = $linemax;
	
	// �ڡ�������ν���
	$page_max = ORDER_STATUS_MAX;
	
	// �ڡ�������μ���
	$objNavi = new SC_PageNavi($pageno, $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
	$objPage->tpl_strnavi = $objNavi->strnavi;		// ɽ��ʸ����
	$startno = $objNavi->start_row;
	
	$objPage->tpl_pageno = $pageno;
	
	// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
	$objQuery->setlimitoffset($page_max, $startno);
	
	//ɽ�����
	$objQuery->setorder($order);
	
	//������̤μ���
	$objPage->arrStatus = $objQuery->select($select, $from, $where, array($status));
	
	return $objPage;
}

//���ơ���������ι����ʰ�ư��
function lfStatusMove($status_id,$move){
	global $objQuery;
	global $objPage;
	
	if ($status_id == 'delete'){
		$sql="UPDATE dtb_order SET del_flg=1";
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
			$objPage->tpl_onload = "window.alert('������ܤ��ư���ޤ�����');";
			}
		}
}

?>
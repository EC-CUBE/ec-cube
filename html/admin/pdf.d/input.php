<?php
/*
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrErr;		// ���顼��å�����������
	var $tpl_recv;		// ���Ͼ���POST��
	var $arrForm;		// �ե����������
	function LC_Page() {
		$this->tpl_recv =  'index.php';
		$this->SHORTTEXT_MAX = STEXT_LEN;
		$this->MIDDLETEXT_MAX = MTEXT_LEN;
		$this->LONGTEXT_MAX = LTEXT_LEN;
		
		$this->arrYear  = array(2007=>"2007",2008=>"2008",2009=>"2009",2010=>"2010",2011=>"2011",2012=>"2012");
		$this->arrMonth = array("01"=>"01","02"=>"02","03"=>"03","04"=>"04","05"=>"05","06"=>"06","07"=>"07","08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12");
		$this->arrDay   = array("01"=>"01","02"=>"02","03"=>"03","04"=>"04","05"=>"05","06"=>"06","07"=>"07","08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20","21"=>"21","22"=>"22","23"=>"23","24"=>"24","25"=>"25","26"=>"26","27"=>"27","28"=>"28","29"=>"29","30"=>"30","31"=>"31");
		$this->arrMode  = array("Ǽ�ʽ�");
		$this->arrDownload = array("�֥饦���˳���","�ե��������¸");
	}
}

$conn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);


// �����ֹ椬���ä��顢���åȤ���
if(sfIsInt($_GET['order_id'])) {
	$objPage->tpl_order_id = $_GET['order_id'];
}

// �����ȥ�򥻥å�
$arrForm['chohyo_title'] = "����夲���ٽ�(Ǽ�ʽ�)";

// ���������դ򥻥å�
$arrForm['year']  = date("Y");
$arrForm['month'] = date("m");
$arrForm['day']   = date("d");

// ��å�����
$arrForm['chohyo_msg1'] = '���Τ��ӤϤ���夲�����������꤬�Ȥ��������ޤ���';
$arrForm['chohyo_msg2'] = '���������Ƥˤ�Ǽ�ʤ����Ƥ��������ޤ���';
$arrForm['chohyo_msg3'] = '����ǧ���������ޤ��褦�����ꤤ�������ޤ���';

$objPage->arrForm = $arrForm;

// �������ܤ������������å��Ѥ�uniqid��������
$objPage->tpl_uniqid = $objSess->getUniqId();

// �ƥ�ץ졼�����ѿ��γ������
$objView->assignobj($objPage);
$objView->display('pdf.d/input.tpl');
?>
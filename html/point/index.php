<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = '/css/layout/point/index.css';	// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'point/index.tpl';		// �ᥤ��ƥ�ץ졼��
		$this->tpl_page_category = 'point';				
		$this->tpl_title = '�ݥ�������٤ˤĤ���';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();

$arrInfo =$objQuery->select("*","dtb_baseinfo");
$objPage->arrPoint = $arrInfo[0]['point_rate'];

if ($arrInfo[0]['welcome_point'] != 0){
	$kome = "��";
	$mes = "�����Ͽ��������Ǥ��ʤ�".$arrInfo[0]['welcome_point']."�ݥ������Ϳ����ޤ���";
	$objPage->mes = $mes;
	$objPage->kome = $kome;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------
?>

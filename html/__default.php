<?php
$PHP_DIR = realpath(dirname( __FILE__));
require_once($PHP_DIR . "require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_css = '/css/layout/contact/index.css';	// �ᥤ��CSS�ѥ�
		
	}
}


sfprintr($PHP_DIR);


$objPage = new LC_Page();
$objView = new SC_SiteView();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage);

// ���̤�ɽ��
 $objView->assignobj($objPage);
 $objView->display(SITE_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------------

?>
<?php
/**
 * ��Х��륵����/�ȥåץڡ���
 */

require_once('./require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'top.tpl';	// �ᥤ��ƥ�ץ졼��
	}
}

$objPage = new LC_Page();
$conn = new SC_DBConn();
$objCustomer = new SC_Customer();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, 'index.php');

$objView = new SC_SiteView();
$objView->assign("isLogin", $objCustomer->isLoginSuccess());
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
?>

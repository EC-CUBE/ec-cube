<?php
require_once("../../../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = '../../../css/test.css';	// �ᥤ��CSS�ѥ�
		$this->tpl_mainpage = 'test/kakinaka/file/index.tpl';		// �ᥤ��ƥ�ץ졼��
	}
}
$objPage = new LC_Page();
$objView = new SC_SiteView();

if ($_POST['css_main'] == ""){
	$fp = fopen($objPage->tpl_css, "r");
	$css = fread($fp, filesize($objPage->tpl_css));
}else{
	$fp = fopen($objPage->tpl_css, "w");
	fwrite($fp, $_POST['css_main']);
}

$objPage->css = $css;

fclose($fp);

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
class LC_NewsPage {
	function LC_NewsPage() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = BLOC_PATH . 'news.tpl';	// �ᥤ��
	}
}

$objSubPage = new LC_NewsPage();
$objSubView = new SC_SiteView();

//����������
$objSubPage->arrNews = lfGetNews();

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
function lfGetNews(){
	$conn = new SC_DBConn();
	$sql = "SELECT *, cast(substring(news_date,1,10) as date) as news_date_disp FROM dtb_news WHERE del_flg = '0' ORDER BY rank DESC";
	$list_data = $conn->getAll($sql);
	return $list_data;	
}
?>
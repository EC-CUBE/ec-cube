<?php

class LC_NewsPage {
	function LC_NewsPage() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = ROOT_DIR . BLOC_DIR.'news.tpl';	// �ᥤ��
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
	$sql = "SELECT *, to_char(news_date, 'YYYY/MM/DD') as news_date_disp FROM dtb_news WHERE del_flg = '0' ORDER BY rank DESC";
	$list_data = $conn->getAll($sql);
	return $list_data;	
}
?>
<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//�������ʤ��ɤ߹���
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = "rss/index.tpl";
		$this->encode = "UTF-8";
		$this->description = "�������";
	}
}

$objQuery = new SC_Query();
$objPage = new LC_Page();
$objView = new SC_SiteView();

//�����������
$arrNews = lfGetNews($objQuery);

//����å��夷�ʤ�(ǰ�Τ���)
header("Paragrama: no-cache");

//XML�ƥ�����(���줬�ʤ��������RSS�Ȥ���ǧ�����Ƥ���ʤ��ġ��뤬���뤿��)
header("Content-type: application/xml");

//�������򥻥å�
$objPage->arrNews = $arrNews;
$objPage->timestamp = sf_mktime("r", $arrNews[0]['HOUR'], $arrNews[0]['MINUTE'], $arrNews[0]['SECOND'], $arrNews[0]['MONTH'], $arrNews[0]['DAY'], $arrNews[0]['YEAR']);

//Ź̾�򥻥å�
$objPage->site_title = $arrNews[0]['shop_name'];

//��ɽEmail���ɥ쥹�򥻥å�
$objPage->email = $arrNews[0]['email'];

//���åȤ����ǡ�����ƥ�ץ졼�ȥե�����˽���
$objView->assignobj($objPage);

//����ɽ��
$objView->display($objPage->tpl_mainpage, true);

//---------------------------------------------------------------------------------------------------------------------
/***************************************************************************************************************
 * �ؿ�̾:lfGetNews
 * ������:���������������
 * ������:$objQuery		DB���饹
 * �����:$arrNews		������̤�������֤�
 ***************************************************************************************************************/
function lfGetNews($objQuery){
	$col = "";
	$col .= "     news_id ";								//�������ID
	$col .= "     ,news_title ";							//������󥿥��ȥ�
	$col .= "     ,news_comment ";							//���������ʸ
	
	if (DB_TYPE == "pgsql") {
		$col .= "     ,to_char(news_date, 'YYYY') AS YEAR ";	//����(ǯ)
		$col .= "     ,to_char(news_date, 'MM') AS MONTH ";		//����(��)
		$col .= "     ,to_char(news_date, 'DD') AS DAY ";		//����(��)
		$col .= "     ,to_char(news_date, 'HH24') AS HOUR ";	//����(����)
		$col .= "     ,to_char(news_date, 'MI') AS MINUTE ";	//����(ʬ)
		$col .= "     ,to_char(news_date, 'SS') AS SECOND ";	//����(��)
	}else if (DB_TYPE == "mysql") {
		$col .= "     ,DATE_FORMAT(news_date, '%Y') AS YEAR ";		//����(ǯ)
		$col .= "     ,DATE_FORMAT(news_date, '%m') AS MONTH ";		//����(��)
		$col .= "     ,DATE_FORMAT(news_date, '%d') AS DAY ";		//����(��)
		$col .= "     ,DATE_FORMAT(news_date, '%H') AS HOUR ";		//����(����)
		$col .= "     ,DATE_FORMAT(news_date, '%i') AS MINUTE ";	//����(ʬ)
		$col .= "     ,DATE_FORMAT(news_date, '%s') AS SECOND ";	//����(��)
	}
	$col .= "     ,news_url ";								//�������URL
	$col .= "     ,news_select ";							//�������ζ�ʬ(1:URL��2:��ʸ)
	$col .= "     ,(SELECT shop_name FROM dtb_baseinfo limit 1) AS shop_name  ";	//Ź̾
	$col .= "     ,(SELECT email04 FROM dtb_baseinfo limit 1) AS email ";			//��ɽEmail���ɥ쥹
	$from = "dtb_news";
	$where = "del_flg = '0'";
	$order = "rank DESC";
	$objQuery->setorder($order);
	$arrNews = $objQuery->select($col,$from,$where);
	return $arrNews;
}

?>
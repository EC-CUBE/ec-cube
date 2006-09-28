<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//共通部品の読み込み
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = "rss/index.tpl";
		$this->encode = "UTF-8";
		$this->description = "新着情報";
	}
}

$objQuery = new SC_Query();
$objPage = new LC_Page();
$objView = new SC_SiteView();

//新着情報を取得
$arrNews = lfGetNews($objQuery);

//キャッシュしない(念のため)
header("Paragrama: no-cache");

//XMLテキスト(これがないと正常にRSSとして認識してくれないツールがあるため)
header("Content-type: application/xml");

//新着情報をセット
$objPage->arrNews = $arrNews;		

//店名をセット
$objPage->site_title = $arrNews[0]['shop_name'];

//代表Emailアドレスをセット
$objPage->email = $arrNews[0]['email'];

//DESCRIPTIONをセット
$objPage->description = $objPage->description;

//XMLファイルのエンコードをセット
$objPage->encode = $objPage->encode;

//セットしたデータをテンプレートファイルに出力
$objView->assignobj($objPage);

//画面表示
$objView->display($objPage->tpl_mainpage, true);

//******************************************************************************************/
/*
 * 関数名:lfGetNews
 * 説明　:新着情報を取得する
 * 引数１:$objQuery		DB操作クラス
 * 戻り値:$arrNews		取得結果を配列で返す
 */
function lfGetNews($objQuery){
	$col = "";
	$col .= "     news_id ";								//新着情報ID
	$col .= "     ,news_title ";								//新着情報タイトル
	$col .= "     ,news_comment ";							//新着情報本文
	$col .= "     ,to_char(news_date, 'YYYY') AS YEAR ";	//日付(年)
	$col .= "     ,to_char(news_date, 'MM') AS MONTH ";		//日付(月)
	$col .= "     ,to_char(news_date, 'DD') AS DAY ";		//日付(日)
	$col .= "     ,to_char(news_date, 'HH24') AS HOUR ";	//日付(時間)
	$col .= "     ,to_char(news_date, 'MI') AS MINUTE ";	//日付(分)
	$col .= "     ,to_char(news_date, 'SS') AS SECOND ";		//日付(秒)
	$col .= "     ,news_url ";								//新着情報URL
	$col .= "     ,news_select ";							//新着情報の区分(1:URL、2:本文)
	$col .= "     ,(SELECT shop_name FROM dtb_baseinfo limit 1) AS shop_name  ";	//店名
	$col .= "     ,(SELECT email04 FROM dtb_baseinfo limit 1) AS email ";			//代表Emailアドレス
	$from = "dtb_news";
	$where = "del_flg = '0'";
	$order = "rank DESC";
	$objQuery->setorder($order);
	$arrNews = $objQuery->select($col,$from,$where);
	return $arrNews;
}
    
?>
<?php

require_once("../require.php");
require_once("./inc_mailmagazine.php");

class LC_Page {
	var $arrSession;
	var $arrHtmlmail;
	var $arrNowDate;
	function LC_Page() {
		$this->tpl_mainpage = 'mail/index.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "index";
		$this->tpl_pager = ROOT_DIR . 'data/Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '配信内容設定';
		
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrJob;
		$arrJob["不明"] = "不明";
		$this->arrJob = $arrJob;
		global $arrSex;		
		$this->arrSex = $arrSex;
		global $arrPageRows;
		$this->arrPageRows = $arrPageRows;
		// ページナビ用
		$this->tpl_pageno = $_POST['search_pageno'];
		global $arrMAILMAGATYPE;
		$this->arrMAILMAGATYPE = $arrMAILMAGATYPE;
		$this->arrHtmlmail[''] = "すべて";
		$this->arrHtmlmail[1] = $arrMAILMAGATYPE[1];
		$this->arrHtmlmail[2] = $arrMAILMAGATYPE[2];
		global $arrCustomerType;
		$this->arrCustomerType = $arrCustomerType;
	}
}

class LC_HTMLtemplate {
	var $list_data;
}

//---- ページ初期設定
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date();
$objPage->objDate = $objDate;
$objPage->arrTemplate = getTemplateList($conn);

$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

/*
	query:配信履歴「確認」
*/
if ($_GET["mode"] == "query" && sfCheckNumLength($_GET["send_id"])) {
	// 送信履歴より、送信条件確認画面
	$sql = "SELECT search_data FROM dtb_send_history WHERE send_id = ?";
	$result = $conn->getOne($sql, array($_GET["send_id"]));
	$tpl_path = "mail/query.tpl";
		
	$list_data = unserialize($result);
	
	// 性別の変換
	if (count($list_data['sex']) > 0) {
		foreach($list_data['sex'] as $key => $val){
			$list_data['sex'][$key] = $objPage->arrSex[$val];
			$sex_disp .= $list_data['sex'][$key] . " ";
		}
	}
	
	// 職業の変換
	if (count($list_data['job']) > 0) {
		foreach($list_data['job'] as $key => $val){
			$list_data['job'][$key] = $objPage->arrJob[$val];
		}
	}
	
	
	$objPage->list_data = $list_data;
	sfprintr($objPage->list_data);
	
	$objView->assignobj($objPage);
	$objView->display($tpl_path);
	exit;
}

if($_POST['mode'] == 'delete') {
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_customer_mail", "email = ?", array($_POST['result_email']));
}

switch($_POST['mode']) {
/*
	search:「検索」ボタン
	back:検索結果画面「戻る」ボタン
*/
case 'delete':
case 'search':
case 'back':
	//-- 入力値コンバート
	$objPage->list_data = lfConvertParam($_POST, $arrSearchColumn);
	
	//-- 入力エラーのチェック
	$objPage->arrErr = lfErrorCheck($objPage->list_data);

	//-- 検索開始
	if (!is_array($objPage->arrErr)) {
		$objPage->list_data['name'] = sfManualEscape($objPage->list_data['name']);
		// hidden要素作成
		$objPage->arrHidden = lfGetHidden($objPage->list_data);

		//-- 検索データ取得	
		$objSelect = new SC_CustomerList($objPage->list_data, "magazine");

		// 生成されたWHERE文を取得する		
		list($where, $arrval) = $objSelect->getWhere();
		// 「WHERE」部分を削除する。
		$where = ereg_replace("^WHERE", "", $where);

		// 検索結果の取得
		$objQuery = new SC_Query();
		$from = "dtb_customer_mail LEFT OUTER JOIN dtb_customer USING(email)";

		// 行数の取得
		$linemax = $objQuery->count($from, $where, $arrval);
		$objPage->tpl_linemax = $linemax;				// 何件が該当しました。表示用
		
		// ページ送りの取得
		$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, SEARCH_PMAX, "fnResultPageNavi", NAVI_PMAX);
		$objPage->arrPagenavi = $objNavi->arrPagenavi;	
		$startno = $objNavi->start_row;

		// 取得範囲の指定(開始行番号、行数のセット)
		$objQuery->setlimitoffset(SEARCH_PMAX, $startno);
		// 表示順序
		$objQuery->setorder("customer_id DESC");
		// 検索結果の取得
		$col = "customer_id,name01,name02,kana01,kana02,sex,email,tel01,tel02,tel03,pref,mail_flag";
		$objPage->arrResults = $objQuery->select($col, $from, $where, $arrval);

		//現在時刻の取得
		$objPage->arrNowDate = lfGetNowDate();
	}
	break;
/*
	input:検索結果画面「配信内容設定」ボタン
*/
case 'input':
	//-- 入力値コンバート
	$objPage->list_data = lfConvertParam($_POST, $arrSearchColumn);
	//-- 入力エラーのチェック
	$objPage->arrErr = lfErrorCheck($objPage->list_data);
	//-- エラーなし
	if (!is_array($objPage->arrErr)) {
		//-- 現在時刻の取得
		$objPage->arrNowDate = lfGetNowDate();
		$objPage->arrHidden = lfGetHidden($objPage->list_data); // hidden要素作成
		$objPage->tpl_mainpage = 'mail/input.tpl';
	}
	break;
/*
	template:テンプレート選択
*/
case 'template':
	//-- 入力値コンバート
	$objPage->list_data = lfConvertParam($_POST, $arrSearchColumn);
	
	//-- 時刻設定の取得
	$objPage->arrNowDate['year'] = $_POST['send_year'];
	$objPage->arrNowDate['month'] = $_POST['send_month'];
	$objPage->arrNowDate['day'] = $_POST['send_day'];
	$objPage->arrNowDate['hour'] = $_POST['send_hour'];
	$objPage->arrNowDate['minutes'] = $_POST['send_minutes'];
	
	//-- 入力エラーのチェック
	$objPage->arrErr = lfErrorCheck($objPage->list_data);

	//-- 検索開始
	if ( ! is_array($objPage->arrErr)) {
		$objPage->list_data['name'] = sfManualEscape($objPage->list_data['name']);
		$objPage->arrHidden = lfGetHidden($objPage->list_data); // hidden要素作成
	
		$objPage->tpl_mainpage = 'mail/input.tpl';
		$template_data = getTemplateData($conn, $_POST['template_id']);
		if ( $template_data ){
			foreach( $template_data as $key=>$val ){
				$objPage->list_data[$key] = $val;
			}
		}

		//-- HTMLテンプレートを使用する場合は、HTMLソースを生成してBODYへ挿入
		if ( $objPage->list_data["mail_method"] == 3) {
			$objTemplate = new LC_HTMLtemplate;
			$objTemplate->list_data = lfGetHtmlTemplateData($_POST['template_id']);
			$objSiteInfo = new SC_SiteInfo();
			$objTemplate->arrInfo = $objSiteInfo->data;
			//メール担当写真の表示
			$objUpFile = new SC_UploadFile(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
			$objUpFile->addFile("メール担当写真", 'charge_image', array('jpg'), IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
			$objUpFile->setDBFileList($objTemplate->list_data);
			$objTemplate->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
			$objMakeTemplate = new SC_AdminView();
			$objMakeTemplate->assignobj($objTemplate);		
			$objPage->list_data["body"] = $objMakeTemplate->fetch("mail/html_template.tpl");
		}
	}
	break;
/*
	regist_confirm:「入力内容を確認」
	regist_back:「テンプレート設定画面へ戻る」
	regist_complete:「登録」
*/	
case 'regist_confirm':
case 'regist_back':
case 'regist_complete':
	//-- 入力値コンバート
	$arrCheckColumn = array_merge( $arrSearchColumn, $arrRegistColumn );
	$objPage->list_data = lfConvertParam($_POST, $arrCheckColumn);
	
	//現在時刻の取得
	$objPage->arrNowDate = lfGetNowDate();

	//-- 入力エラーのチェック
	$objPage->arrErr = lfErrorCheck($objPage->list_data, 1);
	$objPage->tpl_mainpage = 'mail/input.tpl';
	$objPage->arrHidden = lfGetHidden($objPage->list_data); // hidden要素作成
	
	//-- 検索開始
	if ( ! is_array($objPage->arrErr)) {
			$objPage->list_data['name'] = sfManualEscape($objPage->list_data['name']);
		if ( $_POST['mode'] == 'regist_confirm'){
			$objPage->tpl_mainpage = 'mail/input_confirm.tpl';
		} else if( $_POST['mode'] == 'regist_complete' ){
			lfRegistData($objPage->list_data);
			header("Location: /admin/mail/sendmail.php?mode=now");
			exit;			
		}
	}
	break;
default:
	break;
}

// 配信時間の年を、「現在年~現在年＋１」の範囲に設定
for ($year=date("Y"); $year<=date("Y") + 1;$year++){
	$arrYear[$year] = $year;
}
$objPage->arrYear = $arrYear;

$objPage->arrCustomerOrderId = lfGetCustomerOrderId($_POST['buy_product_code']);

$objPage->arrCatList = sfGetCategoryList();

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-------------------------------------------------------------------------------------------------------------------------------

// 商品コードで検索された場合にヒットした受注番号を取得する。
function lfGetCustomerOrderId($keyword) {
	if($keyword != "") {
		$col = "customer_id, order_id";
		$from = "dtb_order LEFT JOIN dtb_order_detail USING(order_id)";
		$where = "product_code LIKE ? AND delete = 0";
		$val = sfManualEscape($keyword);
		$arrVal[] = "%$val%";
		$objQuery = new SC_Query();
		$objQuery->setgroupby("customer_id, order_id");
		$arrRet = $objQuery->select($col, $from, $where, $arrVal);
		$arrCustomerOrderId = sfArrKeyValues($arrRet, "customer_id", "order_id");
	}
	return $arrCustomerOrderId;	
}

function lfMakeCsvData( $send_id ){
		
	global $conn;

	$arrTitle  = array(	 'name01','email');
				
	$sql = "SELECT name01,email FROM dtb_send_customer WHERE send_id = ? ORDER BY email";
	$result = $conn->getAll($sql, array($send_id) );
	
	if ( $result ){
		$return = lfGetCSVData( $result, $arrTitle);
	}
	return $return;	
}

//---- CSV出力用データ取得
function lfGetCSVData( $array, $arrayIndex){	
	
	for ($i=0; $i<count($array); $i++){
		
		for ($j=0; $j<count($array[$i]); $j++ ){
			if ( $j > 0 ) $return .= ",";
			$return .= "\"";			
			if ( $arrayIndex ){
				$return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";	
			} else {
				$return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
			}
		}
		$return .= "\n";			
	}
	return $return;
}

//現在時刻の取得（配信時間デフォルト値）
function lfGetNowDate(){
	$nowdate = date("Y/n/j/G/i");
	list($year, $month, $day, $hour, $minute) = split("[/]", $nowdate);
	$arrNowDate = array( 'year' => $year, 'month' => $month, 'day' => $day, 'hour' => $hour, 'minutes' => $minute);
	foreach ($arrNowDate as $key => $val){
		switch ($key){
			case 'minutes':
			$val = ereg_replace('^[0]','', $val);
			if ($val < 30){
			$list_date[$key] = '30';
			}else{
			$list_date[$key] = '00';
			}
			break;
			case 'year':
			case 'month':
			case 'day':
			$list_date[$key] = $val;
			break;
		}
	}
		if ($arrNowDate['minutes'] < 30){
			$list_date['hour'] = $hour;
		}else{
			$list_date['hour'] = $hour + 1;
		}
	return $list_date;
}

// 配信内容と配信リストを書き込む
function lfRegistData($arrData){
	
	global $conn;
	global $arrSearchColumn;
		
	$objSelect = new SC_CustomerList( lfConvertParam($arrData, $arrSearchColumn), "magazine" );
	$search_data = $conn->getAll($objSelect->getListMailMagazine(), $objSelect->arrVal);
	$dataCnt = count($search_data);

	$dtb_send_history = array();
	$dtb_send_history["send_id"] = $conn->getOne("SELECT NEXTVAL('dtb_send_history_send_id_seq')");
	$dtb_send_history["mail_method"] = $arrData['mail_method'];
	$dtb_send_history["subject"] = $arrData['subject'];
	$dtb_send_history["body"] = $arrData['body'];
	$dtb_send_history["start_date"] = "now()";
	$dtb_send_history["creator_id"] = $_SESSION['member_id'];
	$dtb_send_history["send_count"] = $dataCnt;
	$arrData['body'] = "";
	$dtb_send_history["search_data"] = serialize($arrData);
	$conn->autoExecute("dtb_send_history", $dtb_send_history );		
	
	if ( is_array( $search_data ) ){
		foreach( $search_data as $line ){
			$dtb_send_customer = array();
			$dtb_send_customer["customer_id"] = $line["customer_id"];
			$dtb_send_customer["send_id"] = $dtb_send_history["send_id"];
			$dtb_send_customer["email"] = $line["email"];
			
			$dtb_send_customer["name"] = $line["name01"] . " " . $line["name02"];
				
			$conn->autoExecute("dtb_send_customer", $dtb_send_customer );					
		}	
	}	
}

?>
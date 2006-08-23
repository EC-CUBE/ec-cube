<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/point.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'point';
		$this->tpl_mainno = 'basis';
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
	}
}
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();
$objDate = new SC_Date();

// 登録・更新検索開始年
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrStartYear = $objDate->getYear();
$objPage->arrStartMonth = $objDate->getMonth();
$objPage->arrStartDay = $objDate->getDay();
$objPage->arrStartHour = $objDate->getHour();
// 登録・更新検索終了年
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrEndYear = $objDate->getYear();
$objPage->arrEndMonth = $objDate->getMonth();
$objPage->arrEndDay = $objDate->getDay();
$objPage->arrEndHour = $objDate->getHour();

// 認証可否の判定
sfIsSuccess($objSess);

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

$cnt = $objQuery->count("dtb_baseinfo");

if ($cnt > 0) {
	$objPage->tpl_mode = "update";
} else {
	$objPage->tpl_mode = "insert";
}

// 検索ワードの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key) || ereg("^campaign", $key)) {
		switch($key) {
			case 'search_product_flag':
			case 'search_status':
				$objPage->arrSearchHidden[$key] = sfMergeParamCheckBoxes($val);
				break;
			default:
				$objPage->arrSearchHidden[$key] = $val;
				break;
		}
	}
	
}

switch($_POST['mode']) {
//全体ポイント登録
case 'update':
case 'insert':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		switch($_POST['mode']) {
		case 'update':
			lfUpdateData(); // 既存編集
			break;
		case 'insert':
			lfInsertData(); // 新規作成
			break;
		default:
			break;
		}
		// 再表示
		//sfReload();
		$objPage->tpl_onload = "window.alert('ポイント設定が完了しました。');";
	}
	break;
//キャンペーン商品選択ページからの推移
case 'campaign_next':
	$objPage->tpl_mainpage = 'basis/campaign_regist.tpl';
	//編集時
	if(sfIsInt($_POST['campaign_id'])) {
		//キャンペーン情報の取得
		$arrRet = $objQuery->select("*", "dtb_campaign", "delete = 0 AND campaign_id = ? ", array($_POST['campaign_id']));
		$arrCamp = $arrRet[0];
		//キャンペーン期間の取得
		if ($arrCamp['start_date'] != "" && $arrCamp['end_date']){
			$arrSDate = sfDispDBDate($arrCamp['start_date']);
			list($arrCamp['startyear'], $arrCamp['startmonth'], $arrCamp['startday'], $arrCamp['starthour']) = split("[/ :]", $arrSDate);
			$arrEDate = sfDispDBDate($arrCamp['end_date']);
			list($arrCamp['endyear'], $arrCamp['endmonth'], $arrCamp['endday'], $arrCamp['endhour']) = split("[/ :]", $arrEDate);
		}
		$objPage->arrCamp = $arrCamp;
	}
	break;
//キャンペーン登録
case 'campaign_regist':
	$objPage->tpl_mainpage = 'basis/campaign_regist.tpl';
	$objPage->arrCamp = $_POST;
	$objPage->arrCamp['campaign_name'] = mb_convert_kana($objPage->arrCamp['campaign_name'], "KVa");
	$objPage->arrCamp['campaign_point_rate'] = mb_convert_kana($objPage->arrCamp['campaign_point_rate'], "n");
	//エラーチェック
	$objPage->arrErr = lfErrorCheck($objPage->arrCamp);
	if(count($objPage->arrErr) == 0) {
		//キャンペーン登録
		lfRegistCampaign($objPage->arrCamp);
		//ポイント設定ページへ移動
		header("Location: ./point.php");
		exit;
	}
	break;
default:
	$arrCol = $objFormParam->getKeyList(); // キー名一覧を取得
	$col	= sfGetCommaList($arrCol);
	$arrRet = $objQuery->select($col, "dtb_baseinfo");
	// POST値の取得
	$objFormParam->setParam($arrRet[0]);
	//キャンペーンの削除
	if($_POST['mode'] == 'delete') {
		$sqlval['delete'] = '1';
		$sqlval['update_date'] = 'now()';
		$objQuery->begin();
		$objQuery->update("dtb_campaign", $sqlval, "campaign_id = ? ", array($_POST['campaign_id']));
		$objQuery->delete("dtb_campaign_detail", "campaign_id = ? ", array($_POST['campaign_id']));
		$objQuery->commit();
	}
	//キャンペーンデータの取得
	$objPage->arrCampData = lfGetCampaignData();
	// カテゴリの読込
	$objPage->arrCatList = sfGetCategoryList();
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("ポイント付与率", "point_rate", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("会員登録時付与ポイント", "welcome_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
}

function lfUpdateData() {
	global $objFormParam;
	// 入力データを渡す。
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = 'Now()';
	$objQuery = new SC_Query();
	// UPDATEの実行
	$ret = $objQuery->update("dtb_baseinfo", $sqlval);
}

function lfInsertData() {
	global $objFormParam;
	// 入力データを渡す。
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = 'Now()';
	$objQuery = new SC_Query();
	// INSERTの実行
	$ret = $objQuery->insert("dtb_baseinfo", $sqlval);
}

//登録済みキャンペーンの取得
function lfGetCampaigndata() {
	$objQuery = new SC_Query;
	//登録日付順に並べる
	$objQuery->setorder('update_date DESC');
	$arrData = $objQuery->select("*", "dtb_campaign", "delete = 0");
	for($i = 0; $i < count($arrData); $i++) {
		if ($arrData[$i]['search_condition'] != "") {
			$arrRet[$i] = unserialize($arrData[$i]['search_condition']);
			foreach($arrRet[$i] as $key => $val) {
				switch($key) {
				case 'search_product_flag':
				case 'search_status':
					$arrData[$i][$key] = split("-", $val);
					break;
				default:
					$arrData[$i][$key] = $val;
					break;
				}
			}
		}
	}
	return $arrData;
}

//---- 入力エラーチェック
function lfErrorCheck($array) {
	
	foreach($array as $key => $val) {
		if(!ereg("^search", $key)) {
			$arrRet[$key] = $val;
		}
	}
	
	$objErr = new SC_CheckError($arrRet);
	
	$objErr->doFunc(array("開始日(年)", "startyear"), array("SELECT_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("開始日(月)", "startmonth"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("開始日(日)", "startday"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("開始日(時)", "starthour"), array("SELECT_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("終了日(年)", "endyear"), array("SELECT_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("終了日(月)", "endmonth"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("終了日(日)", "endday"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("終了日(時)", "endhour"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("開始日", "startyear", "startmonth", "startday", "starthour"), array("CHECK_DATE2"));
	$objErr->doFunc(array("終了日", "endyear", "endmonth", "endday", "endhour"), array("CHECK_DATE2"));	
	$objErr->doFunc(array("開始日","終了日", "startyear", "startmonth", "startday", "starthour", "endyear", "endmonth", "endday", "endhour"), array("CHECK_SET_TERM2"));
	$objErr->doFunc(array("キャンペーン名", "campaign_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("キャンペーンポイント付与率", "campaign_point_rate", INT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));	
	
	return $objErr->arrErr;
}

//キャンペーン登録
function lfRegistCampaign($array) {
	$objQuery = new SC_Query;
	
	$objQuery->begin();
	$sqlval['campaign_name'] = $array['campaign_name'];
	$sqlval['campaign_point_rate'] = $array['campaign_point_rate'];
	$sqlval['start_date'] = $array['startyear']."-".$array['startmonth']."-".$array['startday']." ".$array['starthour'].":00:00";
	$sqlval['end_date'] = $array['endyear']."-".$array['endmonth']."-".$array['endday']." ".$array['endhour'].":00:00";
	//検索条件を格納するキーを指定する
	foreach($array as $key => $val) {
		//ページNOは格納しない
		if(ereg("^search", $key) && !ereg("^search_page", $key)) {
			$arrRet[$key] = $val;
		}
	}
	$sqlval['search_condition'] = serialize($arrRet);
	$sqlval['create_date'] = 'now()';
	//編集時
	if(sfIsInt($array['campaign_id'])) {
		$sqlval['update_date'] = 'now()';
		//更新
		$objQuery->update("dtb_campaign", $sqlval, "campaign_id = ?", array($array['campaign_id']));
		//詳細テーブルを削除
		$objQuery->delete("dtb_campaign_detail", "campaign_id = ? ", array($array['campaign_id']));
		$sqlvaldet['campaign_id'] = $array['campaign_id'];
	} else {
		//新規登録
		$campaign_id = $objQuery->nextval("dtb_campaign", "campaign_id");
		$sqlval['campaign_id'] = $campaign_id;
		$objQuery->insert("dtb_campaign", $sqlval);
		$sqlvaldet['campaign_id'] = $campaign_id;
	}
	$sqlvaldet['campaign_point_rate'] = $array['campaign_point_rate'];
	//キャンペーン商品IDを配列に格納
	$arrID = explode("-", $array['campaign_product_id']);
	foreach($arrID as $val) {
		$sqlvaldet['product_id'] = $val;
		$objQuery->insert("dtb_campaign_detail", $sqlvaldet);
	}
	$objQuery->commit();
}

<?php
require_once("../require.php");
require_once("./index_sub.php");
require_once("../batch/daily.php");

require_once("./class/SC_GraphPie.php");
require_once("./class/SC_GraphLine.php");
require_once("./class/SC_GraphBar.php");

class LC_Page {
	var $arrResults;
	var $keyname;
	var $tpl_image;
	var $arrTitle;
	function LC_Page() {
		$this->tpl_mainpage = 'total/index.tpl';
		$this->tpl_subnavi = 'total/subnavi.tpl';
		$this->tpl_graphsubtitle = 'total/subtitle.tpl';
		$this->tpl_titleimage = '/img/title/title_sale.jpg';
		$this->tpl_mainno = 'total';
		global $arrWDAY;
		$this->arrWDAY = $arrWDAY;
		// ページタイトル
		$this->arrTitle[''] = "期間別集計";
		$this->arrTitle['term'] = "期間別集計";
		$this->arrTitle['products'] = "商品別集計";
		$this->arrTitle['age'] = "年代別集計";
		$this->arrTitle['job'] = "職業別集計";
		$this->arrTitle['member'] = "会員別集計";
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
// 認証可否の判定
sfIsSuccess($objSess);

// 入力期間をセッションに記録する
lfSaveDateSession();

// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
$objFormParam->setParam($_POST);
$objFormParam->setParam($_GET);

// 検索ワードの引き継ぎ
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrHidden[$key] = $val;		
	}
}

switch($_POST['mode']) {
case 'pdf':
case 'csv':
case 'search':
	// 入力値の変換
	$objFormParam->convParam();
	$objPage->arrErr = lfCheckError($arrRet);
	$arrRet = $objFormParam->getHashArray();
	
	// 入力エラーなし
	if (count($objPage->arrErr) == 0) {
		foreach ($arrRet as $key => $val) {
			if($val == "") {
				continue;
			}
			switch ($key) {
			case 'search_startyear':
				$sdate = $_POST['search_startyear'] . "/" . $_POST['search_startmonth'] . "/" . $_POST['search_startday'];
				break;
			case 'search_endyear':
				$edate = $_POST['search_endyear'] . "/" . $_POST['search_endmonth'] . "/" . $_POST['search_endday'];
				break;
			case 'search_startyear_m':
				list($sdate, $edate) = sfTermMonth($_POST['search_startyear_m'], $_POST['search_startmonth_m'], CLOSE_DAY);
				break;
			default:
				break;
			}
		}

		if($_POST['type'] != "") {
			$type = $_POST['type'];
		}
				
		$page = $objFormParam->getValue('page');
		switch($page) {
		// 商品別集計
		case 'products':
			if($type == "") {
				$type = 'all';
			}
			$objPage->tpl_page_type = "total/page_products.tpl";
			// 未集計データの集計を行う
			lfRealTimeDailyTotal($sdate, $edate);
			// 検索結果の取得
			$objPage = lfGetOrderProducts($type, $sdate, $edate, $objPage);
			break;
		// 職業別集計
		case 'job':
			if($type == "") {
				$type = 'all';
			}
			$objPage->tpl_page_type = "total/page_job.tpl";
			// 未集計データの集計を行う
			lfRealTimeDailyTotal($sdate, $edate);
			// 検索結果の取得
			$objPage = lfGetOrderJob($type, $sdate, $edate, $objPage);
			break;
		// 会員別集計
		case 'member':
			if($type == "") {
				$type = 'all';
			}
			$objPage->tpl_page_type = "total/page_member.tpl";
			// 未集計データの集計を行う
			lfRealTimeDailyTotal($sdate, $edate);
			// 検索結果の取得
			$objPage = lfGetOrderMember($type, $sdate, $edate, $objPage);
			break;
		// 年代別集計
		case 'age':
			if($type == "") {
				$type = 'all';
			}
			$objPage->tpl_page_type = "total/page_age.tpl";
			// 未集計データの集計を行う
			lfRealTimeDailyTotal($sdate, $edate);
			// 検索結果の取得
			$objPage = lfGetOrderAge($type, $sdate, $edate, $objPage);
			break;
		// 期間別集計
		default:
			if($type == "") {
				$type = 'day';
			}
			$objPage->tpl_page_type = "total/page_term.tpl";
			// 未集計データの集計を行う
			lfRealTimeDailyTotal($sdate, $edate);
			// 検索結果の取得
			$objPage = lfGetOrderTerm($type, $sdate, $edate, $objPage);
			
			break;
		}

		if($_POST['mode'] == 'csv') {
			// CSV出力タイトル行の取得
			list($arrTitleCol, $arrDataCol) = lfGetCSVColum($page, $objPage->keyname);
			$head = sfGetCSVList($arrTitleCol);
			$data = lfGetDataColCSV($objPage->arrResults, $arrDataCol);
			// CSVを送信する。
			sfCSVDownload($head.$data, $page."_".$type);
			exit;
		}
		
		if($_POST['mode'] == 'pdf') {
			// CSV出力タイトル行の取得
			list($arrTitleCol, $arrDataCol, $arrColSize, $arrAlign, $title) = lfGetPDFColum($page, $type, $objPage->keyname);
			$head = sfGetPDFList($arrTitleCol);
			$data = lfGetDataColPDF($objPage->arrResults, $arrDataCol, 40);
			// PDF出力用
			$graph_name = basename($objPage->tpl_image);
			lfPDFDownload($graph_name, $head . $data, $arrColSize, $arrAlign, $sdate, $edate, $title);
			exit;	
		}	
	}
	break;
default:
	if(count($_GET) == 0) {
		/*
			リアルタイム集計に切り替え by Nakagawa 2006/08/31
			// 1ヶ月分の集計
			lfStartDailyTotal(31,0);
		*/
	}
	break;
}

// 登録・更新日検索用
$objDate = new SC_Date();
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrYear = $objDate->getYear();
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();
// 入力値の取得
$objPage->arrForm = $objFormParam->getFormParamList();

$objPage->tpl_subtitle = $objPage->arrTitle[$objFormParam->getValue('page')];

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------
/* PDF出力 */
function lfPDFDownload($image, $table, $arrColSize, $arrAlign, $sdate, $edate, $title) {
	
	$objPdf = new SC_Pdf();
	$objPdf->setTableColor("CCCCCC", "F0F0F0", "D1DEFE");
			
	// 土台となるPDFファイルの指定
	$objPdf->setTemplate(PDF_DIR . "total.pdf");

	$disp_sdate = sfDispDBDate($sdate, false);
	$disp_edate = sfDispDBDate($edate, false);
				
	$arrText['title_block'] = $title;
	$arrText['date_block'] = "$disp_sdate-$disp_edate";
	$arrImage['graph_block'] = GRAPH_DIR . $image;
	
	// 文末の\nを削除する
	$table = ereg_replace("\n$", "", $table);
	$arrRet = split("\n", $table);
	$page_max = intval((count($arrRet) / 35) + 1);
	
	for($page = 1; $page <= $page_max; $page++) {
		if($page > 1) {
			// 2ページ以降
			$start_no = 35 * ($page - 1) + 1;
		} else {
			// 開始ページ
			$start_no = 1;			
		}
				
		$arrText['page_block'] = $page . " / " . $page_max;
		$objPdf->setTextBlock($arrText);
		$objPdf->setImageBlock($arrImage);
		// ブロック値の入力
		$objPdf->writeBlock();
		// 最終ページのみ、商品別集計は合計がないので最終行の色を変更しない。
		if($page == $page_max && $_POST['page'] != 'products') {
			$last_color_flg = true;
		} else {
			$last_color_flg = false;
		}	
		$objPdf->writeTableCenter($table, 500, $arrColSize, $arrAlign, 35, $start_no, $last_color_flg);
		$objPdf->closePage();
	}

	// PDFの出力
	$objPdf->output();	
}

/* セッションに入力期間を記録する */
function lfSaveDateSession() {
	if($_POST['form'] == 1) {
		$_SESSION['total']['startyear_m'] = $_POST['search_startyear_m'];
		$_SESSION['total']['startmonth_m'] = $_POST['search_startmonth_m'];
	}
	
	if($_POST['form'] == 2) {
		$_SESSION['total']['startyear'] = $_POST['search_startyear'];
		$_SESSION['total']['startmonth'] = $_POST['search_startmonth'];
		$_SESSION['total']['startday'] = $_POST['search_startday'];
		$_SESSION['total']['endyear'] = $_POST['search_endyear'];
		$_SESSION['total']['endmonth'] = $_POST['search_endmonth'];
		$_SESSION['total']['endday'] = $_POST['search_endday'];
	}
}

/* デフォルト値の取得 */
function lfGetDateDefault() {
	$year = date("Y");
	$month = date("m");
	$day = date("d");
	
	$list = $_SESSION['total'];
	
	// セッション情報に開始月度が保存されていない。
	if($_SESSION['total']['startyear_m'] == "") {
		$list['startyear_m'] = $year;
		$list['startmonth_m'] = $month;
	}
	
	// セッション情報に開始日付、終了日付が保存されていない。
	if($_SESSION['total']['startyear'] == "" && $_SESSION['total']['endyear'] == "") {
		$list['startyear'] = $year;
		$list['startmonth'] = $month;
		$list['startday'] = $day;
		$list['endyear'] = $year;
		$list['endmonth'] = $month;
		$list['endday'] = $day;
	}
	
	return $list;	
}

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
		
	// デフォルト値の取得
	$arrList = lfGetDateDefault();
	
	// 月度集計
	$objFormParam->addParam("月度", "search_startyear_m", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startyear_m']);
	$objFormParam->addParam("月度", "search_startmonth_m", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startmonth_m']);
	// 期間集計
	$objFormParam->addParam("開始日", "search_startyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startyear']);
	$objFormParam->addParam("開始日", "search_startmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startmonth']);
	$objFormParam->addParam("開始日", "search_startday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startday']);
	$objFormParam->addParam("終了日", "search_endyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endyear']);
	$objFormParam->addParam("終了日", "search_endmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endmonth']);
	$objFormParam->addParam("終了日", "search_endday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endday']);
	
	// hiddenデータの取得用
	$objFormParam->addParam("", "page");
	$objFormParam->addParam("", "type");
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError();
	
	// 特殊項目チェック
	if($_POST['form'] == 1) {
		$objErr->doFunc(array("月度", "search_startyear_m"), array("ONE_EXIST_CHECK"));
	}
	
	if($_POST['form'] == 2) {
		$objErr->doFunc(array("期間", "search_startyear", "search_endyear"), array("ONE_EXIST_CHECK"));
	}
			
	$objErr->doFunc(array("月度", "search_startyear_m", "search_startmonth_m"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("開始日", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
	$objErr->doFunc(array("終了日", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
	$objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
	return $objErr->arrErr;
}

/* 折れ線グラフの作成 */
function lfGetGraphLine($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate) {
	
	$ret_path = "";
	
	// 結果が0行以上ある場合のみグラフを生成する。
	if(count($arrResults) > 0) {
		
		// グラフの生成
		$arrList = sfArrKeyValue($arrResults, $keyname, "total");

		// 一時ファイル名の取得
		$pngname = lfGetGraphPng($type);
		
		$path = GRAPH_DIR . $pngname;
		
		// ラベル表示インターバルを求める
		$interval = intval(count($arrList) / 20);
		if($interval < 1) {
			$interval = 1;
		}
		$objGraphPie = new SC_GraphPie();
		$objGraphLine = new SC_GraphLine();
		
		// 値のセット
		$objGraphLine->setData($arrList);
		$objGraphLine->setXLabel(array_keys($arrList));
		
		// ラベル回転(日本語不可)
		if($keyname == "key_day"){
			$objGraphLine->setXLabelAngle(45);
		}

		// タイトルセット
		$objGraphLine->setXTitle($xtitle);
		$objGraphLine->setYTitle($ytitle);
		
		// メインタイトル作成
		list($sy, $sm, $sd) = split("[/ ]" , $sdate);
		list($ey, $em, $ed) = split("[/ ]" , $edate);
		$start_date = $sy . "年" . $sm . "月" . $sd . "日";
		$end_date = $ey . "年" . $em . "月" . $ed . "日";
		$objGraphLine->drawTitle("集計期間：" . $start_date . " - " . $end_date);
		
		// グラフ描画
		$objGraphLine->drawGraph();
		$objGraphLine->outputGraph(false, $path);

		// ファイルパスを返す
		$ret_path = GRAPH_URL . $pngname;
	}
	return $ret_path;
}

// 円グラフの作成 
function lfGetGraphPie($arrResults, $keyname, $type, $title = "", $sdate = "", $edate = "") {
	
	$ret_path = "";
	
	// 結果が0行以上ある場合のみグラフを生成する。
	if(count($arrResults) > 0) {
		// グラフの生成
		$arrList = sfArrKeyValue($arrResults, $keyname, "total", GRAPH_PIE_MAX, GRAPH_LABEL_MAX);
		
		// 一時ファイル名の取得
		$pngname = lfGetGraphPng($type);
		$path = GRAPH_DIR . $pngname;
		
			$objGraphPie = new SC_GraphPie();
			
			/* デバッグ表示用 by naka
			foreach($arrList as $key => $val) {
				$objGraphPie->debugPrint("key:$key val:$val");
			}
			*/
			
			// データをセットする
			$objGraphPie->setData($arrList);
			// 凡例をセットする
			$objGraphPie->setLegend(array_keys($arrList));
									
			// メインタイトル作成
			list($sy, $sm, $sd) = split("[/ ]" , $sdate);
			list($ey, $em, $ed) = split("[/ ]" , $edate);
			$start_date = $sy . "年" . $sm . "月" . $sd . "日";
			$end_date = $ey . "年" . $em . "月" . $ed . "日";
			$objGraphPie->drawTitle("集計期間：" . $start_date . " - " . $end_date);
					
			// 円グラフ描画
			$objGraphPie->drawGraph();
			
			// グラフの出力
			$objGraphPie->outputGraph(false, $path);			

		// ファイルパスを返す
		$ret_path = GRAPH_URL . $pngname;
	}
	return $ret_path;
}

// 棒グラフの作成 
function lfGetGraphBar($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate) {
	$ret_path = "";
	
	// 結果が0行以上ある場合のみグラフを生成する。
	if(count($arrResults) > 0) {
		// グラフの生成
		$arrList = sfArrKeyValue($arrResults, $keyname, "total", GRAPH_PIE_MAX, GRAPH_LABEL_MAX);
		
		// 一時ファイル名の取得
		$pngname = lfGetGraphPng($type);
		$path = GRAPH_DIR . $pngname;
		
			$objGraphBar = new SC_GraphBar();
			
			foreach(array_keys($arrList) as $val) {
				$arrKey[] = ereg_replace("〜", "-", $val);
			}
			
			// グラフ描画
			$objGraphBar->setXLabel($arrKey);
			$objGraphBar->setXTitle($xtitle);
			$objGraphBar->setYTitle($ytitle);
			$objGraphBar->setData($arrList);
			
			// メインタイトル作成
			$arrKey = array_keys($arrList);
			list($sy, $sm, $sd) = split("[/ ]" , $sdate);
			list($ey, $em, $ed) = split("[/ ]" , $edate);
			$start_date = $sy . "年" . $sm . "月" . $sd . "日";
			$end_date = $ey . "年" . $em . "月" . $ed . "日";
			$objGraphBar->drawTitle("集計期間：" . $start_date . " - " . $end_date);
			
			$objGraphBar->drawGraph();
			$objGraphBar->outputGraph(false,$path);

		// ファイルパスを返す
		$ret_path = GRAPH_URL . $pngname;
	}
	return $ret_path;
}

// グラフ用のPNGファイル名 
function lfGetGraphPng($keyname) {
	if($_POST['search_startyear_m'] != "") {
		$pngname = sprintf("%s_%02d%02d.png", $keyname, substr($_POST['search_startyear_m'],2), $_POST['search_startmonth_m']);
	} else {
		$pngname = sprintf("%s_%02d%02d%02d_%02d%02d%02d.png", $keyname, substr($_POST['search_startyear'], 2), $_POST['search_startmonth'], $_POST['search_startday'], substr($_POST['search_endyear'],2), $_POST['search_endmonth'], $_POST['search_endday']);
	}
	return $pngname;
}

// 会員、非会員集計のWHERE分の作成
function lfGetWhereMember($col_date, $sdate, $edate, $type, $col_member = "customer_id") {
	// 取得日付の指定
	if($sdate != "") {
		if ($where != "") {
			$where.= " AND ";
		}			
		$where.= " $col_date >= '". $sdate ."'";
	}
		
	if($edate != "") {
		if ($where != "") {
			$where.= " AND ";
		}
		$edate = date("Y/m/d",strtotime("1 day" ,strtotime($edate)));	
		$where.= " $col_date < date('" . $edate ."')";
	}
	
	// 会員、非会員の判定
	switch($type) {
	// 全体
	case 'all':
		break;
	case 'member':
		if ($where != "") {
			$where.= " AND ";
		}
		$where.= " $col_member <> 0";
		break;
	case 'nonmember':
		if ($where != "") {
			$where.= " AND ";
		}
		$where.= " $col_member = 0";
		break;
	default:
		break;
	}
	
	return array($where, $arrval);
}

/** 会員別集計 **/
function lfGetOrderMember($type, $sdate, $edate, $objPage, $graph = true) {
	global $arrSex;
		
	list($where, $arrval) = lfGetWhereMember('create_date', $sdate, $edate, $type);
	
	// 会員集計の取得
	$col = "COUNT(*) AS order_count, SUM(total) AS total, (AVG(total)) AS total_average, order_sex";
	$from = "dtb_order";
	$objQuery = new SC_Query();
	$objQuery->setGroupBy("order_sex");
	
	$tmp_where = $where . " AND customer_id <> 0 AND del_flg = 0 ";
	$arrRet = $objQuery->select($col, $from, $tmp_where, $arrval);
	
	// 会員購入であることを記録する。
	$max = count($arrRet);
	for($i = 0; $i < $max; $i++) {
		$arrRet[$i]['member_name'] = '会員'.$arrSex[$arrRet[$i]['order_sex']];
	}
	$objPage->arrResults = $arrRet;
	
	// 非会員集計の取得
	$tmp_where = $where . " AND customer_id = 0 AND del_flg = 0 ";
	$arrRet = $objQuery->select($col, $from, $tmp_where, $arrval);
	// 非会員購入であることを記録する。
	$max = count($arrRet);
	for($i = 0; $i < $max; $i++) {
		$arrRet[$i]['member_name'] = '非会員'.$arrSex[$arrRet[$i]['order_sex']];
	}
	
	$objPage->arrResults = array_merge($objPage->arrResults, $arrRet);
	
	// 円グラフの生成
	if($graph) {	
		$image_key = "member";
		$objPage->tpl_image = lfGetGraphPie($objPage->arrResults, "member_name", $image_key, "(売上比率)", $sdate, $edate);
	}
	
	return $objPage;
}

/** 商品別集計 **/
function lfGetOrderProducts($type, $sdate, $edate, $objPage, $graph = true) {
	list($where, $arrval) = lfGetWhereMember('create_date', $sdate, $edate, $type);
	
	$sql = "SELECT T1.product_id, T1.product_code, T2.name, T1.products_count, T1.order_count, T1.price, T1.total ";
	$sql.= "FROM ( ";
	$sql.= "SELECT product_id, product_code, price, ";
	$sql.= "COUNT(*) AS order_count, ";
	$sql.= "SUM(quantity) AS products_count, ";
	$sql.= "(price * sum(quantity)) AS total ";
	$sql.= "FROM dtb_order_detail WHERE order_id IN (SELECT order_id FROM dtb_order WHERE $where ) ";
	$sql.= "GROUP BY product_id, product_code, price ";
	$sql.= ") ";
	$sql.= "AS T1 LEFT JOIN dtb_products AS T2 USING (product_id) WHERE T2.name IS NOT NULL AND status = 1 ORDER BY T1.total DESC ";
	
	if($_POST['mode'] != "csv") {
		$sql.= "LIMIT " . PRODUCTS_TOTAL_MAX;
	}
	
	$objQuery = new SC_Query();
	$objPage->arrResults = $objQuery->getall($sql, $arrval);
	
	// 円グラフの生成
	if($graph) {
		$image_key = "products_" . $type;
		$objPage->tpl_image = lfGetGraphPie($objPage->arrResults, "name", $image_key, "(売上比率)", $sdate, $edate);
	}
	
	return $objPage;
}

/** 職業別集計 **/
function lfGetOrderJob($type, $sdate, $edate, $objPage, $graph = true) {
	global $arrJob;	
		
	list($where, $arrval) = lfGetWhereMember('T2.create_date', $sdate, $edate, $type);
	
	$sql = "SELECT job, count(*) AS order_count, SUM(total) AS total, (AVG(total)) AS total_average ";
	$sql.= "FROM dtb_customer AS T1 LEFT JOIN dtb_order AS T2 USING ( customer_id ) WHERE $where AND T2.del_flg = 0 ";
	$sql.= "GROUP BY job ORDER BY total DESC";
	
	$objQuery = new SC_Query();
	$objPage->arrResults = $objQuery->getall($sql, $arrval);
			
	$max = count($objPage->arrResults);
	for($i = 0; $i < $max; $i++) {
		$job_key = $objPage->arrResults[$i]['job'];
		if($job_key != "") {
			$objPage->arrResults[$i]['job_name'] = $arrJob[$job_key];
		} else {
			$objPage->arrResults[$i]['job_name'] = "未回答";
		}
	}
	
	// 円グラフの生成	
	if($graph) {
		$image_key = "job_" . $type;
		$objPage->tpl_image = lfGetGraphPie($objPage->arrResults, "job_name", $image_key, "(売上比率)", $sdate, $edate);
	}
	
	return $objPage;
}

/** 年代別集計 **/
function lfGetOrderAge($type, $sdate, $edate, $objPage, $graph = true) {

	list($where, $arrval) = lfGetWhereMember('order_date', $sdate, $edate, $type, "member");
	
	$sql = "SELECT SUM(order_count) AS order_count, SUM(total) AS total, start_age, end_age ";
	$sql.= "FROM dtb_bat_order_daily_age WHERE $where ";
	$sql.= "GROUP BY start_age, end_age ORDER BY start_age, end_age";

	$objQuery = new SC_Query();
	$objPage->arrResults = $objQuery->getall($sql, $arrval);
	
	$max = count($objPage->arrResults);
	for($i = 0; $i < $max; $i++) {
		if($objPage->arrResults[$i]['order_count'] > 0) {
			$objPage->arrResults[$i]['total_average'] = intval($objPage->arrResults[$i]['total'] / $objPage->arrResults[$i]['order_count']);
		}	
		$start_age = $objPage->arrResults[$i]['start_age'];
		$end_age = $objPage->arrResults[$i]['end_age'];
		if($start_age != "" || $end_age != "") {
			if($end_age != 999) {
				$objPage->arrResults[$i]['age_name'] = $start_age . "〜" . $end_age . "歳";
			} else {
				$objPage->arrResults[$i]['age_name'] = $start_age . "歳〜";
			}
		} else {
			$objPage->arrResults[$i]['age_name'] = "未回答";
		}
	}
	
	// 棒グラフの生成
	if($graph) {
		$image_key = "age_" . $type;
		$xtitle = "(年齢)";
		$ytitle = "(売上合計)";
		$objPage->tpl_image = lfGetGraphBar($objPage->arrResults, "age_name", $image_key, $xtitle, $ytitle, $sdate, $edate);
	}
	
	return $objPage;
}

/** 期間別集計 **/
function lfGetOrderTerm($type, $sdate, $edate, $objPage, $graph = true) {
		
		$tmp_col = "sum(total_order) as total_order, sum(men) as men, sum(women) as women,";
		$tmp_col.= "sum(men_member) as men_member, sum(men_nonmember) as men_nonmember,";
		$tmp_col.= "sum(women_member) as women_member, sum(women_nonmember) as women_nonmember,";
		$tmp_col.= "sum(total) as total, (avg(total_average)) as total_average";
		$objQuery = new SC_Query();
		
		switch($type) {
		// 月別
		case 'month':
			$col = $tmp_col . ",key_month";
			$objQuery->setgroupby("key_month");
			$objQuery->setOrder("key_month");
			$objPage->keyname = "key_month";
			$objPage->tpl_tail = "月";
			$from = "dtb_bat_order_daily";
			$xtitle = "(月別)";
			$ytitle = "(売上合計)";
			break;
		// 年別
		case 'year':
			$col = $tmp_col . ",key_year";
			$objQuery->setgroupby("key_year");
			$objQuery->setOrder("key_year");
			$objPage->keyname = "key_year";
			$objPage->tpl_tail = "年";
			$from = "dtb_bat_order_daily";
			$xtitle = "(年別)";
			$ytitle = "(売上合計)";
			break;
		// 曜日別
		case 'wday':
			$col = $tmp_col . ",key_wday, wday";
			$objQuery->setgroupby("key_wday, wday");
			$objQuery->setOrder("wday");
			$objPage->keyname = "key_wday";
			$objPage->tpl_tail = "曜日";
			$from = "dtb_bat_order_daily";
			$xtitle = "(曜日別)";
			$ytitle = "(売上合計)";
			break;
		// 時間別
		case 'hour':
			$col = $tmp_col . ",hour";
			$objQuery->setgroupby("hour");
			$objQuery->setOrder("hour");
			$objPage->keyname = "hour";
			$objPage->tpl_tail = "時";
			$from = "dtb_bat_order_daily_hour";
			$xtitle = "(時間別)";
			$ytitle = "(売上合計)";
			break;
		default:
			$col = "*";
			$objQuery->setOrder("key_day");
			$objPage->keyname = "key_day";
			$from = "dtb_bat_order_daily";
			$xtitle = "(日別)";
			$ytitle = "(売上合計)";
			break;
		}
		

	// 取得日付の指定
		if($sdate != "") {
			if ($where != "") {
				$where.= " AND ";
			}			
			$where.= " order_date >= '". $sdate ."'";
		}
		
		if($edate != "") {
			if ($where != "") {
				$where.= " AND ";
			}
			$edate = date("Y/m/d",strtotime("1 day" ,strtotime($edate)));
			$where.= " order_date < date('" . $edate ."')";
		}
		
		// 検索結果の取得
		$objPage->arrResults = $objQuery->select($col, $from, $where, $arrval);
		
		// 折れ線グラフの生成	
		if($graph) {
			$image_key = "term_" . $type;
			$objPage->tpl_image = lfGetGraphLine($objPage->arrResults, $objPage->keyname, $image_key, $xtitle, $ytitle, $sdate, $edate);
		}
		
		// 検索結果が0でない場合
		if(count($objPage->arrResults) > 0) {
			// 最終集計行取得する
			$col = $tmp_col;
			$objQuery = new SC_Query();
			$arrRet = $objQuery->select($col, $from, $where, $arrval);
			$arrRet[0][$objPage->keyname] = "合計";
			$objPage->arrResults[] = $arrRet[0];
		}

		// 平均値の計算
		$max = count($objPage->arrResults);
		for($i = 0; $i < $max; $i++) {
			if($objPage->arrResults[$i]['total_order'] > 0) {
				$objPage->arrResults[$i]['total_average'] = intval($objPage->arrResults[$i]['total'] / $objPage->arrResults[$i]['total_order']);
			}
		}
		
		return $objPage;
}

?>

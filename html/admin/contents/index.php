<?php

require_once("../require.php");

//---- 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

//---- ページ表示クラス
class LC_Page {
	
	var $arrSession;
	var $tpl_mainpage;
	var $sub_navipage;
	var $regist_data;
	var $arrYear;
	var $arrMonth;
	var $arrDate;
	var $selected_year;
	var $selected_month;
	var $selected_day;
	var $list_data;
	var $max_rank;
	var $edit_mode;
	var $news_title;
	var $news_date_unix;
	var $news_url;
	var $link_method;
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/index.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "index";
		$this->tpl_mainno = 'contents';
		$this->selected_year = date("Y");
		$this->selected_month = date("n");
		$this->selected_day = date("j");
		$this->tpl_subtitle = '新着情報管理';
	}
}


//---- ページ初期設定
$conn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date(ADMIN_NEWS_STARTYEAR);

//----　日付プルダウン設定
$objPage->arrYear = $objDate->getYear();
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();

//----　新規登録/編集登録
if ( $_POST['mode'] == 'regist'){
	$_POST = lfConvData($_POST);

	if ($objPage->arrErr = lfErrorCheck()) {		// 入力エラーのチェック
		foreach($_POST as $key => $val) {
			$objPage->$key = $val;
		}
		$objPage->selected_year = $_POST["year"];
		$objPage->selected_month = $_POST["month"];
		$objPage->selected_day = $_POST["day"];

	} else {
		
		if (isset($_POST['link_method']) == ""){
			$_POST['link_method'] = 1;
		}
		
		$registDate = $_POST['year'] ."/". $_POST['month'] ."/". $_POST['day'];

		//-- 編集登録
		if (strlen($_POST["news_id"]) > 0 && is_numeric($_POST["news_id"])) {

			lfNewsUpdate();

		//--　新規登録
		} else {
			lfNewsInsert();
		}

		$objPage->tpl_onload = "window.alert('編集が完了しました');";
	}
}

//----　編集データ取得
if ($_POST["mode"] == "search" && is_numeric($_POST["news_id"])) {
	$sql = "SELECT *, cast(substring(news_date,1, 10) as date) as cast_news_date FROM dtb_news WHERE news_id = ? ";
	$result = $conn->getAll($sql, array($_POST["news_id"]));
	foreach($result[0] as $key => $val ){
		$objPage->$key = $val;
	}
	$arrData = split("-",$result[0]["cast_news_date"]);
	
	sfprintr($arrData);

	$objPage->selected_year = $arrData[0];
	$objPage->selected_month =$arrData[1];
	$objPage->selected_day =  $arrData[2];

	$objPage->edit_mode = "on";
}

//----　データ削除
if ( $_POST['mode'] == 'delete' && is_numeric($_POST["news_id"])) {

	//-- 削除する新着情報以降のrankを1つ繰り上げておく
	$conn->query("BEGIN");
	$sql = "UPDATE dtb_news SET rank = rank - 1, update_date = NOW() WHERE del_flg = 0 AND rank > ( SELECT rank FROM dtb_news WHERE del_flg = 0 AND news_id = ? )";
	$conn->query( $sql, array( $_POST['news_id']  ) );

	$sql = "UPDATE dtb_news SET rank = 0, del_flg = 1, update_date = NOW() WHERE news_id = ?";
	$conn->query( $sql, array( $_POST['news_id'] ) );
	$conn->query("COMMIT");

	sfReload();				//自分にリダイレクト（再読込による誤動作防止）
}

//----　表示順位移動

if ( $_POST['mode'] == 'move' && is_numeric($_POST["news_id"]) ) {
	if ($_POST["term"] == "up") {
		sfRankUp("dtb_news", "news_id", $_POST["news_id"]);
	} else if ($_POST["term"] == "down") {
		sfRankDown("dtb_news", "news_id", $_POST["news_id"]);	
	}
	//sf_rebuildIndex($conn);
	sfReload();
}

//----　指定表示順位移動
if ($_POST['mode'] == 'moveRankSet') {
	$key = "pos-".$_POST['news_id'];
	$input_pos = mb_convert_kana($_POST[$key], "n");
	if(sfIsInt($input_pos)) {
		sfMoveRank("dtb_news", "news_id", $_POST['news_id'], $input_pos);
		sfReload();
	}
}


//---- 全データ取得
//$sql = "SELECT *, EXTRACT(EPOCH FROM news_date) as news_date_unix FROM dtb_news WHERE del_flg = '0' ORDER BY rank DESC";
$sql = "SELECT *, cast(substring(news_date,1, 10) as date) as cast_news_date FROM dtb_news WHERE del_flg = '0' ORDER BY rank DESC";
$objPage->list_data = $conn->getAll($sql);
$objPage->list_data["cast_news_data"] = str_replace("-", "/", $objPage->list_data["cast_news_data"]);
$objPage->line_max = count($objPage->list_data);
$sql = "SELECT MAX(rank) FROM dtb_news WHERE del_flg = '0'";		// rankの最大値を取得
$objPage->max_rank = $conn->getOne($sql);

$objPage->arrForm['news_select'] = 0;

//----　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//function --------------------------------------------------------------------------------------------- 

//---- 入力文字列を配列へ
function lfConvData( $data ){
	
	 // 文字列の変換（mb_convert_kanaの変換オプション）							
	$arrFlag = array(
					  "year" => "n"
					 ,"month" => "n"
					 ,"day" => "n"
					 ,"url" => "a"
					 ,"news_title" => "aKV"
					 ,"news_comment" => "aKV"
					 ,"link_method" => "n"
					);
		
	if ( is_array($data) ){
		foreach ($arrFlag as $key=>$line) {
			$data[$key] = mb_convert_kana($data[$key], $line);
		}
	}

	return $data;
}

//----　指定順位へ移動
function sf_setRankPosition($conn, $tableName, $keyIdColumn, $keyId, $position) {

	// 自身のランクを取得する
	$conn->query("BEGIN");
	$rank = $conn->getOne("SELECT rank FROM $tableName WHERE $keyIdColumn = ?", array($keyId));	

	if( $position > $rank ) $term = "- 1";	//入れ替え先の順位が入れ換え元の順位より大きい場合
	if( $position < $rank ) $term = "+ 1";	//入れ替え先の順位が入れ換え元の順位より小さい場合

	//--　指定した順位の商品から移動させる商品までのrankを１つずらす
	$sql = "UPDATE $tableName SET rank = rank $term, update_date = NOW() WHERE rank BETWEEN ? AND ? AND del_flg = 0";
	if( $position > $rank ) $conn->query( $sql, array( $rank + 1, $position ) );
	if( $position < $rank ) $conn->query( $sql, array( $position, $rank - 1 ) );

	//-- 指定した順位へrankを書き換える。
	$sql  = "UPDATE $tableName SET rank = ?, update_date = NOW() WHERE $keyIdColumn = ? AND del_flg = 0 ";
	$conn->query( $sql, array( $position, $keyId ) );
	$conn->query("COMMIT");
}

//---- 入力エラーチェック（順位移動用）
function sf_errorCheckPosition($conn, $tableName, $position, $keyIdColumn, $keyId) {

	$objErr = new SC_CheckError();
	$objErr->doFunc( array("移動順位", "moveposition", 4 ), array( "ZERO_CHECK", "NUM_CHECK", "EXIST_CHECK", "MAX_LENGTH_CHECK" ) );

	// 自身のランクを取得する。
	$rank = $conn->getOne("SELECT rank FROM $tableName WHERE $keyIdColumn = ?", array($keyId));
	if ($rank == $position ) $objErr->arrErr["moveposition"] .= "※ 指定した移動順位は現在の順位です。";
	
	// rankの最大値以上の入力を許容しない											 
	if( ! $objErr->arrErr["position"] ) {								 
		$sql = "SELECT MAX( rank ) FROM " .$tableName. " WHERE del_flg = 0";
		$result = $conn->getOne($sql);
		if( $position > $result ) $objErr->arrErr["moveposition"] .= "※ 入力された順位は、登録数の最大値を超えています。";
	}

	return $objErr->arrErr;
}

//---- 入力エラーチェック
function lfErrorCheck(){

	$objErr = new SC_CheckError();

	$objErr->doFunc(array("日付(年)", "year"), array("EXIST_CHECK"));
	$objErr->doFunc(array("日付(月)", "month"), array("EXIST_CHECK"));
	$objErr->doFunc(array("日付(日)", "day"), array("EXIST_CHECK"));
	$objErr->doFunc(array("日付", "year", "month", "day"), array("CHECK_DATE"));
	$objErr->doFunc(array("タイトル", 'news_title', MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("本文", 'url', URL_LEN), array("MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("本文", 'news_comment', LTEXT_LEN), array("MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}

//INSERT文
function lfNewsInsert(){
	global $conn;
	global $registDate;
	
	if ($_POST["link_method"] == "") {
		$_POST["link_method"] = 1;
	}

	$sql = "INSERT INTO dtb_news (news_date, news_title, creator_id, news_url, link_method, news_comment, rank)
			VALUES ( ?,?,?,?,?,? , (SELECT MAX(rank) + 1 FROM dtb_news WHERE del_flg = '0'))";
	$arrRegist = array($registDate, $_POST["news_title"], $_SESSION['member_id'],  $_POST["news_url"], $_POST["link_method"], $_POST["news_comment"]);

	$conn->query($sql, $arrRegist);
	
	// 最初の1件目の登録はrankにNULLが入るので対策
	$sql = "UPDATE dtb_news SET rank = 1 WHERE del_flg = 0 AND rank IS NULL";
	$conn->query($sql);
}

//UPDATE文
function lfNewsUpdate(){
	global $conn;
	global $registDate;
	
	if ($_POST["link_method"] == "") {
		$_POST["link_method"] = 1;
	}	

	$sql = "UPDATE dtb_news SET news_date = ?, news_title = ?, creator_id = ?, update_date = NOW(),  news_url = ?, link_method = ?, news_comment = ? WHERE news_id = ?";
	$arrRegist = array($registDate, $_POST['news_title'], $_SESSION['member_id'], $_POST['news_url'], $_POST["link_method"], $_POST['news_comment'], $_POST['news_id']);
		
	$conn->query($sql, $arrRegist);	
}
?>
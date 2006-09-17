<?php

require_once("../../require.php");
require_once(ROOT_DIR."data/include/csv_output.inc");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'contents/csv_sql.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = 'csv';
		$this->tpl_subno_csv = 'csv_sql';
		$this->tpl_mainno = "contents";
		$this->tpl_subtitle = 'CSV出力設定';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

$objPage->arrSubnavi = $arrSubnavi;
$objPage->arrSubnaviName = $arrSubnaviName;

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// SQL_IDの取得
if ($_POST['sql_id'] != "") {
	$sql_id = $_POST['sql_id'];
}elseif($_GET['sql_id'] != ""){
	$sql_id = $_GET['sql_id'];
}else{
	$sql_id = "";
}

$mode = $_POST['mode'];

switch($_POST['mode']) {
	// データの登録
	case "confirm":
		// エラーチェック
		$objPage->arrErr = lfCheckError($_POST);
		
		if (count($objPage->arrErr) <= 0){
			// データの更新
			$sql_id = lfUpdData($sql_id, $_POST);
			// 完了メッセージ表示
			$objPage->tpl_onload = "alert('登録が完了しました。');";
		}
		break;
	
	// 確認画面
	case "preview":
		// SQL文表示
		$sql = "SELECT \n" . $_POST['sql'];
		$objPage->sql = $sql;
		
		// エラー表示
		$objErrMsg = lfCheckSQL($_POST);
		if ($objErrMsg != "") {
			$errMsg = $objErrMsg->message . "\n" . $objErrMsg->userinfo;
		}
		
		$objPage->sqlerr = $errMsg;

		$objPage->objView = $objView;
		
		// 画面の表示
		$objView->assignobj($objPage);
		$objView->display('contents/csv_sql_view.tpl');
		exit;
		break;

	// 新規作成
	case "new_page":
		header("location: ./csv_sql.php");
		break;
		
	// データ削除
	case "delete":
		lfDelData($sql_id);
		header("location: ./csv_sql.php");
		break;
		
	case "csv_output":
		// CSV出力データ取得
		$arrCsvData = lfGetSqlList(" WHERE sql_id = ?", array($_POST['csv_output_id']));
		
		$objQuery = new SC_Query();
		$arrCsvOutputData = $objQuery->getall("SELECT " . $arrCsvData[0]['sql']);
		
		if (count($arrCsvOutputData) > 0) {
			
			$arrKey = array_keys(sfSwapArray($arrCsvOutputData));
			foreach($arrKey as $data) {
				if ($i != 0) $header .= ", ";
				$header .= $data;
				$i ++;
			}
			$header .= "\n";

			$data = lfGetCSVData($arrCsvOutputData, $arrKey);
			// CSV出力
			sfCSVDownload($header.$data);
			exit;
		break;
		}else{
			$objPage->tpl_onload = "alert('出力データがありません。');";
			$sql_id = "";
			$_POST="";
		}
		break;
}

// mode が confirm 以外のときは完了メッセージは出力しない
if ($mode != "confirm" and $mode != "csv_output") {
	$objPage->tpl_onload = "";
}

// 登録済みSQL一覧取得
$arrSqlList = lfGetSqlList();

// 編集用SQLデータの取得
if ($sql_id != "") {
	$arrSqlData = lfGetSqlList(" WHERE sql_id = ?", array($sql_id));
}

// テーブル一覧を取得する
$arrTableList = lfGetTableList();
$arrTableList = sfSwapArray($arrTableList);

// 現在選択されているテーブルを取得する
if ($_POST['selectTable'] == ""){
	$selectTable = $arrTableList['table_name'][0];
}else{
	$selectTable = $_POST['selectTable'];
}

// カラム一覧を取得する
$arrColList = lfGetColumnList($selectTable);
$arrColList =  sfSwapArray($arrColList);

// テンプレートに出力するデータをセット
$objPage->arrSqlList = $arrSqlList;																// SQL一覧
$objPage->arrTableList = sfarrCombine($arrTableList['table_name'], $arrTableList['description']);	// テーブル一覧
$objPage->arrColList = sfarrCombine($arrColList['column_name'],$arrColList['description']);			// カラム一覧
$objPage->selectTable = $selectTable;															// 選択されているテーブル
$objPage->sql_id = $sql_id;																		// 選択されているSQL

sfprintr($objPage->arrColList);

// POSTされたデータをセットする
if (count($_POST) > 0) {
	$arrSqlData[0]['name'] = $_POST['name'];
	$arrSqlData[0]['sql'] = $_POST['sql'];
}
$objPage->arrSqlData = $arrSqlData[0];															// 選択されているSQLデータ

// 画面の表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * 関数名	：lfGetTableList
 * 処理内容	：テーブル一覧を取得する
 * 引数		：なし
 * 戻り値 　：取得結果
 **************************************************************************************************************/
function lfGetTableList(){
	$objQuery = new SC_Query();
	$arrRet = array();		// 結果取得用
	/*
	$sql = "";
	$sql .= " SELECT";
	$sql .= "     c.relname ,";
	$sql .= "     c.relname || ':' ||obj_description(c.oid) as description";
	$sql .= " FROM";
	$sql .= "     pg_class c,";
	$sql .= "     pg_user u";
	$sql .= " WHERE";
	$sql .= "     c.relowner = u.usesysid AND";
	$sql .= "     relname IN (SELECT";
	$sql .= "                     tablename";
	$sql .= "                 FROM";
	$sql .= "                     pg_tables";
	$sql .= "                 WHERE";
	$sql .= "                     tableowner=? ";
	$sql .= "                 )";
	$sql .= " ORDER BY c.relname ";
	$arrRet = $objQuery->getAll($sql, array(DB_USER));
	*/
	$sql = "";
	$sql .= "SELECT table_name, description FROM dtb_table_comment WHERE column_name IS NULL ORDER BY table_name";
	$arrRet = $objQuery->getAll($sql);
	
	
	return $arrRet;
}


/**************************************************************************************************************
 * 関数名	：lfGetColunmList
 * 処理内容	：テーブルのカラム一覧を取得する
 * 引数		：$selectTable：テーブル名称
 * 戻り値 　：取得結果
 **************************************************************************************************************/
function lfGetColumnList($selectTable){
	$objQuery = new SC_Query();
	$arrRet = array();		// 結果取得用
	/*
	$sql = "";
	$sql .= " SELECT";
	$sql .= "     a.attname,";
	$sql .= "     a.attnum as fldnum, ";
	$sql .= "     (select case count(description) when 0 then a.attname else (select a.attname || ':' || description from pg_description where a.attrelid=objoid and a.attnum=objsubid ) end from pg_description where a.attrelid=objoid and a.attnum=objsubid ) as description ";
	$sql .= " FROM";
	$sql .= "     pg_class c,";
	$sql .= "         pg_attribute a left join pg_description e on (a.attrelid=e.objoid and a.attnum=e.objsubid) ";
	$sql .= " ";
	$sql .= " WHERE";
	$sql .= "     (c.relname=?) AND";
	$sql .= "     (c.oid=a.attrelid) AND";
	$sql .= "     a.attnum > 0";
	$sql .= " ORDER BY";
	$sql .= "     fldnum";
	$sql .= " ";
	$arrRet = $objQuery->getAll($sql, array($selectTable));	
	*/
	$sql = "";
	$sql .= " SELECT column_name, description FROM dtb_table_comment WHERE table_name = ?";
	$arrRet = $objQuery->getAll($sql, array($selectTable));	
	
	return $arrRet;
	
}

/**************************************************************************************************************
 * 関数名	：lfGetSqlList
 * 処理内容	：登録済みSQL一覧を取得する
 * 引数1	：$where：Where句
 * 引数2	：$arrData：絞り込みデータ
 * 戻り値 　：取得結果
 **************************************************************************************************************/
function lfGetSqlList($where = "" , $arrData = array()){
	$objQuery = new SC_Query();
	$arrRet = array();		// 結果取得用
	
	$sql = "";
	$sql .= " SELECT";
	$sql .= "     sql_id,";
	$sql .= "     name,";
	$sql .= "     sql,";
	$sql .= "     update_date,";
	$sql .= "     create_date";
	$sql .= " FROM";
	$sql .= "     dtb_csv_sql";
	
	// Where句の指定があれば結合する
	if ($where != "") {
		$sql .= " $where ";
	}else{
		$sql .= " ORDER BY sql_id ";
	}
	$sql .= " ";

	// データを引数で渡されている場合にはセットする
	if (count($arrData) > 0) {
		$arrRet = $objQuery->getall($sql, $arrData);
	}else{
		$arrRet = $objQuery->getall($sql);
	}

	return $arrRet;
	
}

/**************************************************************************************************************
 * 関数名	：lfUpdCsvOutput
 * 処理内容	：入力項目のエラーチェックを行う
 * 引数		：POSTデータ
 * 戻値		：エラー内容
 **************************************************************************************************************/
function lfCheckError($data){
	$objErr = new SC_CheckError();
	$objErr->doFunc( array("名称", "name"), array("EXIST_CHECK") );
	$objErr->doFunc( array("SQL文", "sql"), array("EXIST_CHECK") );
	
	// SQLの妥当性チェック
	if ($objErr->arrErr['sql'] == "") {
		$objsqlErr = lfCheckSQL($data);
		if ($objsqlErr != "") {
			$objErr->arrErr["sql"] = "SQL文が不正です。SQL文を見直してください";
		}
	}
	
	return $objErr->arrErr;

}

/**************************************************************************************************************
 * 関数名	：lfCheckSQL
 * 処理内容	：入力されたSQL文が正しいかチェックを行う
 * 引数		：POSTデータ
 * 戻値		：エラー内容
 **************************************************************************************************************/
function lfCheckSQL($data){
	$err = "";
	$objDbConn = new SC_DbConn();
	$sql = "SELECT " . $data['sql'] . " ";
	$ret = $objDbConn->conn->query($sql);
	if ($objDbConn->conn->isError($ret)){
		$err = $ret;
	}
	
	return $err;
}

function lfprintr($data){
	print_r($data);
}

/**************************************************************************************************************
 * 関数名	：lfUpdData
 * 処理内容	：DBにデータを保存する
 * 引数1	：$sql_id･･･更新するデータのSQL_ID
 * 引数2	：$arrData･･･更新データ
 * 戻り値	：$sql_id:SQL_IDを返す
 **************************************************************************************************************/
function lfUpdData($sql_id = "", $arrData = array()){
	$objQuery = new SC_Query();		// DB操作オブジェクト
	$sql = "";						// データ取得SQL生成用
	$arrRet = array();				// データ取得用(更新判定)
	$arrVal = array();				// データ更新

	// sql_id が指定されている場合にはUPDATE
	if ($sql_id != "") {
		// 存在チェック
		$arrSqlData = lfGetSqlList(" WHERE sql_id = ?", array($sql_id));
		if (count($arrSqlData) > 0) {
			// データ更新
			$sql = "UPDATE dtb_csv_sql SET name = ?, sql = ?, update_date = now() WHERE sql_id = ? ";
			$arrVal= array($arrData['name'], $arrData['sql'], $sql_id);
		}else{
			// データの新規作成
			$sql_id = "";
			$sql = "INSERT INTO dtb_csv_sql (name, sql) values (?, ?) ";
			$arrVal= array($arrData['name'], $arrData['sql']);
			
		}
	}else{
		// データの新規作成
		$sql = "INSERT INTO dtb_csv_sql (name, sql) values (?, ?) ";
		$arrVal= array($arrData['name'], $arrData['sql']);
	}
	// SQL実行	
	$arrRet = $objQuery->query($sql,$arrVal);
	
	// 新規作成時は$sql_idを取得
	if ($sql_id == "") {
		$arrNewData = lfGetSqlList(" ORDER BY create_date DESC");
		$sql_id = $arrNewData[0]['sql_id'];
	}
	
	return $sql_id;
}


/**************************************************************************************************************
 * 関数名	：lfDelData
 * 処理内容	：データを削除する
 * 引数1	：$sql_id･･･削除するデータのSQL_ID
 * 戻り値	：実行結果　TRUE：成功 FALSE：失敗
 **************************************************************************************************************/
function lfDelData($sql_id = ""){
	$objQuery = new SC_Query();		// DB操作オブジェクト
	$sql = "";						// データ取得SQL生成用
	$Ret = false;					// 実行結果

	// sql_id が指定されている場合のみ実行
	if ($sql_id != "") {
		// データの削除
		$sql = "DELETE FROM dtb_csv_sql WHERE sql_id = ? ";
		// SQL実行	
		$ret = $objQuery->query($sql,array($sql_id));
	}else{
		$ret = false;
	}

	// 結果を返す
	return $ret;
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


<?php

require_once("../require.php");
require_once("../../../data/module/Tar.php");
//require_once("./Tar.php");

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = 'system/bkup.tpl';
		$this->tpl_subnavi = 'system/subnavi.tpl';
		$this->tpl_mainno = 'system';		
		$this->tpl_subno = 'bkup';
		$this->tpl_subtitle = 'バックアップ管理';
		
//		$this->bkup_dir = ROOT_DIR . USER_DIR . "bkup/";
		$this->bkup_dir = ROOT_DIR . "html/test/" . "bkup/";
//		$this->bkup_dir = "../../test/bkup/";
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// セッションクラス
$objSess = new SC_Session();
// 認証可否の判定
sfIsSuccess($objSess);

switch($_POST['mode']) {
// バックアップを作成する
case 'bkup':
	// 入力文字列の変換
	$arrData = lfConvertParam($_POST);

	// エラーチェック
	$arrErr = lfCheckError($arrData);

	// エラーがなければバックアップ処理を行う	
	if (count($arrErr) <= 0) {
		// バックアップファイル作成
		$arrErr = lfCreateBkupData($arrData['bkup_name']);
		
		// DBにデータ更新
		if (count($arrErr) <= 0) {
			lfUpdBkupData($arrData);
		}else{
			$arrForm = $arrData;
		}
	}else{
		$arrForm = $arrData;
	}

	break;
	
// リストア
case 'restore':
	lfRestore($_POST['list_name']);

	break;
	
// 削除
case 'del':
	// ファイルの削除
	unlink($objPage->bkup_dir.$_POST['list_name'] . ".tar.gz");

	// DBから削除
	$delsql = "DELETE FROM dtb_bkup WHERE bkup_name = ?";
	$objQuery->query($delsql, array($_POST['list_name']));

	break;
default:
	break;
}

// バックアップリストを取得する
$arrBkupList = lfGetBkupData("ORDER BY create_date DESC");

// テンプレートファイルに渡すデータをセット
$objPage->arrErr = $arrErr;
$objPage->arrForm = $arrForm;
$objPage->arrBkupList = $arrBkupList;

$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display(MAIN_FRAME);		//テンプレートの出力

//-------------------------------------------------------------------------------------------------------
/* 取得文字列の変換 */
function lfConvertParam($array) {
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 *  a :  全角英数字を半角英数字に変換する
	 */
	$arrConvList['bkup_name'] = "a";
	$arrConvList['bkup_memo'] = "KVa";
	
	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

// エラーチェック
function lfCheckError($array){
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("バックアップ名", "bkup_name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK","NO_SPTAB","ALNUM_CHECK"));
	$objErr->doFunc(array("バックアップメモ", "bkup_memo", MTEXT_LEN), array("MAX_LENGTH_CHECK"));
	
	// 重複チェック
	$ret = lfGetBkupData("WHERE bkup_name = ?", array($array['bkup_name']));
	if (count($ret) > 0) {
		$objErr->arrErr['bkup_name'] = "バックアップ名が重複しています。別名を入力してください。";
	}

	return $objErr->arrErr;
}

// バックアップファイル作成
function lfCreateBkupData($bkup_name){
	global $objPage;
	$objQuery = new SC_Query();
	$csv_data = "";
	$err = true;
	
	$bkup_dir = $objPage->bkup_dir;
	$bkup_dir = $bkup_dir . $bkup_name . "/";

	// 全テーブル取得
	$arrTableList = lfGetTableList();
	
	// 各テーブル情報を取得する
	foreach($arrTableList as $key => $val){
		
		if ($val != "dtb_bkup") {
			// テーブル構成を取得
			$arrColumnList = lfGetColumnList($val);
			
			// 全データを取得
			$arrData = $objQuery->getAll("SELECT * FROM $val");
	
			// CSVデータ生成
			if (count($arrData) > 0) {
				
				// カラムをCSV形式に整える
				$arrKyes = sfGetCommaList(array_keys($arrData[0]), false);
				
				// データをCSV形式に整える
				$data = "";
				foreach($arrData as $data_key => $data_val){
					$data .= lfGetCSVList($arrData[$data_key]);
				}
				
				// CSV出力データ生成
				$csv_data .= $val . "\n";
				$csv_data .= $arrKyes . "\n";
				$csv_data .= $data;
				$csv_data .= "\n";
			}	
		}
	}

	$csv_file = $bkup_dir . "bkup_data.csv";
	// CSV出力
	// ディレクトリが存在していなければ作成する		
	if (!is_dir(dirname($csv_file))) {
		$err = mkdir(dirname($csv_file));
	}
	if ($err) {
		$fp = fopen($csv_file,"w");
		if($fp) {
			$err = fwrite($fp, $csv_data);
			fclose($fp);
		}
	}

	// 商品画像ファイルをコピー
	if ($err) {
		$copy_mess = "";
		$copy_mess = sfCopyDir("../../upload/save_image/", $bkup_dir, $copy_mess);

		//圧縮フラグTRUEはgzip圧縮をおこなう
		$tar = new Archive_Tar($objPage->bkup_dir . $bkup_name.".tar.gz", TRUE);

		//bkupフォルダに移動する
		chdir($objPage->bkup_dir);

		//圧縮をおこなう
		$zip = $tar->create("./" . $bkup_name . "/");

		// バックアップデータの削除
		if ($zip) sfDelFile($bkup_dir);
	}

	if (!$err) {
		$arrErr['bkup_name'] = "バックアップに失敗しました。";
	}
	
	return $arrErr;
}

/* 配列の要素をCSVフォーマットで出力する。*/
function lfGetCSVList($array) {
	if (count($array) > 0) {
		foreach($array as $key => $val) {
			if ($val == "") {
				$line .= "NULL,";
			}else{
				$line .= "'".$val."',";
			}
		}
		$line = ereg_replace(",$", "\n", $line);
		return $line;
	}else{
		return false;
	}
}

// 全テーブルリストを取得する
function lfGetTableList(){
	$objQuery = new SC_Query();
	
	if(DB_TYPE == "pgsql"){
		$sql = "SELECT tablename FROM pg_tables WHERE tableowner = ? ORDER BY tablename ; ";
		$arrRet = $objQuery->getAll($sql, array(DB_USER));
		$arrRet = sfSwapArray($arrRet);
		$arrRet = $arrRet['tablename'];
	}else if(DB_TYPE == "mysql"){
		
	}
	
	return $arrRet;
}

// テーブル構成を取得する
function lfGetColumnList($table_name){
	$objQuery = new SC_Query();

	if(DB_TYPE == "pgsql"){
		$sql = "SELECT 
					a.attname, t.typname, a.attnotnull, d.adsrc as defval, a.atttypmod,	a.attnum as fldnum,	e.description 
				FROM 
					pg_class c,
					pg_type t,
					pg_attribute a left join pg_attrdef d on (a.attrelid=d.adrelid and a.attnum=d.adnum)
								   left join pg_description e on (a.attrelid=e.objoid and a.attnum=e.objsubid)
				WHERE (c.relname=?) AND (c.oid=a.attrelid) AND (a.atttypid=t.oid) AND a.attnum > 0
				ORDER BY fldnum";
		$arrRet = $objQuery->getAll($sql, array($table_name));
	}
	
	return sfswaparray($arrRet);

}

// バックアップテーブルにデータを更新する
function lfUpdBkupData($data){
	$objQuery = new SC_Query();
	
	$sql = "INSERT INTO dtb_bkup (bkup_name,bkup_memo,create_date) values (?,?,now())";
	$objQuery->query($sql, array($data['bkup_name'],$data['bkup_memo']));
}

// バックアップテーブルからデータを取得する
function lfGetBkupData($where = "", $data = array()){
	$objQuery = new SC_Query();
	
	$sql = "SELECT bkup_name, bkup_memo, create_date FROM dtb_bkup ";
	if ($where != "")	$sql .= $where;
	
	$ret = $objQuery->getall($sql,$data);
	
	return $ret;
}

// バックアップファイルをリストアする
function lfRestore($bkup_name){
	global $objPage;
	$objQuery = new SC_Query();
	$csv_data = "";
	$err = true;
	
	$bkup_dir = $objPage->bkup_dir;
	
	//fileフォルダに移動する
	chdir($bkup_dir);
	
	//圧縮フラグTRUEはgzip解凍をおこなう
	$tar = new Archive_Tar($bkup_name . ".tar.gz", TRUE);
	
	//指定されたフォルダ内に解凍する
	$err = $tar->extract("./");
	
	// 無事解凍できれば、リストアを行う
	if ($err) {
		
		// INSERT文作成
		lfCreateInsertSQL($bkup_dir . $bkup_name . "/bkup_data.csv");
		
	}
	sfprintr($data);
}

// CSVファイルからインサート文作成
function lfCreateInsertSQL($csv){
	// csvファイルからデータの取得
	$arrCsvData = file($csv);
	
	$sql = "";
	$base_sql = "";
	$tbl_flg = false;
	$col_flg = false;
	
	foreach($arrCsvData as $key => $val){
		$data = trim($val);
		$sql = "";
		
		//空白行のときはテーブル変更
		if ($data == "") {
			$base_sql = "";
			$tbl_flg = false;
			$col_flg = false;
			continue;
		}
		
		// テーブルフラグがたっていない場合にはテーブル名セット
		if (!$tbl_flg) {
			$base_sql = "INSERT INTO $data ";
			$tbl_flg = true;
			continue;
		}
		
		// カラムフラグがたっていない場合にはカラムセット
		if (!$col_flg) {
			$base_sql .= " ($data) VALUES ";
			$col_flg = true;
			continue;
		}
		
		// インサートする値をセット
		$sql .= $base_sql . " ($data);";
	}
	
	sfprintr($sql);
}



?>

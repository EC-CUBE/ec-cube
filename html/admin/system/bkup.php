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
		$this->bkup_dir = "../../test/bkup/";
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
	
// インストール
case 'install':
	// 更新情報を最新にする
	lfLoadUpdateList();
	// モジュール郡のインストール
	lfInstallModule();
	break;
	
// 削除
case 'del':

	// ファイルの削除
	

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
					$data .= sfGetCSVList($arrData[$data_key]);
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

		//オブジェクトを作成する
		//new Archive_Tar(ファイル名,圧縮フラグ);
		//圧縮フラグTRUEはgzip圧縮をおこなう
		$tar = new Archive_Tar($objPage->bkup_dir . $bkup_name.".tar.gz", TRUE);
	
		//圧縮をおこなう
		$zip = $tar->create($bkup_dir);
		// バックアップデータの削除
		if ($zip) {
			$dh = opendir($bkup_dir);
			while($file = readdir($dh)){
				sfPrintR($file);
			}
		}
		
	}
	

	if (!$err) {
		$arrErr['bkup_name'] = "バックアップに失敗しました。";
	}
	
	return $arrErr;
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











// 更新ファイルの取得
function lfCopyUpdateFile($val) {
	global $objPage;
	
	$src_path = sfRmDupSlash(UPDATE_HTTP . $val . ".txt");
	$dst_path = sfRmDupSlash(ROOT_DIR . $val);
	$flg_ok = true;	// 処理の成功判定
	
	$src_fp = @fopen($src_path, "rb");
	
	if(!$src_fp) {
		sfErrorHeader(">> " . $src_path . "の取得に失敗しました。");
		$flg_ok = false;
	} else {
		// ファイルをすべて読み込む
		$contents = '';
		while (!feof($src_fp)) {
			$contents .= fread($src_fp, 1024);
		}
		fclose($src_fp);
		
		// ディレクトリ作成を試みる
		lfMakeDirectory($dst_path);
		// ファイル書込み		
		$dst_fp = @fopen($dst_path, "wb");
		if(!$dst_fp) {
			sfErrorHeader(">> " . $dst_path . "をオープンできません。");
			$flg_ok = false;
		} else {
			fwrite($dst_fp, $contents);
			fclose($dst_fp);
		}
	}
	
	if($flg_ok) {
		$objPage->update_mess.= ">> " . $dst_path . "：コピー成功<br>";
	} else {
		$objPage->update_mess.= ">> " . $dst_path . "：コピー失敗<br>";		
	}
	
	return $flg_ok;
}

// すべてのパスのディレクトリを作成する
function lfMakeDirectory($path) {
	$pos = 0;
	$cnt = 0;				// 無限ループ対策
	$len = strlen($path);	// 無限ループ対策
	
	while($cnt <= $len) {
		$pos = strpos($path, "/", $pos);
		// ここでの判定は、等号3つを使用
		if($pos === false) {
			// スラッシュが見つからない場合はループから抜ける
			break;
		}
		$pos++; // 文字発見位置を一文字進める
		$dir = substr($path, 0, $pos);
		
		// すでに存在するかどうか調べる
		if(!file_exists($dir)) {
			mkdir($dir);
		}
		$cnt++; // 無限ループ対策
	}
}

// 更新情報を最新にする
function lfLoadUpdateList() {
	$objQuery = new SC_Query();
	$path = UPDATE_HTTP . "update.txt";
	$fp = @fopen($path, "rb");
	
	if(!$fp) {
		sfErrorHeader(">> " . $path . "の取得に失敗しました。");
	} else {
		while (!feof($fp)) {
			$arrCSV = fgetcsv($fp, UPDATE_CSV_LINE_MAX);
			// カラム数が正常であった場合のみ
			if(count($arrCSV) == UPDATE_CSV_COL_MAX) {
				// 取得したアップデート情報をDBに書き込む
				$sqlval['module_id'] = $arrCSV[0];
				$sqlval['module_name'] = $arrCSV[1];
				$sqlval['latest_version'] = $arrCSV[3];
				$sqlval['module_explain'] = $arrCSV[4];
				$sqlval['main_php'] = $arrCSV[5];
				$sqlval['extern_php'] = $arrCSV[6];
				$sqlval['install_sql'] = $arrCSV[7];
				$sqlval['uninstall_sql'] = $arrCSV[8];				
				$sqlval['other_files'] = $arrCSV[9];
				$sqlval['del_flg'] = $arrCSV[10];
				$sqlval['update_date'] = "now()";
				$sqlval['release_date'] = $arrCSV[12];
				// 既存レコードのチェック
				$cnt = $objQuery->count("dtb_update", "module_id = ?", array($sqlval['module_id']));
				if($cnt > 0) {
					// すでに取得されている場合は更新する。	
					$objQuery->update("dtb_update", $sqlval, "module_id = ?", array($sqlval['module_id']));
				} else {
					// 新規レコードの追加
					$sqlval['create_date'] = "now()";
					$objQuery->insert("dtb_update", $sqlval);
				}
			}
		}
		fclose($fp);
	}
}

// インストール処理
function lfInstallModule() {
	global $objPage;
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("module_id, extern_php, other_files, install_sql, latest_version", "dtb_update", "module_id = ?", array($_POST['module_id']));
	$flg_ok = true;	// 処理の成功判定
	
	if(count($arrRet) > 0) {
		$arrFiles = array();
		if($arrRet[0]['other_files'] != "") {
			$arrFiles = split("\|", $arrRet[0]['other_files']);
		}
		$arrFiles[] = $arrRet[0]['extern_php'];
		foreach($arrFiles as $val) {
			// 更新ファイルの取得
			$ret=lfCopyUpdateFile($val);
			if(!$ret) {
				$flg_ok = false;
			}
		}
	} else {
		sfErrorHeader(">> 対象の機能は、配布を終了しております。");
		$flg_ok = false;
	}
	
	// 必要なSQL文の実行
	if($arrRet[0]['install_sql'] != "") {
		// SQL文実行、パラーメータなし、エラー無視
		$arrInstallSql = split(";",$arrRet[0]['install_sql']);
		foreach($arrInstallSql as $key => $val){
			if (trim($val) != ""){
				$ret = $objQuery->query(trim($val),"",true);
			}
		}
		if(DB::isError($ret)) {
			// エラー文を取得する
			ereg("\[(.*)\]", $ret->userinfo, $arrKey);
			$objPage->update_mess.=">> テーブル構成の変更に失敗しました。<br>";
			$objPage->update_mess.= $arrKey[0] . "<br>";
			$flg_ok = false;
		} else {
			$objPage->update_mess.=">> テーブル構成の変更を行いました。<br>";
		}
	}
	
	if($flg_ok) {
		$sqlval['now_version'] = $arrRet[0]['latest_version'];
		$sqlval['update_date'] = "now()";
		$objQuery->update("dtb_update", $sqlval, "module_id = ?", array($arrRet[0]['module_id']));
	}
}

// アンインストール処理
function lfUninstallModule() {
	global $objPage;
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("module_id, extern_php, other_files, install_sql, uninstall_sql, latest_version", "dtb_update", "module_id = ?", array($_POST['module_id']));
	$flg_ok = true;	// 処理の成功判定
	
	if(count($arrRet) > 0) {
		$arrFiles = array();
		if($arrRet[0]['other_files'] != "") {
			$arrFiles = split("\|", $arrRet[0]['other_files']);
		}
		$arrFiles[] = $arrRet[0]['extern_php'];
		foreach($arrFiles as $val) {
			$path = ROOT_DIR . $val;
			if(file_exists($path)) {
				// ファイルを削除する
				if(unlink($path)) {
					$objPage->update_mess.= ">> " . $path . "：削除成功<br>";
				} else {
					$objPage->update_mess.= ">> " . $path . "：削除失敗<br>";
				}
			}
		}
		
		// 必要なSQL文の実行
		if($arrRet[0]['uninstall_sql'] != "") {
			// SQL文実行、パラーメータなし、エラー無視
			$ret = $objQuery->query($arrRet[0]['uninstall_sql'],"",true);
			if(DB::isError($ret)) {
				// エラー文を取得する
				ereg("\[(.*)\]", $ret->userinfo, $arrKey);
				$objPage->update_mess.=">> テーブル構成の変更に失敗しました。<br>";
				$objPage->update_mess.= $arrKey[0] . "<br>";
				$flg_ok = false;
			} else {
				$objPage->update_mess.=">> テーブル構成の変更を行いました。<br>";
			}
		}		
	} else {
		sfErrorHeader(">> 対象の機能は、配布を終了しております。");
	}
	
	if($flg_ok) {
		// バージョン情報を削除する。
		$sqlval['now_version'] = "";
		$sqlval['update_date'] = "now()";
		$objQuery->update("dtb_update", $sqlval, "module_id = ?", array($arrRet[0]['module_id']));
	}
}


?>

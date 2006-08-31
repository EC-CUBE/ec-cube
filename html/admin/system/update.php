<?php
require_once("../require.php");

//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = 'system/update.tpl';
		$this->tpl_subnavi = 'system/subnavi.tpl';
		$this->tpl_mainno = 'system';		
		$this->tpl_subno = 'update';
		$this->tpl_subtitle = 'アップデート管理';
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
// アップデート情報ファイルを取得
case 'edit':
	// 更新情報を最新にする
	lfLoadUpdateList();
	break;
// インストール
case 'install':
	// 更新情報を最新にする
	lfLoadUpdateList();
	// モジュール郡のインストール
	lfInstallModule();
	header("Location: " . $_SERVER['PHP_SELF']);
	break;
// アンインストール
case 'uninstall':
	// 更新情報を最新にする
	lfLoadUpdateList();
	// モジュール郡のインストール	
	lfUninstallModule();
	header("Location: " . $_SERVER['PHP_SELF']);
	break;
default:
	break;
}

$col = "module_id, module_name, now_version, latest_version, module_explain, create_date, release_date";
$objQuery->setorder("module_id");
$objPage->arrUpdate = $objQuery->select($col, "dtb_update");

$objView->assignobj($objPage);		//変数をテンプレートにアサインする
$objView->display(MAIN_FRAME);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
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
				$sqlval['sql'] = $arrCSV[7];
				$sqlval['uninstall_sql'] = $arrCSV[8];				
				$sqlval['other_files'] = $arrCSV[9];
				$sqlval['delete'] = $arrCSV[10];
				$sqlval['update_date'] = "now()";
				$sqlval['release_date'] = $arrCSV[12];
				// 既存レコードのチェック
				$cnt = $objQuery->count("dtb_update", "module_id = ?", array($sqlval['module_id']));
				if($cnt > 0) {
					// すでに取得されている場合は更新する。	
					$objQuery->update("dtb_update", $sqlval, "module_id = ?", array($sqlval['module_id']));
				} else {
					// 新規レコードの追加
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
	
	print("kita");
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("module_id, extern_php, other_files, sql, latest_version", "dtb_update", "module_id = ?", array($_POST['module_id']));
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
	if($arrRet[0]['sql'] != "") {
		// SQL文実行、パラーメータなし、エラー無視
		$ret = $objQuery->query($arrRet[0]['sql'],"",true);
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
		$objQuery->getLastQuery();
	}
}

// アンインストール処理
function lfUninstallModule() {
	global $objPage;
	
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("module_id, extern_php, other_files, sql, uninstall_sql, latest_version", "dtb_update", "module_id = ?", array($_POST['module_id']));
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

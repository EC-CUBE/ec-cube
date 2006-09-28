<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./upload_csv.inc");

// 1行あたりの最大文字数
define("CSV_LINE_MAX", 10000);

class LC_Page {
	var $arrSession;
	var $arrCSVErr;
	function LC_Page() {
		$this->tpl_mainpage = 'products/upload_csv.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';
		$this->tpl_subno = 'upload_rakuten';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

// ファイル管理クラス
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
// ファイル情報の初期化
lfInitFile();
// パラメータ管理クラス
$objFormParam = new SC_FormParam();
// パラメータ情報の初期化
lfInitParam();
$colmax = $objFormParam->getCount();
$objFormParam->setHtmlDispNameArray();
$objPage->arrTitle = $objFormParam->getHtmlDispNameArray();

switch($_POST['mode']) {
case 'csv_upload':
	$err = false;
	// エラーチェック
	$objPage->arrErr['csv_file'] = $objUpFile->makeTempFile('csv_file');
	
	if($objPage->arrErr['css_file'] == "") {
		$objPage->arrErr = $objUpFile->checkEXISTS();
	}
	
	if($objPage->arrErr['csv_file'] == "") {
		// 一時ファイル名の取得
		$filepath = $objUpFile->getTempFilePath('csv_file');
		// エンコード
		$enc_filepath = sfEncodeFile($filepath, "EUC-JP", CSV_TEMP_DIR);
		$fp = fopen($enc_filepath, "r");
		$line = 0;		// 行数
		$regist = 0;	// 登録数
		
		$objQuery = new SC_Query();
		$objQuery->begin();
		
		while(!feof($fp) && !$err) {
			$arrCSV = fgetcsv($fp, CSV_LINE_MAX);
						
			// 行カウント
			$line++;
			
			if($line <= 1) {
				continue;
			}			
							
			// 項目数カウント
			$max = count($arrCSV);
			
			// 項目数が1以下の場合は無視する
			if($max <= 1) {
				continue;			
			}
			
			// 項目数チェック
			if($max != $colmax) {
				$objPage->arrCSVErr['blank'] = "※ 項目数が" . $max . "個検出されました。項目数は" . $colmax . "個になります。";
				$err = true;
			} else {
				// シーケンス配列を格納する。
				$objFormParam->setParam($arrCSV, true);
				$arrRet = $objFormParam->getHashArray();
				$objFormParam->setParam($arrRet);
				// 入力値の変換
				$objFormParam->convParam();
				// <br>なしでエラー取得する。
				$objPage->arrCSVErr = lfCheckError();
			}
			
			// 入力エラーチェック
			if(count($objPage->arrCSVErr) > 0) {
				$objPage->tpl_errtitle = "■" . $line . "行目でエラーが発生しました。";
				$objPage->arrParam = $objFormParam->getHashArray();
				$err = true;
			}
			
			if(!$err) {
				lfRegistProduct($objQuery);
				$regist++;
			}
		}
		fclose($fp);
		
		if(!$err) {
			$objQuery->commit();
			$objPage->tpl_oktitle = "■" . $regist . "件のレコードを登録しました。";
			// 商品件数カウント関数の実行
			sfCategory_Count($objQuery);
		} else {
			$objQuery->rollback();
		}
	}
	break;
default:
	break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------

/* ファイル情報の初期化 */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("CSVファイル", 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
}

/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	
	$objFormParam->addParam("フラグ(対応なし)", "dummy1");
	$objFormParam->addParam("商品名", "name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("モバイル用商品名(対応なし)", "dummy2");
	$objFormParam->addParam("商品コード", "product_code", STEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("商品ID(対応なし)", "dummy3");
	$objFormParam->addParam("商品ページID(対応なし)", "dummy1");
	$objFormParam->addParam("実売価格", "price01", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("表示価格", "price02", PRICE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("消費税フラグ(対応なし)", "dummy4");
	$objFormParam->addParam("送料(対応なし)", "dummy5");
	$objFormParam->addParam("個別送料(対応なし)", "dummy6");
	$objFormParam->addParam("注文ボタン(対応なし)", "dummy7");
	$objFormParam->addParam("資料請求ボタン(対応なし)", "dummy8");
	$objFormParam->addParam("問い合わせボタン(対応なし)", "dummy9");
	$objFormParam->addParam("お勧めボタン(対応なし)", "dummy10");
	$objFormParam->addParam("のし対応フラグ(対応なし)", "dummy11");
	$objFormParam->addParam("在庫数", "stock", INT_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("項目選択肢(対応なし)", "dummy12");
	$objFormParam->addParam("期間限定販売(対応なし)", "dummy13");
	$objFormParam->addParam("説明文", "main_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("モバイル説明文(対応なし)", "dummy14");
	$objFormParam->addParam("画像(対応なし)", "dummy15");
	$objFormParam->addParam("楽天ディレクトリID(対応なし)", "dummy16");
	$objFormParam->addParam("モバイル(対応なし)", "dummy17");
}
?>
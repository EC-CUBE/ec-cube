<?php

require_once("../require.php");

// 1行あたりの最大文字数
define("CSV_LINE_MAX", 10000);

class LC_Page {
	var $arrSession;
	var $arrCSVErr;
	function LC_Page() {
		$this->tpl_mainpage = 'products/upload_csv.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';
		$this->tpl_subno = 'upload_csv';
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
$objPage->arrTitle = $objFormParam->getTitleArray();

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
				// 値をフォーマット変換して格納する。
				$arrRet = lfConvFormat($arrRet);
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
				lfInsertProduct($objQuery);
				$regist++;
			}
		}
		fclose($fp);
		
		if(!$err) {
			$objQuery->commit();
			$objPage->tpl_oktitle = "■" . $regist . "件のレコードを登録しました。";
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
	
	$objFormParam->addParam("商品名", "name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("カテゴリID", "category_id", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("商品ステータス", "product_flag", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("商品コード", "product_code", STEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("商品価格", "price01", PRICE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("商品価格", "price02", PRICE_LEN, "n", array("MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("ポイント付与率", "point_rate", PERCENTAGE_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("在庫数", "stock", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("購入制限", "sale_limit", INT_LEN, "n", array("EXIST_CHECK","MAX_LENGTH_CHECK","NUM_CHECK"));
	$objFormParam->addParam("メイン一覧コメント", "main_list_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("メインコメント", "main_comment", LTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブタイトル(1)", "sub_title1", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブコメント(1)", "sub_comment1", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブタイトル(2)", "sub_title2", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブコメント(2)", "sub_comment2", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブタイトル(3)", "sub_title3", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブコメント(3)", "sub_comment3", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブタイトル(4)", "sub_title4", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブコメント(4)", "sub_comment4", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブタイトル(5)", "sub_title5", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブコメント(5)", "sub_comment5", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブタイトル(6)", "sub_title6", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("詳細-サブコメント(6)", "sub_comment6", LTEXT_LEN, "KVa", array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
}

/* 特殊項目の変換 */
function lfConvFormat($array) {
	global $arrDISP;
	foreach($array as $key => $val) {
		switch($key) {
		case 'status':
			$arrRet[$key] = sfSearchKey($arrDISP, $val, 1);
			break;
		default:
			$arrRet[$key] = $val;
			break;
		}
	}
	return $arrRet;
}

/* 商品の新規追加 */
function lfInsertProduct($objQuery) {
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();
	
	// 規格に登録される値を除外する。
	foreach($arrRet as $key => $val) {
		switch($key) {
		case 'product_code':
		case 'price01':
		case 'price02':
		case 'point_rate':
		case 'stock':
			break;
		default:
			$sqlval[$key] = $val;
			break;
		}
	}
	
	$sqlval['status'] = 2;	// 非表示に設定する。
	$sqlval['update_date'] = "Now()";
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['rank'] = $objQuery->max("dtb_products", "rank", "del_flg = 0 AND category_id = ?", array($sqlval['category_id'])) + 1;
	
	// 規格登録
	sfInsertProductClass($objQuery, $arrRet, $product_id);
	
	// INSERTの実行
	$objQuery->insert("dtb_products", $sqlval);
	
	$product_id = $objQuery->nextval("dtb_products", "product_id");
	$sqlval['product_id'] = $product_id;
	
}

/* 入力内容のチェック */
function lfCheckError() {
	global $objFormParam;
	// 入力データを渡す。
	$arrRet =  $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrRet);
	$objErr->arrErr = $objFormParam->checkError(false);
	
	if(!isset($objErr->arrErr['category_id'])) {
		$objQuery = new SC_Query();
		$col = "level";
		$table = "dtb_category";
		$where = "category_id = ?";
		$level = $objQuery->get($table, $col, $where, array($arrRet['category_id']));
		if($level != LEVEL_MAX) {
			$objErr->arrErr['category_id'] = "※ このカテゴリIDには商品を登録できません。";
		}
	}
	return $objErr->arrErr;
}
?>
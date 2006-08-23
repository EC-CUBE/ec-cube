<?php

require_once("../require.php");
require_once("./page_edit.inc");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'contents/page_edit.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "page_edit";
		global $arrPageList;
		$this->arrPageList = $arrPageList;
		$this->tpl_subtitle = 'ページ編集';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

$objFormParam = new SC_FormParam();			// フォーム用
lfInitParam();								// パラメータ情報の初期化
$objFormParam->setParam($_POST);			// POST値の取得

switch($_POST['mode']) {
case 'edit':
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		$page = $_POST['page'];
		if($arrPageTpl[$page] != "") {
			// 一時ファイルに書き込む
			$path = TEMPLATE_FTP_DIR . $arrPageTpl[$page] . ".tmp";
			$ret = lfWriteFile($path, $objFormParam->getValue('template'));
			// 本番ファイルに反映
			if($ret > 0) {
				$dst_path = TEMPLATE_FTP_DIR . $arrPageTpl[$page];
				if(!copy($path, $dst_path)) {
					print("ファイルの書込みに失敗しました。");
				}
			}
		}
	}	
	break;
case 'preview':
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		$page = $_POST['page'];
		if($arrPageTpl[$page] != "") {
			// 一時ファイルに書き込む
			$path = TEMPLATE_FTP_DIR . $arrPageTpl[$page] . ".tmp";
			$ret = lfWriteFile($path, $objFormParam->getValue('template'));
			// プレビュー表示
			$url = $arrPageURL[$page] . "tpl=" . $arrPageTpl[$page] . ".tmp";
			$objPage->tpl_onload ="window.open('$url', 'preview');";
		}
	}	
	break;
case 'select':
	$page = $_POST['page'];
	if($arrPageTpl[$page] != "") {
		// ファイルの中身を読んで文字列に格納する
		$path = TEMPLATE_FTP_DIR . $arrPageTpl[$page];
		if(file_exists($path)) {
	 		$fp = fopen($path, "r");
			$contents = fread($fp, filesize($path));
			$objFormParam->setValue('template', $contents);
			fclose($fp);
		}
	} else {
		$objFormParam->setValue('template', "");
	}
	break;
default:
	
	break;
}

/*

// ファイルの中身を読んで文字列に格納する
$path = TEMPLATE_FTP_DIR . "index.tpl";
$fp = fopen($path, "r");
$contents = fread($fp, filesize($path));

$objFormParam->setValue('template', $contents);

fclose($fp);

*/

// 入力値の取得
$objPage->arrForm = $objFormParam->getFormParamList();

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//---------------------------------------------------------
/* パラメータ情報の初期化 */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("ページ選択", "page", INT_LEN, "n", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam("テンプレート", "template", LLTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
}

function lfWriteFile($path, $string) {
	$fp = fopen($path,"w+");
	flock($fp, LOCK_EX);
	$ret = fwrite($fp, $string);
	fclose($fp);
	return $ret;
}

?>
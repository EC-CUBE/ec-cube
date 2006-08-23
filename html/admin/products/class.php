<?php

require_once("../require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'products/class.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_subno = 'class';
		$this->tpl_subtitle = '規格登録';
		$this->tpl_mainno = 'products';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// 認証可否の判定
sfIsSuccess($objSess);

// 要求判定
switch($_POST['mode']) {
// 編集処理
case 'edit':
	// POST値の引き継ぎ
	$objPage->arrForm = $_POST;
	// 入力文字の変換
	$objPage->arrForm = lfConvertParam($objPage->arrForm);
	// エラーチェック
	$objPage->arrErr = lfErrorCheck();
	if(count($objPage->arrErr) <= 0) {
		if($_POST['class_id'] == "") {
			lfInsertClass();	// 新規作成
		} else {
			lfUpdateClass();	// 既存編集
		}
		// 再表示
		sfReload();
	} else {
		// POSTデータを引き継ぐ
		$objPage->tpl_class_id = $_POST['class_id'];
	}
	break;
// 削除
case 'delete':
	sfDeleteRankRecord("dtb_class", "class_id", $_POST['class_id'], "", true);
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_classcategory", "class_id = ?", $_POST['class_id']);
	// 再表示
	sfReload();
	break;
// 編集前処理
case 'pre_edit':
	// 編集項目をDBより取得する。
	$where = "class_id = ?";
	$class_name = $objQuery->get("dtb_class", "name", $where, array($_POST['class_id']));
	// 入力項目にカテゴリ名を入力する。
	$objPage->arrForm['name'] = $class_name;
	// POSTデータを引き継ぐ
	$objPage->tpl_class_id = $_POST['class_id'];
break;
case 'down':
	sfRankDown("dtb_class", "class_id", $_POST['class_id']);
	// 再表示
	sfReload();
	break;
case 'up':
	sfRankUp("dtb_class", "class_id", $_POST['class_id']);
	// 再表示
	sfReload();
	break;
default:
	break;
}

// 規格の読込
$where = "delete <> 1";
$objQuery->setorder("rank DESC");
$objPage->arrClass = $objQuery->select("name, class_id", "dtb_class", $where);
$objPage->arrClassCatCount = sfGetClassCatCount();

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------------

/* DBへの挿入 */
function lfInsertClass() {
	$objQuery = new SC_Query();
	// INSERTする値を作成する。
	$sqlval['name'] = $_POST['name'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['rank'] = $objQuery->max("dtb_class", "rank") + 1;
	// INSERTの実行
	$ret = $objQuery->insert("dtb_class", $sqlval);
	return $ret;
}

/* DBへの更新 */
function lfUpdateClass() {
	$objQuery = new SC_Query();
	// UPDATEする値を作成する。
	$sqlval['name'] = $_POST['name'];
	$sqlval['update_date'] = "Now()";
	$where = "class_id = ?";
	// UPDATEの実行
	$ret = $objQuery->update("dtb_class", $sqlval, $where, array($_POST['class_id']));
	return $ret;
}

/* 取得文字列の変換 */
function lfConvertParam($array) {
	// 文字変換
	$arrConvList['name'] = "KVa";

	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

/* 入力エラーチェック */
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	$objErr->doFunc(array("規格名", "name", STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	
	if(!isset($objErr->arrErr['name'])) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("class_id, name", "dtb_class", "delete = 0 AND name = ?", array($_POST['name']));
		// 編集中のレコード以外に同じ名称が存在する場合		
		if ($arrRet[0]['class_id'] != $_POST['class_id'] && $arrRet[0]['name'] == $_POST['name']) {
			$objErr->arrErr['name'] = "※ 既に同じ内容の登録が存在します。<br>";
		}
	}
	return $objErr->arrErr;
}
?>
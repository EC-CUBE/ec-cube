<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

class UC_Page {
	function UC_Page() {
		$this->tpl_mainpage = 'products/category.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'category';
		$this->tpl_onload = " fnSetFocus('category_name'); ";
		$this->tpl_subtitle = 'カテゴリー登録';
	}
}

$conn = new SC_DBConn();
$objPage = new UC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

// パラメータ管理クラス
$objFormParam = new SC_FormParam();

sfPrintR($objFormParam);

// パラメータ情報の初期化
lfInitParam();
// POST値の取得
$objFormParam->setParam($_POST);

// 通常時は親カテゴリを0に設定する。
$objPage->arrForm['parent_category_id'] = $_POST['parent_category_id'];

switch($_POST['mode']) {
case 'edit':
	$objFormParam->convParam();
	$arrRet =  $objFormParam->getHashArray();
	$objPage->arrErr = lfCheckError($arrRet);
	
	if(count($objPage->arrErr) == 0) {
		if($_POST['category_id'] == "") {
			$objQuery = new SC_Query();
			$count = $objQuery->count("dtb_category");
			if($count < CATEGORY_MAX) {			
				lfInsertCat($_POST['parent_category_id']);
			} else {
				print("カテゴリの登録最大数を超えました。");
			}
		} else {
			lfUpdateCat($_POST['category_id']);
		}
	} else {
		$objPage->arrForm = array_merge($objPage->arrForm, $objFormParam->getHashArray());
		$objPage->arrForm['category_id'] = $_POST['category_id'];
	}
	break;
case 'pre_edit':
	// 編集項目のカテゴリ名をDBより取得する。
	$oquery = new SC_Query();
	$where = "category_id = ?";
	$cat_name = $oquery->get("dtb_category", "category_name", $where, array($_POST['category_id']));
	// 入力項目にカテゴリ名を入力する。
	$objPage->arrForm['category_name'] = $cat_name;
	// POSTデータを引き継ぐ
	$objPage->arrForm['category_id'] = $_POST['category_id'];
	break;
case 'delete':
	$objQuery = new SC_Query();
	// 子カテゴリのチェック
	$where = "parent_category_id = ? AND del_flg = 0";
	$count = $objQuery->count("dtb_category", $where, array($_POST['category_id']));
	if($count != 0) {
		$objPage->arrErr['category_name'] = "※ 子カテゴリが存在するため削除できません。<br>";
	}
	// 登録商品のチェック
	$where = "category_id = ? AND del_flg = 0";
	$count = $objQuery->count("dtb_products", $where, array($_POST['category_id']));
	if($count != 0) {
		$objPage->arrErr['category_name'] = "※ カテゴリ内に商品が存在するため削除できません。<br>";
	}	
	
	if(!isset($objPage->arrErr['category_name'])) {
		// ランク付きレコードの削除(※処理負荷を考慮してレコードごと削除する。)
		sfDeleteRankRecord("dtb_category", "category_id", $_POST['category_id'], "", true);
	}
	break;
case 'up':
	$objQuery = new SC_Query();
	$objQuery->begin();
	$up_id = lfGetUpRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
	if($up_id != "") {
		// 上のグループのrankから減算する数
		$my_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
		// 自分のグループのrankに加算する数
		$up_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id);
		if($my_count > 0 && $up_count > 0) {
			// 自分のグループに加算
			lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id'], $up_count);
			// 上のグループから減算
			lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id, $my_count);
		}
	}
	$objQuery->commit();
	break;
case 'down':
	$objQuery = new SC_Query();
	$objQuery->begin();
	$down_id = lfGetDownRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
	if($down_id != "") {
		// 下のグループのrankに加算する数
		$my_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
		// 自分のグループのrankから減算する数
		$down_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id);
		if($my_count > 0 && $down_count > 0) {
			// 自分のグループから減算
			lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id, $my_count);
			// 下のグループに加算
			lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id'], $down_count);
		}
	}
	$objQuery->commit();
	break;
case 'tree':
	break;
default:
	$objPage->arrForm['parent_category_id'] = 0;
	break;
}

$objPage->arrList = lfGetCat($objPage->arrForm['parent_category_id']);
$objPage->arrTree = sfGetCatTree($objPage->arrForm['parent_category_id']);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

?>